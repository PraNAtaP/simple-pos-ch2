<?php

namespace App\Imports;

use App\Models\ImportError;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsExcelImport implements ToModel, WithHeadingRow, WithChunkReading
{
    public int $created = 0;
    public int $updated = 0;
    private int $rowNumber = 1;

    public function model(array $row)
    {
        $this->rowNumber++;

        $validator = Validator::make($row, [
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            $message = implode(', ', $validator->errors()->all());
            ImportError::create([
                'row_number' => $this->rowNumber,
                'error_message' => $message,
            ]);
            Log::warning("Import Excel gagal pada baris {$this->rowNumber}: {$message}");

            return null;
        }

        $data = $validator->validated();

        $product = Product::updateOrCreate(
            [
                'name' => $data['name'],
                'category_id' => $data['category_id'],
            ],
            [
                'price' => $data['price'],
            ]
        );

        $product->wasRecentlyCreated ? $this->created++ : $this->updated++;

        return $product;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}