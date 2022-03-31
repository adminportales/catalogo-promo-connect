<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
    <ul class="navbar-nav mr-auto">
        <!-- Nav Item - User Information -->
        <li class="nav-item no-arrow">
            <div class="nav-link text-dark">
                <h5>Catalogo</h5>
            </div>
        </li>
        <div class="topbar-divider d-none d-sm-block"></div>
    </ul>
    <div class="w-100 py-2 h-100">
        <ul class="d-flex justify-content-around align-items-center h-100 m-0 p-0">
            <li class="p-0 m-0 h-100" style="list-style: none">
                <img class="h-100 img-fluid" src="{{ asset('/img/bhtrade.png') }}" alt="bhtrade"></a>
            </li>
            <li class="p-0 m-0 h-100" style="list-style: none">
                <img class="h-100 img-fluid" src="{{ asset('/img/promolife.png') }}" alt="promolife"></a>
            </li>
            <li class="p-0 m-0 h-100" style="list-style: none">
                <img class="h-100  w-auto img-fluid" src="{{ asset('/img/promodreams.png') }}" alt="promodreams"></a>
            </li>
            <li class="p-0 m-0 h-100" style="list-style: none">
                <img class="h-100 img-fluid" src="{{ asset('/img/trademarket.png') }}" alt="trademarket"></a>
            </li>
        </ul>
    </div>

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
                <a class="btn btn-primary" href="{{ route('logout') }}" onclick="event.preventDefault();
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
