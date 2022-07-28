<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

    <!-- Styles -->
    <link href="{{ global_asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ global_asset('/vendor/fontawesome-pro/css/all.min.css') }}" rel="stylesheet">
	<link rel="shortcut icon" href="{{ global_asset( '/img/favicon.png' ) }}">

	<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @if (App::environment('production'))
        @include ('snippets.tagmanagerhead')
    @endif

</head>
<body>
    @if (App::environment('production'))
        @include ('snippets.tagmanagerbody')
    @endif

    @routes(nonce: 'your-nonce-here')

    @inertia

    <!-- Scripts -->
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
    <script src="{{ mix('/js/app.js') }}"></script>

	@stack('scripts-footer-bottom')

</body>
</html>
