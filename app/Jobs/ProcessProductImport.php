<?php

namespace App\Jobs;

use App\Models\ImportError;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProcessProductImport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public array $backoff = [10, 60];
    public int $timeout = 60;

    public function __construct(private string $csvPath) {}

    public function handle(): void
    {
        $lines = file(Storage::path($this->csvPath), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (! $lines) {
            Storage::delete($this->csvPath);
            return;
        }

        $rows = array_map('str_getcsv', $lines);
        $header = array_map(
            fn ($col) => trim((string) $col, " \t\n\r\0\x0B\xEF\xBB\xBF"),
            array_shift($rows)
        );

        $rowNumber = 2;
        $created = 0;
        $updated = 0;

        foreach ($rows as $row) {
            if (count($row) !== count($header)) {
                $message = 'Jumlah kolom tidak sesuai dengan header.';
                ImportError::create([
                    'row_number' => $rowNumber,
                    'error_message' => $message,
                ]);
                Log::warning("Import gagal pada baris {$rowNumber}: {$message}");
                $rowNumber++;
                continue;
            }

            $validator = Validator::make(array_combine($header, $row), [
                'name' => ['required', 'string'],
                'category_id' => ['required', 'exists:categories,id'],
                'price' => ['required', 'integer', 'min:0'],
            ]);

            if ($validator->fails()) {
                $message = implode(', ', $validator->errors()->all());
                ImportError::create([
                    'row_number' => $rowNumber,
                    'error_message' => $message,
                ]);
                Log::warning("Import gagal pada baris {$rowNumber}: {$message}");
                $rowNumber++;
                continue;
            }

            $data = $validator->validated();

            $product = Product::updateOrCreate(
                ['name' => $data['name'], 'category_id' => $data['category_id']],
                ['price' => $data['price']],
            );

            $product->wasRecentlyCreated ? $created++ : $updated++;
            $rowNumber++;
        }

        Cache::put('import_summary', "{$created} produk baru, {$updated} diperbarui.", now()->addDay());

        Storage::delete($this->csvPath);
    }
}
