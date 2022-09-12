@extends('layouts.guest')

@section('content')
    <style>
        .invalid-feedback {
            display: block;
            color: red;
            font-weight: normal;
            font-size: 12px;
        }

    </style>
    <div class="wrapper fadeInDown">
        <div id="formContent">
            <!-- Tabs Titles -->
            <h1 class="active" style="margin-bottom: 0">Promo Connect</h1>
            <br>
            <h2 class="active" style="margin-top: 0"> Ingresar </h2>
            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <input type="text" name="email" id="login" class="fadeIn second" name="login" placeholder="Email"
                    value="{{ old('email') }}">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="password" name="password" id="password" class="fadeIn third" name="login" placeholder="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="submit" class="fadeIn fourth" value="Iniciar Sesion">
            </form>

            <!-- Remind Passowrd -->
            @if (Route::has('password.request'))
                <div id="formFooter">
                    <a class="underlineHover" href="{{ route('password.request') }}">Olvide mi contraseña</a>
                </div>
            @endif
        </div>
    </div>
@endsection
