@extends('layouts.login')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 col-xl-5">
            <div class="text-center mb-4">
                <a href="home" class="logo">
                    <img src="{{ asset('img/logo.png') }}" alt="" height="73" class="logo-dark mx-auto">
                </a>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <h4 class="text-uppercase mt-0 mb-3">Atur Ulang Password</h4>
                        <p class="text-muted mb-0 font-13">Masukkan email Anda dan kami akan mengirimkan link untuk mengatur ulang password Anda.</p>
                    </div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="email">Masukkan email anda</label>
                            <input id="email" name="email" class="form-control @error('email') is-invalid @enderror" type="email" required autocomplete="email" placeholder="Masukkan email Anda">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-0 text-center">
                            <button class="btn btn-success btn-block" type="submit"> Kirim Link Atur Ulang Password </button>
                        </div>
                    </form>
                </div> <!-- end card-body -->
            </div>
        </div> <!-- end col -->
    </div>
    <!-- end row -->
</div>
@endsection
