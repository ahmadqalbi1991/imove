<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>{{env('APP_NAME')}} | Password Reset </title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('') }}admin-assets/assets/img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('') }}admin-assets/assets/img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('') }}admin-assets/assets/img/favicon/favicon-16x16.png">
    <link rel="manifest" href="{{ asset('') }}admin-assets/assets/img/favicon/site.webmanifest">
    <link rel="mask-icon" href="{{ asset('') }}admin-assets/assets/img/favicon/safari-pinned-tab.svg" color="#ac772b">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <!-- <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700' rel='stylesheet' type='text/css'> -->

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('admin-assets/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/assets/css/plugins.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin-assets/assets/css/users/login-3.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('admin-assets/plugins/jqvalidation/custom-jqBootstrapValidation.css') }}">
    <link href="{{ asset('admin-assets/plugins/notification/toastr/toastr.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" integrity="sha512-1sCRPdkRXhBV2PBLUdRb4tMg1w2YPf37qatUFeS7zlBy7jJI8Lf4VHwWfZZfpXtYSLy85pkm9GaYVYMfw5BC1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
    <style type="text/css">
        .invalid-feedback{
            color: red;
            display: block;
        }
    </style>
    <style>
        .toast-success{
            background-color:green !important;
        }
    </style>
    <style>
        .toast-danger{
            background-color:red !important;
        }
    </style>
</head>
<body class="login" style="font-family: 'Quicksand', sans-serif; background: url('{{ asset('') }}admin-assets/assets/img/bg-1920x1080.jpg'); background-size: cover; background-position: center; background-repeat: no-repeat;">

    <form method="POST" id="login-form" class="form-login" action="">
        @csrf
        </div>
        <input type="hidden" name="admin" value="1">
        <div class="row">
            <div class="col-md-12 text-center mb-4">
                <img alt="logo" src="{{ asset('') }}admin-assets/assets/img/logo.svg" style="height: 60px;" class="theme-logo">
            </div>
            <div class="col-md-12">
                <input id="email" type="hidden" name="email" value="{{ $attempt->email }}" >
                <input id="id" type="hidden" name="id" value="{{ $attempt->id }}" >

                <label for="inputPassword" class="">Password</label>
            
                <div class="input-group mb-4">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                      <div class="input-group-append" style="display: block; margin-left: auto; margin-top: 14px; margin-bottom: -44px; z-index: 5;">
                        <span class="input-group-text" onclick="password_show_hide();" style="background: transparent; border-color: transparent;">
                          <i class="fas fa-eye" id="show_eye"></i>
                          <i class="fas fa-eye-slash d-none" id="hide_eye"></i>
                        </span>
                      </div>


                </div>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="col-md-12">
                <label for="inputPassword" class="">Confirm Password</label>
            
                <div class="input-group mb-4">
                    <input id="confirm-password" type="password" class="form-control" name="password_confirm" required autocomplete="current-password">

                    <div class="input-group-append" style="display: block; margin-left: auto; margin-top: 14px; margin-bottom: -44px; z-index: 5;">
                        <span class="input-group-text" onclick="confirm_password_show_hide();" style="background: transparent; border-color: transparent;">
                          <i class="fas fa-eye" id="show_eye_confirm"></i>
                          <i class="fas fa-eye-slash d-none" id="hide_eye_confirm"></i>
                        </span>
                    </div>

                </div>

                <div style = "color:red" id = "errors"></div>
                <button type="submit" class="btn btn-gradient-dark btn-rounded btn-block">Reset Password</button>
            </div>
            
    </form>

    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="{{ asset('admin-assets/assets/js/libs/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('admin-assets/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('admin-assets/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('admin-assets/plugins/jqvalidation/jqBootstrapValidation-1.3.7.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.js"></script>
    <script src="{{ asset('admin-assets/plugins/notification/toastr/toastr.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/js/all.min.js" integrity="sha512-8pHNiqTlsrRjVD4A/3va++W1sMbUHwWxxRPWNyVlql3T+Hgfd81Qc6FC5WMXDC+tSauxxzp1tgiAvSKFu1qIlA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        // Toaster options
        toastr.options = {
            "closeButton": false,
            "debug": false,
            "newestOnTop": false,
            "progressBar": false,
            "rtl": false,
            "positionClass": "toast-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": 300,
            "hideDuration": 1000,
            "timeOut": 2000,
            "extendedTimeOut": 1000,
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        $(document).ready(function() {
        @if (\Session::has('error') && \Session::get('error') != null)
            toastr["error"]("{{\Session::get('error')}}");
        @endif

        })
        $(".form-login").submit(function(e) {
            e.preventDefault();
        }).validate({
            errorPlacement: function(error, element) { 
                //element.val(error[0].outerText);
                //error.appendTo(element.next('div'));
                 $('#errors').html(error);

            },
            rules: {
                password: {
                    required:true,
                    minlength: 8,
                },
                password_confirm: {
                    required:true,
                    equalTo: "#password"
                }
            },
            messages: {
                password:{
                    required:"Password field is required",
                    minlength: 'Please enter minimum of 8 characters length'
                } ,
                password_confirm:{
                    required:"Confirm Password field is required",
                    equalTo:"Password Mismatch"   
                } 
            },
            submitHandler: function(form) {
                $.ajax({
                    type:'POST',
                    url: "{{ route("user.password_set")}}",
                    data:{
                        '_token': $('input[name=_token]').val(),
                        'password': $("#password").val(),
                        'email': $("#email").val(),
                        'id': $("#id").val(),
                        'timezone': Intl.DateTimeFormat().resolvedOptions().timeZone
                    },
                    success: function(response) {
                        if(response.success){
                            toastr["success"](response.message);
                            document.getElementById('login-form').reset();
                        } else {
                            toastr["error"](response.message);
                        }
                    }
                });
            }
        });
        function password_show_hide() {
        var x = document.getElementById("password");
        var show_eye = document.getElementById("show_eye");
        var hide_eye = document.getElementById("hide_eye");
        hide_eye.classList.remove("d-none");
        if (x.type === "password") {
            x.type = "text";
            show_eye.style.display = "none";
            hide_eye.style.display = "block";
        } else {
            x.type = "password";
            show_eye.style.display = "block";
            hide_eye.style.display = "none";
        }
    }

    function confirm_password_show_hide() {
        var x = document.getElementById("confirm-password");
        var show_eye = document.getElementById("show_eye_confirm");
        var hide_eye = document.getElementById("hide_eye_confirm");
        hide_eye.classList.remove("d-none");
        if (x.type === "password") {
            x.type = "text";
            show_eye.style.display = "none";
            hide_eye.style.display = "block";
        } else {
            x.type = "password";
            show_eye.style.display = "block";
            hide_eye.style.display = "none";
        }
    }
    </script>

    <!-- END GLOBAL MANDATORY SCRIPTS -->
</body>
</html>
