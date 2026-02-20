@extends('auth.layouts.main')
@section('title', 'Login')
@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img
                        src="{{ asset('template') }}/images/logo_more_voucher.png" width="120" height="50"
                        alt=""></a>
            </div>
            <div class="card card-md">
                <div class="card-body">
                    <h2 class="h2 text-center mb-4">Login to your account</h2>
                    <div class="alert  alert-danger" style="display:none;" id="alert" role="alert">

                    </div>
                    <form id="formLogin">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Username</label>
                            <input type="text" name="username" placeholder="user@mail.com" value=""
                                class="form-control validate" id="username">
                            <span class="validation formErrors-username" style="color: red;"></span>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control validate" id="password">
                            <span class="validation formErrors-password" style="color: red;"></span>
                        </div>
                        <div class="mb-3 d-flex justify-content-center">
                            <div class="g-recaptcha" data-sitekey="{{ $site_key }}" data-action="LOGIN"></div>
                        </div>
                        <div class="form-footer">
                            <button type="button" id="btnSendOtp" onclick="sendOtp()"
                                class="btn btn-primary w-100">Login</button>
                        </div>
                    </form>
                    <div id="afterOtp" style="display: none;">
                        <div class="mb-3">
                            <div class="otpDiv" id="otpDiv">

                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputOtp" class="form-label">One Time Password (OTP)</label>
                            <input type="text" name="otp" class="form-control validate" id="otp">
                            <span class="validation formErrors-otp" style="color: red;"></span>
                        </div>
                        <div class="mb-3">
                            <button type="button" id="btnVerify" onclick="verifyOtp()"
                                class="btn btn-primary  w-100">Verify</button>
                            <a style="color:gray" class="card-title card-title-lg text-center mt-2" onclick="resendOtp()"
                                id='btnResend' disabled>Resend OTP (<span id="timerCount"></span>)</a>
                        </div>
                    </div>
                </div>
                {{-- <div class="hr-text">or</div> --}}
            </div>
            {{-- <div class="text-center text-muted mt-3">
                Don't have account yet? <a href="" tabindex="-1">Sign up</a>
            </div> --}}
        </div>
    </div>
@endsection
@push('js')
    <script>
        var username = $("#username");
        var password = $("#password");
        var beforeOtp = $("#formLogin");
        var otpInput = $("#otpInput");
        var afterOtp = $("#afterOtp");
        var otpMessage = $("#otpDiv");
        var btnSend = $("#btnSendOtp");
        var btnVerify = $("#btnVerify");
        var alert = $("#alert");
        var spn = $("#timerCount");
        var btn = $("#btnResend");
        var count = 60;
        var timer = null;
        var cooldown = true;
        var icons = '<i class="bi bi-exclamation-circle-fill"></i>';

        function countDownTime() {
            spn.text(count);
            btn.css("color", "gray");
            if (count !== 0) {
                timer = setTimeout(countDownTime, 1000);
                count--; // decrease the timer
            } else {
                btn.css("color", "blue").prop("disabled", false);
                cooldown = false;
            }
        }


        function ajaxOtp(successCallback, errorCallback) {
            return $.ajax({
                method: "POST",
                url: "{{ route('login.post') }}",
                data: {
                    username: username.val(),
                    password: password.val()
                },
                success: successCallback,
                error: errorCallback,
            });
        }


        function verifyRecaptcha(successCallback, errorCallback) {
            let token = grecaptcha.enterprise.getResponse();
            return $.ajax({
                method: "POST",
                url: "{{ route('login.recaptcha') }}",
                data: {
                    token: token
                },
                success: successCallback,
                error: errorCallback,
            });
        }

        function sendOtp() {
            btnSend.prop("disabled", true);
            $('.validation').html('');
            $('.validate').removeClass('is-invalid');
            alert.hide().html("");
            verifyRecaptcha(
                function(success) {
                    grecaptcha.enterprise.reset();
                    ajaxOtp(
                        function(result) {
                            beforeOtp.hide();
                            afterOtp.show();
                            otpMessage.html(`
                                <h2 class="h3 text-center mb-3" id="otpAfterText">ENTER OTP</h2>
                                <p class="my-4 text-center" id="otpText">
                                    We've just sent OTP to <strong>${username.val()}</strong>
                                </p>
                            `);
                            countDownTime();
                        },
                        function(error) {
                            btnSend.prop("disabled", false);
                            if (error.status == 422) {
                                var formError = error.responseJSON.errors;
                                $.each(formError, function(field_name, errorMsg) {
                                    const id = field_name.replaceAll(".", "");
                                    $("#" + id).addClass("is-invalid");
                                    $(".formErrors-" + id).html(errorMsg);
                                });

                            } else if (error.status == 401 || error.status == 403) {
                                alert.show().html(icons + ' ' + error.responseJSON.error_message);
                            } else {
                                alert.show().html(icons + ' ' + error.responseJSON.error_message);
                            }
                        }
                    );
                },
                function(errors) {
                    btnSend.prop("disabled", false);
                    if (errors.status == 403) {
                        alert.show().html(icons + ' ' + errors.responseJSON.error_message);
                    }
                }
            );
        }

        function resendOtp() {
            if (!cooldown) {
                alert.hide().html("");
                cooldown = true;
                $("#otp").val("");
                count = 60;
                ajaxOtp(
                    function(result) {
                        countDownTime();
                    },
                    function(error) {
                        btnSend.prop("disabled", false);
                        cooldown = false;
                        alert.show().html(icons + ' ' + error.responseJSON.error_message);
                    }
                );
            }
        }

        function verifyOtp() {
            btnVerify.prop("disabled", true);
            alert.hide().html("");
            $('.validation').html('');
            $('.validate').removeClass('is-invalid');
            $('.removeval').removeAttr('style');
            let finalOtp = $("#otp").val();
            let data = {
                otp: finalOtp,
                username: username.val()
            };
            $.ajax({
                method: "POST",
                url: "{{ route('login.otp') }}",
                data: data,
                success: function(result) {
                    window.location.href = "{{ route('dashboard.index') }}";
                },
                error: function(error) {
                    btnVerify.prop("disabled", false);
                    if (error.status === 422) {
                        var formError = error.responseJSON.errors;
                        $.each(formError, function(field_name, errorMsg) {
                            const id = field_name.replaceAll(".", "");
                            $("#" + id).addClass("is-invalid");
                            $(".formErrors-" + id).html(errorMsg);
                        });
                    } else if (error.status === 500) {
                        alert.show().html(`${icons} Error, Please contact administrator !`);
                    } else if (error.status === 404) {
                        alert.show().html(`${icons} Wrong OTP !`);
                    } else if (error.status === 403) {
                        alert.show().html(icons + ' ' + error.responseJSON.error_message);
                    }
                }
            });
        }
    </script>
@endpush
