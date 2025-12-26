<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu"><?php echo app('translator')->get('translation.Menu'); ?></li>

                <li>
                    <a href="/" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards"><?php echo app('translator')->get('translation.Dashboards'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="/publicaciones" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones"><?php echo app('translator')->get('titulos.Publicaciones'); ?></span>
                    </a>
                    
                </li>
                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Super-admin|Admin')): ?>
                <li class="menu-title" key="t-backend"><?php echo app('translator')->get('translation.backend'); ?></li>
                <li>
                    <a href="/banners" class="waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-banners"><?php echo app('translator')->get('titulos.Banners'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="/categorias" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones"><?php echo app('translator')->get('titulos.Categorias'); ?></span>
                    </a>
                    
                </li>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Super-admin|Admin')): ?>

                <li class="menu-title" key="t-pages"><?php echo app('translator')->get('translation.Administration'); ?></li>

                <li>
                    <a href="/indexado/edit" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication">Indexado</span>
                    </a>
                </li>

                <li>
                    <a href="/users" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication"><?php echo app('translator')->get('translation.Users'); ?></span>
                    </a>
                    
                </li>
                <?php endif; ?>

                <?php if (\Illuminate\Support\Facades\Blade::check('role', 'Super-admin')): ?>
                <li class="menu-title" key="t-pages"><?php echo app('translator')->get('translation.Configuracion'); ?></li>

                <li>
                    <a href="/roles" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication"><?php echo app('translator')->get('translation.Roles'); ?></span>
                    </a>                    
                </li>
                <li>
                    <a href="/permissions" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication"><?php echo app('translator')->get('translation.Permisos'); ?></span>
                    </a>
                    
                </li>
                <?php endif; ?>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>