<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <ul class="navbar-nav mr-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item no-arrow">
            <div class="nav-link text-dark">
                <img class="img-fluid h-100" src="{{ asset('/img/logo.png') }}" alt="bhtrade"></a>
            </div>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
        <li class="nav-item">
            <div class="nav-link text-dark">
                <h5>Promo Connect</h5>
            </div>
        </li>
    </ul>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <div class="topbar-divider d-none d-sm-block"></div>

        <!-- Nav Item - User Information -->
        @auth
            <li class="nav-item dropdown no-arrow">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"> {{ Auth::user()->name }}</span>
                    {{-- <img class="img-profile rounded-circle" src="img/undraw_profile.svg"> --}}
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
