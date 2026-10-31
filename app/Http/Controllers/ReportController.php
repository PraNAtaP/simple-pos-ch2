<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Exports\SalesReportExport;
use App\Jobs\ProcessProductImport;
use App\Models\ImportError; // Tambahan model error
use App\Imports\ProductsExcelImport;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use App\Models\TransactionDetail;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to] = $this->resolveDateRange($request);
        $query = $this->filteredQuery($from, $to);

        return view('reports.index', [
            'totalPenjualan' => $query->sum('total'),
            'jumlahTransaksi' => $query->count(),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolveDateRange($request);
        $query = $this->filteredQuery($from, $to);

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['ID', 'Tanggal', 'Kasir', 'Total']);

            foreach ($query->lazyById() as $t) {
                fputcsv($out, [
                    $t->id,
                    $t->created_at,
                    $t->user->name,
                    $t->total
                ]);
            }

            fclose($out);
        }, 'laporan-penjualan.csv');
    }

    public function exportExcel(Request $request): BinaryFileResponse
    {
        [$from, $to] = $this->resolveDateRange($request);

        return Excel::download(
            new SalesReportExport($this->filteredQuery($from, $to)),
            'laporan-penjualan.xlsx',
        );
    }

    public function exportPdf(Request $request): Response
    {
        [$from, $to] = $this->resolveDateRange($request);

        $topProducts = TransactionDetail::whereHas('transaction', function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->selectRaw('product_id, SUM(qty) as total_terjual')
            ->groupBy('product_id')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->with('product') 
            ->get();

        $pdf = Pdf::loadView('reports.pdf', [
            'totalPenjualan' => $this->filteredQuery($from, $to)->sum('total'),
            'jumlahTransaksi' => $this->filteredQuery($from, $to)->count(),
            'transactions' => $this->filteredQuery($from, $to)->latest()->limit(50)->get(),
            'topProducts' => $topProducts, // <--- Kirim variabel baru ke view
            'from' => $from->toDateTime(),
            'to' => $to->toDateTime(),
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    public function importForm(): View
    {
        $importErrors = ImportError::orderBy('row_number')->get();
        $importSummary = Cache::get('import_summary');

        return view('reports.import', compact('importErrors', 'importSummary'));
    }

    // Import manual menggunakan Queue
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        ImportError::truncate();
        Cache::forget('import_summary');

        $path = $request->file('csv')->store('imports');
        ProcessProductImport::dispatch($path);

        return back()->with(
            'success',
            'Import sedang diproses di background. Refresh halaman ini beberapa saat lagi untuk melihat hasil dan log error (jika ada).'
        );
    }

    // Import menggunakan Laravel Excel
    public function importExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        ImportError::truncate();
        Cache::forget('import_summary');

        $import = new ProductsExcelImport;
        Excel::import(
            $import,
            $request->file('csv')
        );

        $summary = "{$import->created} produk baru, {$import->updated} diperbarui.";
        Cache::put('import_summary', $summary, now()->addDay());

        return back()->with(
            'success',
            "Import menggunakan Laravel Excel selesai: {$summary}"
        );
    }

    private function resolveDateRange(Request $request): array
    {
        $from = $request->date('from')?->startOfDay() ?? now()->startOfMonth();
        $to = $request->date('to')?->endOfDay() ?? now()->endOfDay();

        return [$from, $to];
    }

    private function filteredQuery($from, $to): Builder
    {
        return Transaction::with('user')
            ->whereBetween('created_at', [$from, $to]);
    }
}