<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('') }}">
        <div class="sidebar-brand-text mx-3">Promo Connect</div>
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

    <!--Nav Bar Hooks - Do not delete!!-->

    <li class="nav-item">
        <a class="nav-link" href="{{ url('catalogo') }}" target="_blank">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Ver Catalogo</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ url('admin/products') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Productos</span></a>
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
    {{-- <li class="nav-item">
        <a class="nav-link" href="{{ url('admin/sites') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Sitios</span></a>
    </li> --}}
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
        <a class="nav-link" href="{{ url('admin/media') }}">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Medios</span></a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseRoles"
            aria-expanded="true" aria-controls="collapseRoles">
            <i class="fas fa-fw fa-cog"></i>
            <span>Roles</span>
        </a>
        <div id="collapseRoles" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Gestor de Roles:</h6>
                <a class="collapse-item" href="{{ url('/laratrust') }}">General</a>
                <a class="collapse-item" href="{{ url('/admin/roles-providers') }}">Proveedores</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
