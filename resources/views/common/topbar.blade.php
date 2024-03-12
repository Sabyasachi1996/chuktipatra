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
                @if(empty(Session::get('user_id')))  
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="/verify-request">Verify Request</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sign-document">Sign Agreement</a></li>
                    <li class="nav-item"><a class="nav-link" href="/user-authenticate">Sign-in</a></li>
                @else 
                    <li class="nav-item"><a class="nav-link" href="/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/logout">Logout</a></li>
                @endif
            </ul>
        </div>
    </div>
</nav>