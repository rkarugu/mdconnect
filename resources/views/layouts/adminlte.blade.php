<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">

    <!-- Optional: Add additional styles if needed -->
    @stack('styles') <!-- To add specific styles per page -->
    
    <!-- Ensure TailwindCSS is included to support modern layouts -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">  <!-- TailwindCSS (if needed) -->

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('admin.main-navigation')

        <!-- Main Sidebar -->
        @include('admin.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            <div class="content-header bg-white shadow-sm">
                <div class="container-fluid">
                    @section('content_header')
                        <h1 class="m-0">Dashboard</h1>
                    @show
                </div>
            </div>

            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
        </div>

        <!-- Footer -->
        @include('admin.footer')

    </div>

    <!-- AdminLTE Scripts -->
    <script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>

    <!-- Optional: Add custom JS specific to a page -->
    @stack('scripts')

</body>

</html>
