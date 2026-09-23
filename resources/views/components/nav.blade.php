<!-- <div> -->
    <!-- Simplicity is the consequence of refined emotions. - Jean D'Alembert -->
<!-- </div> -->

<nav class="bg-slate-900 text-white px-4 py-3 flex items-center gap-6">
    <span class="font-semibold">Simple POS</span>
    <a href="{{ route('pos.create') }}"
       class="{{ request()->routeIs('pos.create') ? 'font-semibold underline' : 'hover:underline' }}">
        Kasir
    </a>
    <a href="{{ route('transactions.index') }}"
       class="{{ request()->routeIs('transactions.index') ? 'font-semibold underline' : 'hover:underline' }}">
        Transaksi
    </a>
    <a href="{{ route('products.index') }}" class="hover:underline">Produk</a>
</nav>