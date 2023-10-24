@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="text-center mb-4">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('img/'.$companyInfo->logo) }}" alt="" height="73" class="logo-dark mx-auto">
                </a>
            </div>
            <div class="card bg-gray-lini">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h4 class="text-uppercase mt-0">masuk | <span class="text-info">admin</span></h4>
                    </div>

                    <div class="flash-message">
                        @foreach (['danger','warning','success','info'] as $msg)
                            @if (Session::has('alert-'.$msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        @endforeach
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            Email & password tidak sesuai atau akun Anda belum aktif. 
                            Silahkan coba lagi atau kontak administrator.
                        </div>
                    @endif
                    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.login.submit') }}" data-parsley-validate novalidate>
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email">Alamat email</label>
                            <input class="form-control @error('email') is-invalid @enderror" name="email" parsley-trigger="change" type="email" id="email" value="{{ old('email') }}" required placeholder="Enter your email">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input class="form-control @error('password') is-invalid @enderror" name="password" type="password" required id="password" placeholder="Enter your password">
                        </div>
                        <div class="form-group mb-3">
                            <div class="custom-control custom-checkbox">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">
                                    {{ __('Remember Me') }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-primary btn-block" name="login" type="submit"> Masuk </button>
                        </div>
                    </form>
                </div> <!-- end card-body -->
                <div class="text-center">
                    <p> <a href="{{ route('password.request') }}" class="text-muted ml-1"><i class="fa fa-lock mr-1"></i>Lupa password?</a></p>
                </div> <!-- end col -->
            </div>
            <!-- end card -->
            <!-- end row -->
        </div> <!-- end col -->
    </div>
</div>
@endsection
