<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.partials.head')
</head>
<body>

@include('layouts.partials.header')

<main>
    @yield('content')
</main>

@include('layouts.partials.footer')

@include('layouts.partials.scripts')

@livewireScripts
</body>
</html>
