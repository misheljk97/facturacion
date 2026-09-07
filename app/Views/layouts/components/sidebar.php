<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="<?= base_url() ?>" class="brand-link">
            <span class="brand-text fw-light">Facturación App</span>
        </a>
    </div>

    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?= base_url('/') ?>" class="nav-link <?= (url_is('/') || url_is('dashboard')) ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Módulo Desplegable: Facturación -->
                <li class="nav-item <?= url_is('facturas*') ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= url_is('facturas*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-receipt"></i>
                        <p>
                            Facturación
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="<?= base_url('facturas/nueva') ?>" class="nav-link <?= url_is('facturas/nueva') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Nueva Factura</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('facturas/historial') ?>" class="nav-link <?= url_is('facturas/historial') ? 'active' : '' ?>">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Historial</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Módulo: Categorías -->
                <li class="nav-item">
                    <a href="<?= base_url('categorias') ?>" class="nav-link <?= url_is('categorias*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-tags"></i>
                        <p>Categorías</p>
                    </a>
                </li>

                <!-- Módulo: Marcas -->
                <li class="nav-item">
                    <a href="<?= base_url('marcas') ?>" class="nav-link <?= url_is('marcas*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-bookmark-star"></i>
                        <p>Marcas</p>
                    </a>
                </li>

                <!-- Módulo: Clientes -->
                <li class="nav-item">
                    <a href="<?= base_url('clientes') ?>" class="nav-link <?= url_is('clientes*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-people"></i>
                        <p>Clientes</p>
                    </a>
                </li>

                <!-- Módulo: Proveedores -->
                <li class="nav-item">
                    <a href="<?= base_url('proveedores') ?>" class="nav-link <?= url_is('proveedores*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-truck"></i>
                        <p>Proveedores</p>
                    </a>
                </li>

                <!-- Módulo: Productos -->
                <li class="nav-item">
                    <a href="<?= base_url('productos') ?>" class="nav-link <?= url_is('productos*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-box-seam"></i>
                        <p>Productos</p>
                    </a>
                </li>

                <!-- Módulo: Usuarios -->
                <li class="nav-item">
                    <a href="<?= base_url('usuarios') ?>" class="nav-link <?= url_is('usuarios*') ? 'active' : '' ?>">
                        <i class="nav-icon bi bi-person-gear"></i>
                        <p>Usuarios</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>