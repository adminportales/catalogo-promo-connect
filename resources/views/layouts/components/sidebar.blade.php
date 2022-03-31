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
            <a class="nav-link" href="{{ url('catalogo') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Catalogo</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/products') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Productos</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/batchInputProducts') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Importar Productos</span></a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/categories') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Categorias</span></a>
        </li> --}}
        {{-- <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/subcategories') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Subcategorias</span></a>
        </li> --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/sites') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Sitios</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/providers') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Proveedores</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/globalAttributes') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Globales</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('admin/users') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Usuarios</span></a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ url('/laratrust') }}">
                <i class="fas fa-fw fa-chart-area"></i>
                <span>Roles</span></a>
        </li>
    @endauth()

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
