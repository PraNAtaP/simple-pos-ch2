@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
<h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4 max-w-md border border-green-200">
        {{ session('success') }}
    </div>
@endif

@if (!empty($importSummary))
    <div class="bg-blue-50 text-blue-700 p-3 rounded-md mb-4 max-w-md border border-blue-200 text-sm">
        <span class="font-semibold">Hasil Import Terakhir:</span> {{ $importSummary }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-50 text-red-700 p-3 rounded-md mb-4 max-w-md border border-red-200">
        @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
@endif

@if (isset($importErrors) && $importErrors->count() > 0)
    <div class="bg-red-50 text-red-700 p-4 rounded-md mb-4 max-w-md border border-red-200">
        <h2 class="font-semibold text-sm mb-2">Row Gagal Di-import ({{ $importErrors->count() }} baris):</h2>
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach ($importErrors as $error)
                <li><strong>Baris {{ $error->row_number }}:</strong> {{ $error->error_message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<p class="text-sm mb-3">
    Pilih metode import yang ingin digunakan.
</p>

<p class="text-xs text-slate-500 mb-3">
    Kolom yang dibutuhkan: name, category_id, price.
</p>

<div class="flex flex-col gap-6 max-w-md">

    <form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data">
        @csrf

        <h2 class="font-semibold mb-2">1. Import Manual (Queue)</h2>

        <input type="file" name="csv" accept=".csv"
            required class="block w-full border rounded-md px-3 py-2 text-sm mb-3">

        <button type="submit"
            class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm">
            Import Manual
        </button>
    </form>

    <div class="border-t pt-4">
        <form method="POST" action="{{ route('import.excel') }}" enctype="multipart/form-data">
            @csrf

            <h2 class="font-semibold mb-2">2. Import Laravel Excel</h2>

            <input type="file" name="csv" accept=".csv"
                required class="block w-full border rounded-md px-3 py-2 text-sm mb-3">

            <button type="submit"
                class="bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                Import Laravel Excel
            </button>
        </form>
    </div>

</div>
@endsection