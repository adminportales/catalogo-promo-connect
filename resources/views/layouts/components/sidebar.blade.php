<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('') }}">
        <div class="sidebar-brand-text mx-3">Catalogo</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ url('') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    @auth()
        <!--Nav Bar Hooks - Do not delete!!-->

        <li class="nav-item">
            <a class="nav-link" href="{{ url('/products') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Productos</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/categories') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Categorias</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/subcategories') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Subcategorias</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/providers') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Proveedores</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/globalAttributes') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Globales</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/users') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Usuarios</span></a>
        </li>
    @endauth()

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
