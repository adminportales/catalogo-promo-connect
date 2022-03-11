@extends('layouts.guest')

@section('content')
    <style>
        .invalid-feedback {
            display: block;
            color: red;
            font-weight: normal;
            font-size: 12px;
        }

        .alert-success {
            background-color: rgba(105, 243, 158, 0.842);
            color: rgb(2, 2, 2);
            margin: 15px 30px 15px 30px;
            padding: 10px 0 10px 0;

            border-radius: 15px;
        }

    </style>
    <div class="wrapper fadeInDown">
        <div id="formContent">
            <!-- Tabs Titles -->
            <h2 class="active"> Resetear Contraseña </h2>
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <!-- Login Form -->
            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="text" name="email" id="login" class="fadeIn second" placeholder="Email"
                    value="{{ $email ?? old('email') }}" >
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="password" name="password" id="password" class="fadeIn second"
                    placeholder="New Password" value="" required autocomplete="new-password">
                <input type="password" name="password_confirmation" id="password_confirmation" class="fadeIn second"
                    placeholder="Confirm Password" value="" required autocomplete="new-password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="submit" class="fadeIn fourth" value="Resetear contraseña">
            </form>
        </div>
    </div>
@endsection
