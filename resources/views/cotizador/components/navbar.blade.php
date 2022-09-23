<nav class="navbar navbar-expand-md topbar mb-2 static-top shadow h-auto">
    <a href="/catalogo" class="navbar-brand">
        <div class="text-light d-flex align-items-center">
            <img class="imagen" height="60" src="{{ asset('/img/logoOnly.png') }}" alt="bhtrade">
            <div>
                <h6 class="m-0 text-white font-weight-bold" style="font-family:'Myriad Pro Bold';font-weight:bold;">PROMO
                    CONNECT
                </h6>
                <p class="m-0 text-white d-none d-sm-block" style="font-family:'Myriad Pro Regular';font-weight:normal;">Encuentra
                    tus articulos</p>
            </div>
        </div>
    </a>
    <button class="navbar-toggler text-light" type="button" data-toggle="collapse" data-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Topbar Navbar -->
        <ul class="navbar-nav ml-auto">
            <li class="nav-item d-md-flex d-none align-items-center">
                <input type="text" class="form-control">
            </li>
            <div class="topbar-divider d-none d-md-block"></div>
            <!-- Nav Item - User Information -->
            @auth
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="mr-2 d-inline text-white"> {{ Auth::user()->name }}</span>
                        <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                    </a>

                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                        @role('admin')
                            <a class="dropdown-item" href="{{ url('admin/') }}">
                                <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                Administrador
                            </a>
                        @endrole
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                            Salir
                        </a>
                    </div>
                </li>
            @endauth
        </ul>
    </div>
</nav>

<!-- Logout Modal-->
<div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
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

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>
