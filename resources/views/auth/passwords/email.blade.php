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
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <input type="text" name="email" id="login" class="fadeIn second" name="login" placeholder="Email"
                    value="{{ old('email') }}">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
                <input type="submit" class="fadeIn fourth" value="Enviar Link de Reseteo">
            </form>
        </div>
    </div>
@endsection
