<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @hasSection('title')
            @yield('title') |
        @endif {{ config('app.name', 'Laravel') }}
    </title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('styles')
    @livewireStyles
</head>

<body>
    <div id="app">
        <div id="page-top">

            <div id="wrapper">
                {{-- Sidebar --}}
                @include('layouts.components.sidebar')

                <div id="content-wrapper" class="d-flex flex-column">

                    <div id="content">
                        @include('layouts.components.navbar')



                        <!-- Page Heading -->
                        {{-- <div class="d-sm-flex align-items-center justify-content-between mb-4">
                            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        </div> --}}
                        <div class="row d-flex flex-column">
                            {{-- <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
                                <div class="container">
                                    <a class="navbar-brand" href="{{ url('/') }}">
                                        {{ config('app.name', 'Laravel') }}
                                    </a>
                                    <button class="navbar-toggler" type="button" data-toggle="collapse"
                                        data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                        aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                                        <span class="navbar-toggler-icon"></span>
                                    </button>

                                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                        <!-- Left Side Of Navbar -->
                                        @auth()
                                            <ul class="navbar-nav mr-auto">
                                                <!--Nav Bar Hooks - Do not delete!!-->
						<li class="nav-item">
                            <a href="{{ url('/media') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Media</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/prices') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Prices</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/product_attributes') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Product_attributes</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/sites') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Sites</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/images') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Images</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/types') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Types</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/colors') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Colors</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/sites') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> Sites</a>
                        </li>
						<li class="nav-item">
                            <a href="{{ url('/globalAttributes') }}" class="nav-link"><i class="fab fa-laravel text-info"></i> GlobalAttributes</a>
                        </li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/users') }}" class="nav-link"><i
                                                            class="fab fa-laravel text-info"></i> Users</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/products') }}" class="nav-link"><i
                                                            class="fab fa-laravel text-info"></i> Products</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/subcategories') }}" class="nav-link"><i
                                                            class="fab fa-laravel text-info"></i> Subcategories</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/categories') }}" class="nav-link"><i
                                                            class="fab fa-laravel text-info"></i> Categories</a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{ url('/providers') }}" class="nav-link"><i
                                                            class="fab fa-laravel text-info"></i> Providers</a>
                                                </li>
                                            </ul>
                                        @endauth()

                                        <!-- Right Side Of Navbar -->
                                        <ul class="navbar-nav ml-auto">
                                            <!-- Authentication Links -->
                                            @guest
                                                @if (Route::has('login'))
                                                    <li class="nav-item">
                                                        <a class="nav-link"
                                                            href="{{ route('login') }}">{{ __('Login') }}</a>
                                                    </li>
                                                @endif

                                                @if (Route::has('register'))
                                                    <li class="nav-item">
                                                        <a class="nav-link"
                                                            href="{{ route('register') }}">{{ __('Register') }}</a>
                                                    </li>
                                                @endif
                                            @else
                                                <li class="nav-item dropdown">
                                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#"
                                                        role="button" data-toggle="dropdown" aria-haspopup="true"
                                                        aria-expanded="false" v-pre>
                                                        {{ Auth::user()->name }}
                                                    </a>

                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="navbarDropdown">
                                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                                            {{ __('Logout') }}
                                                        </a>

                                                        <form id="logout-form" action="{{ route('logout') }}"
                                                            method="POST" class="d-none">
                                                            @csrf
                                                        </form>
                                                    </div>
                                                </li>
                                            @endguest
                                        </ul>
                                    </div>
                                </div>
                            </nav> --}}
                            <main class="py-0">
                                @yield('content')
                            </main>
                        </div>
                    </div>



                    <!-- Logout Modal-->
                    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">Desea salir del catalogo?.
                                    <br>
                                    <br>
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">No</button>
                                    <a class="btn btn-primary" href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                                        document.getElementById('logout-form').submit();">
                                        {{ __('Salir') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                        class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>


                    <footer class="sticky-footer bg-white">
                        <div class="container my-auto">
                            <div class="copyright text-center my-auto">
                                <span>Copyright &copy; Your Website 2021</span>
                            </div>
                        </div>
                    </footer>
                </div>

            </div>
        </div>
    </div>
    @livewireScripts
    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('vendor/jquery-easing/jquery.easing.min.js') }}"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('js/sb-admin-2.min.js') }}"></script>
    <script type="text/javascript">
        window.livewire.on('closeModal', () => {
            $('#createDataModal').modal('hide');
        });
    </script>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('scripts')
</body>

</html>
