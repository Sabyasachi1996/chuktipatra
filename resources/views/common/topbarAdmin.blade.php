<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
        <div class="logo-wrapper d-flex justify-content-center align-items-center">
            <a class="navbar-brand" href="{{app_url()}}">
                <img src="{{app_url()}}/assets/images/logo.png" alt="Govt. of WB" class="img-fluid">
            </a>
            <h1>eAgreement <span>- Govt. of West Bengal</span></h1>
        </div>
        <button data-bs-toggle="collapse" class="navbar-toggler" data-bs-target="#navbarResponsive"><span class="visually-hidden">Toggle navigation</span><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
           <ul class="navbar-nav ms-auto">
                @if(!empty(Session::get('adminLogin')))  
                    <li class="nav-item"><a class="nav-link" href="/admin/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin/agreement-list">Agreement List</a></li>
                    <li class="nav-item"><a class="nav-link btn-admin-logout" href="#">Logout</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>