<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | {{ config('app.name', 'Choir Concierge') }}</title>

    <!-- Styles -->
    <link href="{{ global_asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ global_asset('css/style.css') }}" rel="stylesheet">
	<link rel="shortcut icon" href="{{ global_asset( '/img/favicon.png' ) }}">

	<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

</head>
<body>
    <div id="app">

		@yield('app-content')

    </div>

    <!-- Scripts -->
	<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
	
	<script>
	  var HW_config = {
		selector: ".headway-badge", // CSS selector where to inject the badge
		account:  "7L6Rky"
	  }
	</script>
	<script async src="//cdn.headwayapp.co/widget.js"></script>
    
    <script src="{{ global_asset('js/app.js') }}"></script>
    <script src="{{ global_asset('js/script.js') }}"></script>

	@stack('scripts-footer-bottom')
	
</body>
</html>
