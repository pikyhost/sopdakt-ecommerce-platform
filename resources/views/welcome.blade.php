<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <?php $randomProduct = \App\Models\Product::inRandomOrder()->first(); ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-gray-50 text-gray-900 flex flex-col min-h-screen">

<!-- Header -->
<header class="bg-gradient-to-r from-indigo-600 to-purple-500 text-white p-5 shadow-lg sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <h1 class="text-3xl font-extrabold tracking-wide">My E-Commerce</h1>

        <div class="relative">
            <button id="langToggle" class="flex items-center px-4 py-2 bg-white text-gray-900 rounded-md shadow-md hover:bg-gray-100 transition">
                <span class="flag-icon flag-icon-{{ app()->getLocale() === 'ar' ? 'eg' : 'us' }} mr-2"></span>
                {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                <i class="fas fa-chevron-down ml-2 text-gray-500"></i>
            </button>

            <!-- Dropdown Menu -->
            <div id="langMenu" class="absolute bg-white text-gray-800 rounded-lg shadow-lg mt-2 py-2 w-48 hidden transition duration-200">
                <ul>
                    <li>
                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}" class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                            <span class="flag-icon flag-icon-us mr-2"></span> English
                            @if (app()->getLocale() === 'en')
                                <i class="fas fa-check text-green-500 text-sm ml-auto"></i>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}" class="flex items-center px-4 py-2 hover:bg-gray-100 transition">
                            <span class="flag-icon flag-icon-eg mr-2"></span> العربية
                            @if (app()->getLocale() === 'ar')
                                <i class="fas fa-check text-green-500 text-sm ml-auto"></i>
                            @endif
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>

<!-- Hero Section -->
<main class="flex-1 flex flex-col items-center justify-center text-center px-6">
    <h2 class="text-5xl font-extrabold mb-4 bg-gradient-to-r from-purple-600 to-indigo-600 text-transparent bg-clip-text">
        {{ app()->getLocale() === 'ar' ? 'مرحبًا في متجرنا' : 'Welcome to Our Store' }}
    </h2>
    <p class="text-lg text-gray-700">
        {{ app()->getLocale() === 'ar' ? 'اكتشف منتجات رائعة بأفضل الأسعار.' : 'Discover amazing products at the best prices.' }}
    </p>

    <a href="{{ route('product.show', $randomProduct->slug) }}"
       class="mt-8 px-8 py-4 bg-red-500 text-white rounded-lg text-xl font-semibold shadow-md hover:bg-red-600 hover:scale-105 transition duration-300 inline-flex items-center">
        🔀 {{ __('Random Product') }}
    </a>
</main>

<!-- Footer -->
<footer class="bg-gray-900 text-white text-center p-6 mt-10">
    <div class="container mx-auto">
        <p class="text-lg">© 2025 My E-Commerce. All rights reserved.</p>
        <p class="text-sm mt-2">
            Made with ❤️ by
            <a href="https://pikyhost.com" class="underline hover:text-indigo-400 transition">PikyHost</a>
        </p>
    </div>
</footer>

<!-- JavaScript -->
<script>
    // Toggle language dropdown
    document.getElementById('langToggle').addEventListener('click', function () {
        document.getElementById('langMenu').classList.toggle('hidden');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        if (!document.getElementById('langToggle').contains(e.target)) {
            document.getElementById('langMenu').classList.add('hidden');
        }
    });
</script>

</body>
</html>
