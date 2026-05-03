<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="{{ asset('images/lms-logo-icon.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('images/lms-logo-icon.png') }}">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=dm-sans:400,500,700|space-grotesk:500,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
