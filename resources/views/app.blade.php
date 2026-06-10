<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@php $faviconUrl = \App\Models\SiteSetting::get('site_favicon_url', ''); @endphp
@if($faviconUrl)<link rel="icon" href="{{ $faviconUrl }}">@endif
@viteReactRefresh
@vite(['resources/css/app.css', 'resources/js/app.jsx'])
@inertiaHead
</head>
<body>
@inertia
</body>
</html>
