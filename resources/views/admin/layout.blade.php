<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>

    <!-- Optional: Add additional styles if needed -->
    @stack('styles') <!-- To add specific styles per page -->
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Navbar -->
        @include('layouts.navigation')

        <!-- Main Sidebar -->
        @include('admin.sidebar')

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @section('content_header')
                <h1>Dashboard</h1>
            @show

            <!-- Main content -->
            <section class="content">
                @yield('content')
            </section>
        </div>

        <!-- Footer -->
        @include('admin.footer')

    </div>

   
    <!-- Optional: Add custom JS specific to a page -->
    @stack('scripts')

</body>

</html>
