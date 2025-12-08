<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" key="t-menu">@lang('translation.Menu')</li>

                <li>
                    <a href="/" class="waves-effect">
                        <i class="bx bx-home-circle"></i>
                        <span key="t-dashboards">@lang('translation.Dashboards')</span>
                    </a>
                </li>
                <li>
                    <a href="/publicaciones" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones">@lang('titulos.Publicaciones')</span>
                    </a>
                    
                </li>

                <li class="menu-title" key="t-backend">@lang('translation.backend')</li>
                <li>
                    <a href="/banners" class="waves-effect">
                        <i class="bx bx-list-ul"></i>
                        <span key="t-banners">@lang('titulos.Banners')</span>
                    </a>
                </li>
                <li>
                    <a href="/categorias" class="waves-effect">
                        <i class="bx bx-file"></i>
                        <span key="t-publicaciones">@lang('titulos.Categorias')</span>
                    </a>
                    
                </li>

                <li class="menu-title" key="t-pages">@lang('translation.Administration')</li>

                <li>
                    <a href="/users" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication">@lang('translation.Users')</span>
                    </a>
                    
                </li>
                @role('Super-admin')
                <li class="menu-title" key="t-pages">@lang('translation.Configuracion')</li>

                <li>
                    <a href="/roles" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication">@lang('translation.Roles')</span>
                    </a>                    
                </li>
                <li>
                    <a href="/permissions" class="waves-effect">
                        <i class="bx bx-user-circle"></i>
                        <span key="t-authentication">@lang('translation.Permisos')</span>
                    </a>
                    
                </li>
                @endrole

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<!-- Left Sidebar End -->
