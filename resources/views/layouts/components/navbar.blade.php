<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>
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
                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                        <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                        Salir
                    </a>
                </div>
            </li>
        @endauth

    </ul>

</nav>
