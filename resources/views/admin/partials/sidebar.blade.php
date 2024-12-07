<div class="sidebar-menu">
    <div class="sidebar-header">
        <div class="logo">
            <a href="index.html"><img src="{{ asset('assets/images/icon/logo.png') }}" alt="logo"></a>
        </div>
    </div>
    <div class="main-menu">
        <div class="menu-inner">
            <nav>
                <ul class="metismenu" id="menu">
                    <li>
                        <!-- Menu untuk "Simple Additive Weighting" -->
                        <a href="index.html">
                            <i class="ti-dashboard"></i>
                            <span>Simple Additive Weighting</span>
                        </a>
                    </li>
                    <li>
                        <!-- Menu untuk Logout dengan Form POST -->
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a href="#" onclick="document.getElementById('logout-form').submit();">
                            <i class="ti-power-off"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>                
            </nav>
        </div>
    </div>
</div>