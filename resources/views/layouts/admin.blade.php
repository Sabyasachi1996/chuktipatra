<!doctype html>
<html lang="en">
	<head>
		@include('common.header')
	</head>
	<body>
		<!-- Header Topbar -->
		@include('common.topbarAdmin')

		<section class="content-wrapper">
			<div class="container">
				<div class="row mb-3">
					<div class="col-12">
						@if(!empty(Session::get('user_fullname')))
							<h1 class="h6 fw-bold text-white">Welcome, {{ Session::get('user_fullname') }}</h1>
						@endif
					</div>
				</div>
				<div class="row justify-content-center">
					
					<!-- Page Content -->
						@yield('content')
					<!-- /#page-content -->
					
				</div>
			</div>
		</section>
        <div id="page_loader">
			<img src="assets/images/page_loader.gif"/>
		</div>
		<footer class="py-4">
			<div class="container">
				<p class="text-center m-0 small">Developed by Department of IT & E, Govt. of WB</p>
			</div>
		</footer>

		{!! flash_message() !!}
	</body>
	
	@include('common.footer')

</html>

