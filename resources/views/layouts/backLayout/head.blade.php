<head>
    <base href="../../../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ asset('frontAssets/textiles_logo_200.png') }}">
    <!-- Page Title  -->
    <title>@yield('title', 'CSMS')</title>
    <!-- StyleSheets  -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/dashlite.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('backAssets/css/theme_override.css') }}"> --}}
    <link id="skin-default" rel="stylesheet" href="{{ asset('backAssets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('backAssets/css/custom-animations.css') }}">
    <script src="{{ asset('backAssets/js/jquery.js') }}"></script>

    <style>
        #global-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(189, 180, 180, 0.9);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader {
            position: relative;
            width: 80px;
            height: 80px;
            animation: rotate 1.5s linear infinite;
        }

        .dot {
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
        }

        .dot1 {
            background: #f52601;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .dot2 {
            background: #3F41D1;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        @keyframes rotate {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .loader-text {
            margin-top: 18px;
            font-size: 18px;
            font-weight: 600;
            color: #3F41D1;
            letter-spacing: 1px;
            opacity: 0.8;
        }
    </style>

</head>
