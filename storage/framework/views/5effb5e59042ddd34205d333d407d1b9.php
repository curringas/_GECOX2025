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
                    <a href="https://themesbrand.com/skote/layouts/index.html" target="_blank" class="waves-effect">
                    <span class="badge rounded-pill bg-danger float-end" key="t-hot"><?php echo app('translator')->get('translation.hot'); ?></span>
                        <i class="bx bx-layout"></i>
                        <span key="t-layouts"><?php echo app('translator')->get('translation.Layouts'); ?></span>
                    </a>                    
                </li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-utility">Mantenimiento/Prox.</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="mantenimiento/maintenance" key="t-maintenance"><?php echo app('translator')->get('translation.Maintenance'); ?></a></li>
                        <li><a href="mantenimiento/comingsoon" key="t-coming-soon"><?php echo app('translator')->get('translation.Coming_Soon'); ?></a></li>
                    </ul>
                </li>


                <li>
                    <a href="/publicaciones" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones"><?php echo app('translator')->get('titulos.Publicaciones'); ?></span>
                    </a>
                    
                </li>

                <li class="menu-title" key="t-backend"><?php echo app('translator')->get('translation.backend'); ?></li>
                <li>
                    <a href="/customers" class="waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-yajra-datatable"><?php echo app('translator')->get('translation.yajra-datatable'); ?></span>
                    </a>
                </li>
                <li>
                    <a href="/categorias" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones"><?php echo app('translator')->get('titulos.Categorias'); ?></span>
                    </a>
                    
                </li>

                <li class="menu-title" key="t-pages"><?php echo app('translator')->get('translation.Administration'); ?></li>

                <li>
                    <a href="/users" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication"><?php echo app('translator')->get('translation.Users'); ?></span>
                    </a>
                    
                </li>
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

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="bx bx-share-alt"></i>
                        <span key="t-multi-level"><?php echo app('translator')->get('translation.Multi_Level'); ?></span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="javascript: void(0);" key="t-level-1-1"><?php echo app('translator')->get('translation.Level_1.1'); ?></a></li>
                        <li>
                            <a href="javascript: void(0);" class="has-arrow"
                                key="t-level-1-2"><?php echo app('translator')->get('translation.Level_1.2'); ?></a>
                            <ul class="sub-menu" aria-expanded="true">
                                <li><a href="javascript: void(0);" key="t-level-2-1"><?php echo app('translator')->get('translation.Level_2.1'); ?></a>
                                </li>
                                <li><a href="javascript: void(0);" key="t-level-2-2"><?php echo app('translator')->get('translation.Level_2.2'); ?></a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
<?php /**PATH /Users/curro/Documents/WEBSERVICES/_GECOX2025/resources/views/layouts/sidebar.blade.php ENDPATH**/ ?>