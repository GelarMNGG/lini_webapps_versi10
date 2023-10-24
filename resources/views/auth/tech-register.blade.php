@extends('layouts.login')

@section('content')
<?php 
    $formRouteRegister = 'tech.register';
?>
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
                        <h4 class="text-uppercase mt-0">Daftar | <span class="text-info">tech</span></h4>
                    </div>

                    <div class="flash-message">
                        @foreach (['danger','warning','success','info'] as $msg)
                            @if (Session::has('alert-'.$msg))
                                <p class="alert alert-{{ $msg }}">{{ Session::get('alert-'.$msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
                            @endif
                        @endforeach
                    </div>

                    <form action="{{ route($formRouteRegister) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- hidden data -->
                        <input type="text" name="user_type" value="tech" hidden>

                        <div class="form-group">
                            <label for="name">Nama</label>
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" type="text" data-parsley-length="[3,30]" placeholder="Masukkan nama Anda" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Alamat email</label>
                            <input id="email" name="email" class="form-control @error('email') is-invalid @enderror" type="email" required placeholder="Masukkan email Anda" value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="d_usr_password">Password</label>
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Masukkan password Anda" data-parsley-length="[6,117]"  required autocomplete="new-password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            
                        </div>
                        <div class="form-group">
                            <label for="password-confirm">Masukkan ulang password</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan ulang password Anda">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-checkbox checkbox">
                                <input type="checkbox" class="custom-control-input" id="checkbox-signup" data-parsley-multiple="groups" data-parsley-mincheck="1" required>
                                <label class="custom-control-label" for="checkbox-signup">Setuju <a href="javascript: void(0);" class="text-dark">Syarat dan Ketentuan</a></label>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-center">
                            <button name="daftar" class="btn btn-primary btn-block" type="submit"> Daftar </button>
                        </div>
                    </form>
                </div> <!-- end card-body -->
                <div class="text-center">
                    <p class="text-muted">Sudah punya akun?  <a href="{{ route('tech.login') }}" class="text-dark ml-1"><b>Masuk</b></a></p>
                </div> <!-- end col -->
            </div>
            <!-- end card -->
            <!-- end row -->
        </div> <!-- end col -->
    </div>
    <!-- end row -->
</div>
@endsection
