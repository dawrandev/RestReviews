<div class="sidebar-wrapper">
    <div>
        <div class="logo-wrapper">
            <a href="{{ route('login') }}">
                <img class="img-fluid for-light" src="../assets/images/logo/logo.png" alt="">
                <img class="img-fluid for-dark" src="../assets/images/logo/logo_dark.png" alt="">
            </a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
        </div>
        <div class="logo-icon-wrapper">
            <a href="{{ route('login') }}"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a>
        </div>
        <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            <div id="sidebar-menu">
                <ul class="sidebar-links" id="simple-bar">
                    <li class="back-btn">
                        <a href="{{ route('login') }}"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a>
                        <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                    </li>

                    @php
                    $user = auth()->user();
                    $isSuperAdmin = $user->hasRole('superadmin');
                    // Restorani borligini tekshirish (hasMany bo'lsa ham, hasOne bo'lsa ham ishlaydi)
                    $hasRestaurant = $user->restaurants()->exists();
                    @endphp

                    {{-- 1. Dashboard: Faqat superadmin yoki kamida 1ta restorani bor adminlar uchun --}}
                    @if($isSuperAdmin || $hasRestaurant)
                    @php $dashboardRoute = $isSuperAdmin ? 'superadmin.dashboard' : 'admin.dashboard'; @endphp
                    <li class="sidebar-list {{ request()->routeIs($dashboardRoute) ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route($dashboardRoute) }}">
                            <i data-feather="monitor"></i><span>Панель управления</span>
                        </a>
                    </li>
                    @endif

                    {{-- 2. Administratorlar (Faqat Superadmin ko'radi) --}}
                    @if($isSuperAdmin)
                    <li class="sidebar-list {{ request()->routeIs('users.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('users.index') }}">
                            <i data-feather="users"></i><span>Администраторы</span>
                        </a>
                    </li>
                    @endif

                    {{-- 3. Restoranlar bo'limi (Superadmin barcha restaranlarni ko'radi, Admin o'z restaranlarini ko'radi) --}}
                    @if($isSuperAdmin || $user->hasRole('admin'))
                    <li class="sidebar-list {{ request()->routeIs('restaurants.*') ? 'active' : '' }}">
                        @php
                        // Agar admin bo'lsa va hali birorta ham restorani bo'lmasa
                        $showCreateFirst = $user->hasRole('admin') && !$hasRestaurant;
                        $resRoute = $showCreateFirst ? route('restaurants.index', ['create' => 1]) : route('restaurants.index');
                        $resLabel = $isSuperAdmin ? 'Рестораны' : ($showCreateFirst ? 'Создать ресторан':'Мои рестораны');
                        @endphp
                        <a class="sidebar-link sidebar-title link-nav" href="{{ $resRoute }}">
                            <i data-feather="home"></i>
                            <span>{{$resLabel }}</span>
                        </a>
                    </li>
                    @endif

                    {{-- 4. Kataloglar (Faqat Superadmin ko'radi) --}}
                    @if($isSuperAdmin)
                    <li class="sidebar-list {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('categories.index') }}">
                            <i data-feather="folder"></i><span>Категории</span>
                        </a>
                    </li>

                    <li class="sidebar-list {{ request()->routeIs('brands.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('brands.index') }}">
                            <i data-feather="tag"></i><span>Бренды</span>
                        </a>
                    </li>

                    <li class="sidebar-list {{ request()->routeIs('cities.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('cities.index') }}">
                            <i data-feather="map-pin"></i><span>Города</span>
                        </a>
                    </li>
                    @endif

                    <li class="sidebar-list {{ request()->routeIs('cities.*') ? 'active' : '' }}">
                        <a class="sidebar-link sidebar-title link-nav" href="{{ route('cities.index') }}">
                            <i data-feather="book-open"></i><span>Меню</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
        </nav>
    </div>
</div>