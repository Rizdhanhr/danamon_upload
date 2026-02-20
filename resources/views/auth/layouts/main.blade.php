<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta19
* @link https://tabler.io
* Copyright 2018-2023 The Tabler Authors
* Copyright 2018-2023 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- CSS files -->
    <link href="{{ asset('template') }}/dist/css/tabler.min.css?1684106062" rel="stylesheet" />
    <link href="{{ asset('template') }}/dist/css/tabler-flags.min.css?1684106062" rel="stylesheet" />
    <link href="{{ asset('template') }}/dist/css/tabler-payments.min.css?1684106062" rel="stylesheet" />
    <link href="{{ asset('template') }}/dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet" />
    <link href="{{ asset('template') }}/dist/css/demo.min.css?1684106062" rel="stylesheet" />
    <script src="https://www.google.com/recaptcha/enterprise.js" async defer></script>
    <style>
        @import url('https://rsms.me/inter/inter.css');

        :root {
            --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }

        body {
            font-feature-settings: "cv03", "cv04", "cv11";
        }
    </style>
    @stack('css')
</head>

<body class=" d-flex flex-column">
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script src="{{ asset('template') }}/dist/js/demo-theme.min.js?1684106062"></script>
    <script src="{{ asset('template') }}/dist/js/tabler.min.js?1684106062" defer></script>
    <script src="{{ asset('template') }}/dist/js/demo.min.js?1684106062" defer></script>
    @stack('js')
</body>

</html>
