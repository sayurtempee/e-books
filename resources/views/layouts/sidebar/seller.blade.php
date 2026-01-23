<nav class="mt-6 space-y-1 px-4">
    <a href="{{ route('seller.dashboard') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('seller.dashboard') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('seller.book.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('seller.book.index') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Produk Buku</span>
    </a>

    {{--  Buat Approval Dibagian ini, lalu index, edit, detail dan delete nya  --}}
    <a href="{{ route('seller.approval.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
    {{ request()->routeIs('seller.approval.index') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-check-square-fill"></i>
        <span>Produk Buku</span>
    </a>

    <a href="{{ route('seller.reports.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('seller.reports.*') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-graph-up-arrow"></i>
        <span>Laporan Penjualan</span>
    </a>
</nav>
