
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
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Sign in - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="{{ asset('template') }}/dist/css/tabler.min.css?1684106062" rel="stylesheet"/>
    <link href="{{ asset('template') }}/dist/css/tabler-flags.min.css?1684106062" rel="stylesheet"/>
    <link href="{{ asset('template') }}/dist/css/tabler-payments.min.css?1684106062" rel="stylesheet"/>
    <link href="{{ asset('template') }}/dist/css/tabler-vendors.min.css?1684106062" rel="stylesheet"/>
    <link href="{{ asset('template') }}/dist/css/demo.min.css?1684106062" rel="stylesheet"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
    </style>
  </head>
  <body  class=" d-flex flex-column">
    <script src="{{ asset('template') }}/dist/js/demo-theme.min.js?1684106062"></script>
    <div class="page page-center">
      <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ asset('template') }}/static/logo.svg" height="36" alt=""></a>
        </div>
        <div class="card card-md">
          <div class="card-body">
            <h2 class="h2 text-center mb-4">Login to your account</h2>
            <form action="{{ route('login.post') }}" method="POST" autocomplete="off" novalidate>
                @csrf
              <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" name="email" placeholder="your@email.com" autocomplete="off">
                <span style="color:red;">@error('email') {{ $message }} @enderror</span>
              </div>
              <div class="mb-3">
                <label class="form-label">
                  Password
                </label>
                <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"  placeholder="Your password"  autocomplete="off">
                <span style="color:red;">@error('password') {{ $message }} @enderror</span>
              </div>
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100">Sign in</button>
              </div>
            </form>
          </div>
          <div class="hr-text">or</div>
        </div>
        <div class="text-center text-muted mt-3">
          Don't have account yet? <a href="" tabindex="-1">Sign up</a>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <!-- Tabler Core -->
    <script src="{{ asset('template') }}/dist/js/tabler.min.js?1684106062" defer></script>
    <script src="{{ asset('template') }}/dist/js/demo.min.js?1684106062" defer></script>
  </body>
</html>
