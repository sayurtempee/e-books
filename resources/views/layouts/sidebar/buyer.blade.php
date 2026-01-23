<nav class="mt-6 space-y-1 px-4">
    <a href="{{ route('buyer.dashboard') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('buyer.dashboard') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>

    <a href="{{ route('buyer.orders.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('buyer.orders.index') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-journal-bookmark-fill"></i>
        <span>Koleksi Buku</span>
    </a>

    <a href="{{ route('buyer.carts.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('buyer.carts.index') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-basket-fill"></i>
        <span>Keranjang</span>
    </a>

    <a href="{{ route('buyer.orders.tracking') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('buyer.orders.tracking') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-truck"></i>
        <span>Lacak Paket</span>
    </a>

    <a href="{{ route('chat.index') }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->routeIs('chat.*') ? $activeClass : $inactiveClass }}">
        <i class="bi bi-chat-dots-fill"></i>
        <span>Pesan Chat</span>
    </a>
</nav>
