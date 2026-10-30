@extends('layouts.app')

@section('title', 'Import Produk')

@section('content')
<h1 class="text-lg font-semibold mb-4">Import Produk dari CSV</h1>

@if (session('success'))
    <div class="bg-green-50 text-green-700 p-3 rounded-md mb-4 max-w-md border border-green-200">
        {{ session('success') }}
    </div>
@endif

@if (isset($importErrors) && $importErrors->count() > 0)
    <div class="bg-red-50 text-red-700 p-4 rounded-md mb-4 max-w-md border border-red-200">
        <h2 class="font-semibold text-sm mb-2">Gagal Di-import (Log Error):</h2>
        <ul class="list-disc pl-5 text-sm space-y-1">
            @foreach ($importErrors as $error)
                <li>Baris {{ $error->row_number }}: {{ $error->error_message }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('import.store') }}" enctype="multipart/form-data" class="max-w-md">
    @csrf
    <label class="block mb-4">
        <span class="text-sm font-medium">File CSV</span>
        <input type="file" name="csv" accept=".csv" class="mt-1 block w-full border rounded-md px-3 py-2 text-sm">
        @error('csv')
            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </label>
    
    <p class="text-xs text-slate-500 mb-3">Column yang dibutuhkan: <code>name</code>, <code>category_id</code>, <code>price</code>.</p>
    
    <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded-md text-sm hover:bg-slate-800">Import</button>
</form>
@endsection