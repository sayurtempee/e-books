<nav class="mt-6 space-y-1 px-4">
    <a href="{{ route('admin.dashboard') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('admin.dashboard') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('admin.sellers') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('admin.sellers') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-shop"></i>
        <span>Seller</span>
    </a>

    <a href="{{ route('admin.buyers') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('admin.buyers') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-people"></i>
        <span>Buyer</span>
    </a>

    <a href="{{ route('admin.categories') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('admin.categories') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-book"></i>
        <span>Categories</span>
    </a>
</nav>
