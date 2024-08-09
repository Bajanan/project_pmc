@extends('layouts.app')

@section('content')
    <section class="p-t-80 p-b-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-12 offset-lg-3">
                    <div class="login-card">

                        <div class="text-center">
                            <img src="{{ asset('uploads/1710228517.png') }}" width="80px" class="pb-3" alt="UTIC" />
                            <h4 class="headingH3-norm p-b-32">Login to PMC Online</h4>
                        </div>

                        <form class="" method="POST" action="">
                            @csrf
                            <div class="p-b-30">
                                <p class="form-label">Email</p>
                                <input type="email"
                                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }} form-text"
                                    id="exampleInputEmail1" name="email" placeholder="Please type your email"
                                    aria-describedby="emailHelp" value="{{ old('email', $prefilledEmail ?? null) }}"
                                    required>
                                @if ($errors->has('email'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <div class="p-b-30">
                                <p class="form-label">Password</p>
                                <div class="icon-container">
                                    <input id="password" type="password" placeholder="Please type your password"
                                        class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }} form-text"
                                        name="password" required>
                                    <a onclick="togglePasswordVisibility()">
                                        <i id="password_img" class="fa fa-eye-slash d5 icon-over"></i>
                                    </a>

                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <script>
                                function togglePasswordVisibility() {
                                    var passwordField = document.getElementById('password');
                                    var icon = document.getElementById('password_img');
                                    if (passwordField.type === "password") {
                                        passwordField.type = "text";
                                        icon.classList.remove('fa-eye-slash');
                                        icon.classList.add('fa-eye');
                                    } else {
                                        passwordField.type = "password";
                                        icon.classList.remove('fa-eye');
                                        icon.classList.add('fa-eye-slash');
                                    }
                                }
                            </script>

                            <div class="p-b-40">

                                <div class="input-group icon-container align-items-center">
                                    <input id="" type="checkbox" placeholder="" class="form-check me-3"
                                        name=""> Remember Me
                                </div>
                            </div>

                            <button type="submit" class="main-button d1 button-full a1-bg">Login</button>

                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
