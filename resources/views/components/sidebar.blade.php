  <!-- Page Sidebar Start-->
  <div class="sidebar-wrapper">
      <div>
          <div class="logo-wrapper"><a href="index.html"><img class="img-fluid for-light" src="../assets/images/logo/logo.png" alt=""><img class="img-fluid for-dark" src="../assets/images/logo/logo_dark.png" alt=""></a>
              <div class="back-btn"><i class="fa fa-angle-left"></i></div>
              <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
          </div>
          <div class="logo-icon-wrapper"><a href="index.html"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a></div>
          <nav class="sidebar-main">
              <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
              <div id="sidebar-menu">
                  <ul class="sidebar-links" id="simple-bar">
                      <li class="back-btn">
                          <a href="{{ route('login') }}"><img class="img-fluid" src="../assets/images/logo/logo-icon.png" alt=""></a>
                          <div class="mobile-back text-end"><span>Back</span><i class="fa fa-angle-right ps-2" aria-hidden="true"></i></div>
                      </li>

                      {{-- 1. Dashboard Faqat restorani bor adminlar va superadminlar uchun --}}
                      @if(auth()->user()->hasRole('superadmin') || (auth()->user()->hasRole('admin') && auth()->user()->restaurant))
                      <li class="sidebar-list">
                          <label class="badge badge-success">2</label>
                          <a class="sidebar-link sidebar-title" href="#">
                              <i data-feather="monitor"></i><span class="lan-3">Dashboard</span>
                          </a>
                          <ul class="sidebar-submenu">
                              <li><a class="lan-4" href="#">Default</a></li>
                              <li><a class="lan-5" href="#">Ecommerce</a></li>
                          </ul>
                      </li>
                      @endif

                      {{-- 2. Superadmin uchun foydalanuvchilar boshqaruvi --}}
                      @role('superadmin')
                      <li class="sidebar-list">
                          <a class="sidebar-link sidebar-title link-nav" href="{{ route('users.index') }}">
                              <i data-feather="users"></i><span>Администраторы</span>
                          </a>
                      </li>
                      @endrole

                      {{-- 3. Restoranlar menyusi --}}
                      {{-- Agar admin bo'lsa va restorani bo'lmasa, majburiy parametr bilan yuboramiz --}}
                      <li class="sidebar-list">
                          <a class="sidebar-link sidebar-title link-nav"
                              href="{{ (auth()->user()->hasRole('admin') && !auth()->user()->restaurant) ? route('restaurants.index', ['create' => 1]) : route('restaurants.index') }}">
                              <i data-feather="home"></i>
                              <span>
                                  {{ (auth()->user()->hasRole('admin') && !auth()->user()->restaurant) ? 'Создать ресторан' : 'Рестораны' }}
                              </span>
                          </a>
                      </li>
                  </ul>
              </div>
              <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
          </nav>
      </div>
  </div>
  <!-- Page Sidebar Ends-->