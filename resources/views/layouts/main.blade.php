<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.head')

    @stack('css')
</head>

<body>
    <div class="tap-top"><i data-feather="chevrons-up"></i></div>
    <div class="page-wrapper compact-wrapper" id="pageWrapper">
        @include('components.header')

        <!-- Page Body Start-->
        <div class="page-body-wrapper">
            @include('components.sidebar')
            <div class="page-body">
                @yield('content')
            </div>
            @include('components.footer')
        </div>

    </div>
    @include('partials.script')

    @stack('scripts')

    @include('components.sweetalert-dynamic')
    @include('components.alerts')
</body>

</html>