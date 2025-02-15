@livewireStyles
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<title>Porto - Bootstrap eCommerce Template</title>

<meta name="keywords" content="HTML5 Template">
<meta name="description" content="Porto - Bootstrap eCommerce Template">
<meta name="author" content="SW-THEMES">

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/images/icons/favicon.png') }}">

<!-- Ensure correct asset loading for dynamic routes -->
<base href="{{ url('/') }}/">

<script>
    WebFontConfig = {
        google: { families: ['Open+Sans:300,400,600,700,800', 'Poppins:300,400,500,600,700', 'Shadows+Into+Light:400'] }
    };
    (function (d) {
        var wf = d.createElement('script'), s = d.scripts[0];
        wf.src = "{{ asset('assets/js/webfont.js') }}";
        wf.async = true;
        s.parentNode.insertBefore(wf, s);
    })(document);
</script>

<!-- Plugins CSS File -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">

<!-- Main CSS File -->
<link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}">
