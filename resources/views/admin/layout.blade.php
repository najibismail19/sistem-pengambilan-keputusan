<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Chart - srtdash</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/icon/favicon.ico') }}">

    <!-- Bootstrap, Font Awesome, dan CSS lainnya -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/metisMenu.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/slicknav.min.css') }}">

    <!-- amchart css -->
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />

    <!-- others css -->
    <link rel="stylesheet" href="{{ asset('assets/css/typography.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/default-css.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    {{-- Sweet Alert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css"> --}}

    <!-- Modernizr js -->
    <script src="{{ asset('assets/js/vendor/modernizr-2.8.3.min.js') }}"></script>

    <!-- jQuery (pastikan jQuery dimuat terlebih dahulu) -->
<!-- Correct order -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

    <!-- DataTables JS (hanya sekali) -->
    {{-- <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script> --}}

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Styling untuk SweetAlert */
        
    .swal2-input {
        width: 100%; /* Lebar input menyesuaikan kontainer */
        padding: 10px; /* Padding untuk memperbesar ruang input */
        margin: 10px 0; /* Jarak antar elemen */
        border: 1px solid #ccc; /* Border abu-abu untuk input */
        border-radius: 5px; /* Sudut input lebih lembut */
        font-size: 16px; /* Ukuran font yang lebih besar */
    }

    .swal2-select {
        width: 100%; /* Lebar select menyesuaikan kontainer */
        padding: 10px; /* Padding untuk memperbesar ruang select */
        margin: 10px 0; /* Jarak antar elemen */
        border: 1px solid #ccc; /* Border abu-abu untuk select */
        border-radius: 5px; /* Sudut select lebih lembut */
        font-size: 16px; /* Ukuran font yang lebih besar */
    }

    /* Membuat tombol konfirmasi lebih menonjol */
    .swal2-confirm {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
    }

    .swal2-cancel {
        background-color: #ccc;
        color: black;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 16px;
    }
    
    </style>
</head>

<body>
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <!-- Preloader area start -->
    <div id="preloader">
        <div class="loader"></div>
    </div>
    <!-- Preloader area end -->

    <!-- Page container area start -->
    <div class="page-container">
        <!-- Sidebar menu area start -->
        @include('admin.partials.sidebar') 
        <!-- Sidebar menu area end -->

        <!-- Main content area start -->
        <div class="main-content">
            <!-- Header area start -->
            @include('admin.partials.header') 
            <!-- Header area end -->

            <!-- Page title area start -->
            <div class="page-title-area">
                <div class="row align-items-center">
                    <div class="col-sm-6">
                        <div class="breadcrumbs-area clearfix">
                            <h4 class="page-title pull-left">Dashboard</h4>
                            <ul class="breadcrumbs pull-left">
                                <li><a href="index.html">Home</a></li>
                                <li><span>Line Chart</span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-6 clearfix">
                        <div class="user-profile pull-right">
                            <img class="avatar user-thumb" src="assets/images/author/avatar.png" alt="avatar">
                            <h4 class="user-name dropdown-toggle" data-toggle="dropdown">Kumkum Rai <i class="fa fa-angle-down"></i></h4>
                            <div class="dropdown-menu">
                                <a class="dropdown-item" href="#">Message</a>
                                <a class="dropdown-item" href="#">Settings</a>
                                
                                <!-- Form logout -->
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                    @csrf
                                </form>
                                <a class="dropdown-item" href="#" onclick="document.getElementById('logout-form').submit();">Log Out</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Page title area end -->

            <!-- Main content inner area start -->
            <div class="main-content-inner">
                @yield('content')
            </div>
            <!-- Main content inner area end -->
        </div>
        <!-- Main content area end -->

        <!-- Footer area start -->
        <footer>
            <div class="footer-area">
                <p>© Copyright 2018. All right reserved. Template by <a href="https://colorlib.com/wp/">Colorlib</a>.</p>
            </div>
        </footer>
        <!-- Footer area end -->
    </div>
    <!-- Page container area end -->

    <!-- Offset area start -->
    <div class="offset-area">
        <div class="offset-close"><i class="ti-close"></i></div>
        <ul class="nav offset-menu-tab">
            <li><a class="active" data-toggle="tab" href="#activity">Activity</a></li>
            <li><a data-toggle="tab" href="#settings">Settings</a></li>
        </ul>
        <div class="offset-content tab-content">
            <div id="activity" class="tab-pane fade in show active">
                <!-- Activity content -->
            </div>
            <div id="settings" class="tab-pane fade">
                <!-- Settings content -->
            </div>
        </div>
    </div>
    <!-- Offset area end -->

    <!-- Additional JS libraries (optional) -->
    {{-- <script src="{{ asset('assets/js/vendor/jquery-2.2.4.min.js') }}"></script> --}}
    <script src="{{ asset('assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('assets/js/metisMenu.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/js/jquery.slicknav.min.js') }}"></script>

    <!-- Chart.js, Highcharts, ZingChart, AmChart, etc. -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://cdn.zingchart.com/zingchart.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>

    <!-- Custom script for charts and plugins -->
    <script src="{{ asset('assets/js/line-chart.js') }}"></script>
    <script src="{{ asset('assets/js/bar-chart.js') }}"></script>
    <script src="{{ asset('assets/js/pie-chart.js') }}"></script>
    <script src="{{ asset('assets/js/plugins.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>

    @stack('scripts')
</body>

</html>
