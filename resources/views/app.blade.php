<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="{{ asset('assets/img/logo/logo.png') }}" rel="icon">
    <title>{{ config('app.name') }} - App</title>
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/faizaan-admin.css') }}" rel="stylesheet">
    <style>
        .text-success {
            color: #67C23A !important;
        }

        .text-danger {
            color: #F56C6C !important;
        }

        .bg-success {
            background-color: #67C23A !important;
            color: white;
        }

        .my-bg-success {
            background-color: #67C23A !important;
            color: white !important;
        }

        .my-btn-success {
            background-color: #67C23A !important;
            color: #FFFFFF !important;
        }

        .my-btn-primary {
            background-color: #974EC3 !important;
            color: #FFFFFF !important;
        }

        .my-bg-primary {
            background-color: #974EC3 !important;
            color: #FFFFFF !important;
        }
        .my-text-primary{
            color:#974EC3;
        }

        .my-btn-danger {
            background-color: #F56C6C !important;
            color: #FFFFFF !important;
        }
        .my-text-danger {
            color: #F56C6C !important;
        }

        .my-text-success {
            background-color: #67C23A !important;
            color: #67C23A !important;
        }

        .bg-danger {
            background-color: #F56C6C !important;
            color: black;
        }

        .input-group .btn {
            position: relative;
            z-index: 1 !important;
        }

        .sidebar .nav-item .nav-link-layer-two[data-toggle="collapse"]::after {
            width: 1rem;
            text-align: center;
            float: right;
            vertical-align: 0;
            border: 0;
            font-weight: 900;
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
        }

        .sidebar .nav-item .nav-link-layer-two[data-toggle="collapse"].collapsed::after {
            content: '\f105';
        }
        .reset {
            all: revert;
            border:1px solid #CCC;
        }
    </style>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body id="page-top">
    <div id="app" class="">

    </div>
    <!-- Scroll to top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <script src="{{ asset('assets/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
    <script src="{{ asset('assets/js/faizaan-admin.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/demo/chart-area-demo.js') }}"></script> --}}
   
{{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}
   <script>    
    // creates multiple instances
    // const flatpickr = require("flatpickr");
// flatpickr(".datecalender");    
        window.publicPath = "{{ asset('') }}";
        window.env = @json([
            'APP_NAME' => config('app.name'),
            'ANOTHER_VAR' => env('ANOTHER_VAR'),
            // Add more variables as needed
        ]);
    </script>
</body>

</html>
