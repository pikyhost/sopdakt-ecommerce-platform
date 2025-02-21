<!DOCTYPE html>
<html lang="en">
<head>
    @livewireStyles
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>X - Bootstrap eCommerce Template</title>

    <meta name="keywords" content="HTML5 Template">
    <meta name="description" content="X - Bootstrap eCommerce Template">
    <meta name="author" content="SW-THEMES">

    <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . \App\Models\Setting::getSetting('site_settings')['favicon'][app()->getLocale()]) }}">

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

    <!-- Ensure Bootstrap JS loads properly -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>
</head>


<body>
	<div class="page-wrapper">
		<div class="top-notice bg-primary text-white">
			<div class="container text-center">
				<h5 class="d-inline-block">Get Up to <b>40% OFF</b> New-Season Styles</h5>
				<a href="category.html" class="category">MEN</a>
				<a href="category.html" class="category ml-2 mr-3">WOMEN</a>
				<small>* Limited time only.</small>
				<button title="Close (Esc)" type="button" class="mfp-close">×</button>
			</div><!-- End .container -->
		</div><!-- End .top-notice -->

		<header class="header">
			<div class="header-top">
				<div class="container">
					<div class="header-left d-none d-sm-block">
						<p class="top-message text-uppercase">FREE Returns. Standard Shipping Orders $99+</p>
					</div><!-- End .header-left -->

					<div class="header-right header-dropdowns ml-0 ml-sm-auto w-sm-100">
						<div class="header-dropdown dropdown-expanded d-none d-lg-block">
							<a href="#">Links</a>
							<div class="header-menu">
								<ul>
									<li><a href="dashboard.html">My Account</a></li>
									<li><a href="about.html">About Us</a></li>
									<li><a href="blog.html">Blog</a></li>
									<li><a href="wishlist.html">My Wishlist</a></li>
									<li><a href="cart.html">Cart</a></li>
									<li><a href="login.html" class="login-link">Log In</a></li>
								</ul>
							</div><!-- End .header-menu -->
						</div><!-- End .header-dropown -->

						<span class="separator"></span>

                        <div class="header-dropdown">
                            <a href="#">
                                <i class="flag-{{ app()->getLocale() === 'ar' ? 'eg' : 'us' }} flag"></i>
                                {{ app()->getLocale() === 'ar' ? 'العربية' : 'English' }}
                            </a>
                            <div class="header-menu">
                                <ul>
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('en', null, [], true) }}">
                                            <i class="flag-us flag mr-2"></i> English
                                            @if (app()->getLocale() === 'en')
                                                <i class="fas fa-check text-sm ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ LaravelLocalization::getLocalizedURL('ar', null, [], true) }}">
                                            <i class="flag-eg flag mr-2"></i> العربية
                                            @if (app()->getLocale() === 'ar')
                                                <i class="fas fa-check text-sm ml-1"></i>
                                            @endif
                                        </a>
                                    </li>
                                </ul>
                            </div><!-- End .header-menu -->
                        </div><!-- End .header-dropdown -->



                        <div class="header-dropdown mr-auto mr-sm-3 mr-md-0">
							<a href="#">USD</a>
							<div class="header-menu">
								<ul>
									<li><a href="#">EUR</a></li>
									<li><a href="#">USD</a></li>
								</ul>
							</div><!-- End .header-menu -->
						</div><!-- End .header-dropown -->

						<span class="separator"></span>

						<div class="social-icons">
							<a href="#" class="social-icon social-facebook icon-facebook" target="_blank"></a>
							<a href="#" class="social-icon social-twitter icon-twitter" target="_blank"></a>
							<a href="#" class="social-icon social-instagram icon-instagram" target="_blank"></a>
						</div><!-- End .social-icons -->
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-top -->

			<div class="header-middle sticky-header" data-sticky-options="{'mobile': true}">
				<div class="container">
                    <div class="header-left col-lg-2 w-auto pl-0 d-flex flex-column align-items-center">
                        <button class="mobile-menu-toggler text-primary mr-2" type="button">
                            <i class="fas fa-bars"></i>
                        </button>

                        <a href="demo4.html" class="logo">
                            <img src="{{ asset('storage/' . \App\Models\Setting::getSetting('site_settings')['logo'][app()->getLocale()]) }}"
                                 alt="Site Logo"
                                 style="
                width: 80px;
                height: 80px;
                border-radius: 50%;
                object-fit: cover;
                border: 3px solid #fff;
                box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
                transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
            "
                                 onmouseover="this.style.transform='scale(1.1)'; this.style.boxShadow='0px 6px 12px rgba(0, 0, 0, 0.3)';"
                                 onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0px 4px 8px rgba(0, 0, 0, 0.2)';"
                            >
                        </a>

                        <p class="site-name mt-2 font-weight-bold text-dark text-center">
                            {{ \App\Models\Setting::getSetting('site_settings')['name'][app()->getLocale()] }}
                        </p>
                    </div>


                    <div class="header-right w-lg-max">
						<div
							class="header-icon header-search header-search-inline header-search-category w-lg-max text-right mt-0">
							<a href="#" class="search-toggle" role="button"><i class="icon-search-3"></i></a>
							<form action="#" method="get">
								<div class="header-search-wrapper">
									<input type="search" class="form-control" name="q" id="q" placeholder="Search..."
										required>
									<div class="select-custom">
										<select id="cat" name="cat">
											<option value="">All Categories</option>
											<option value="4">Fashion</option>
											<option value="12">- Women</option>
											<option value="13">- Men</option>
											<option value="66">- Jewellery</option>
											<option value="67">- Kids Fashion</option>
											<option value="5">Electronics</option>
											<option value="21">- Smart TVs</option>
											<option value="22">- Cameras</option>
											<option value="63">- Games</option>
											<option value="7">Home &amp; Garden</option>
											<option value="11">Motors</option>
											<option value="31">- Cars and Trucks</option>
											<option value="32">- Motorcycles &amp; Powersports</option>
											<option value="33">- Parts &amp; Accessories</option>
											<option value="34">- Boats</option>
											<option value="57">- Auto Tools &amp; Supplies</option>
										</select>
									</div><!-- End .select-custom -->
									<button class="btn icon-magnifier p-0" type="submit"></button>
								</div><!-- End .header-search-wrapper -->
							</form>
						</div><!-- End .header-search -->

						<div class="header-contact d-none d-lg-flex pl-4 pr-4">
							<img alt="phone" src="assets/images/phone.png" width="30" height="30" class="pb-1">
							<h6><span>Call us now</span><a href="tel:#" class="text-dark font1">+123 5678 890</a></h6>
						</div>

						<a href="login.html" class="header-icon" title="login"><i class="icon-user-2"></i></a>

						<a href="wishlist.html" class="header-icon" title="wishlist"><i class="icon-wishlist-2"></i></a>

						<div class="dropdown cart-dropdown">
							<a href="#" title="Cart" class="dropdown-toggle dropdown-arrow cart-toggle" role="button"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
								<i class="minicart-icon"></i>
								<span class="cart-count badge-circle">3</span>
							</a>

							<div class="cart-overlay"></div>

							<div class="dropdown-menu mobile-cart">
								<a href="#" title="Close (Esc)" class="btn-close">×</a>

								<div class="dropdownmenu-wrapper custom-scrollbar">
									<div class="dropdown-cart-header">Shopping Cart</div>
									<!-- End .dropdown-cart-header -->

									<div class="dropdown-cart-products">
										<div class="product">
											<div class="product-details">
												<h4 class="product-title">
													<a href="product.html">Ultimate 3D Bluetooth Speaker</a>
												</h4>

												<span class="cart-product-info">
													<span class="cart-product-qty">1</span>
													× $99.00
												</span>
											</div><!-- End .product-details -->

											<figure class="product-image-container">
												<a href="product.html" class="product-image">
													<img src="assets/images/products/product-1.jpg" alt="product"
														width="80" height="80">
												</a>

												<a href="#" class="btn-remove" title="Remove Product"><span>×</span></a>
											</figure>
										</div><!-- End .product -->

										<div class="product">
											<div class="product-details">
												<h4 class="product-title">
													<a href="product.html">Brown Women Casual HandBag</a>
												</h4>

												<span class="cart-product-info">
													<span class="cart-product-qty">1</span>
													× $35.00
												</span>
											</div><!-- End .product-details -->

											<figure class="product-image-container">
												<a href="product.html" class="product-image">
													<img src="assets/images/products/product-2.jpg" alt="product"
														width="80" height="80">
												</a>

												<a href="#" class="btn-remove" title="Remove Product"><span>×</span></a>
											</figure>
										</div><!-- End .product -->

										<div class="product">
											<div class="product-details">
												<h4 class="product-title">
													<a href="product.html">Circled Ultimate 3D Speaker</a>
												</h4>

												<span class="cart-product-info">
													<span class="cart-product-qty">1</span>
													× $35.00
												</span>
											</div><!-- End .product-details -->

											<figure class="product-image-container">
												<a href="product.html" class="product-image">
													<img src="assets/images/products/product-3.jpg" alt="product"
														width="80" height="80">
												</a>
												<a href="#" class="btn-remove" title="Remove Product"><span>×</span></a>
											</figure>
										</div><!-- End .product -->
									</div><!-- End .cart-product -->

									<div class="dropdown-cart-total">
										<span>SUBTOTAL:</span>

										<span class="cart-total-price float-right">$134.00</span>
									</div><!-- End .dropdown-cart-total -->

									<div class="dropdown-cart-action">
										<a href="cart.html" class="btn btn-gray btn-block view-cart">View
											Cart</a>
										<a href="checkout.html" class="btn btn-dark btn-block">Checkout</a>
									</div><!-- End .dropdown-cart-total -->
								</div><!-- End .dropdownmenu-wrapper -->
							</div><!-- End .dropdown-menu -->
						</div><!-- End .dropdown -->
					</div><!-- End .header-right -->
				</div><!-- End .container -->
			</div><!-- End .header-middle -->

			<div class="header-bottom sticky-header d-none d-lg-block" data-sticky-options="{'mobile': false}">
				<div class="container">
					<nav class="main-nav w-100">
						<ul class="menu">
							<li>
								<a href="demo4.html">Home</a>
							</li>
							<li>
								<a href="category.html">Categories</a>
								<div class="megamenu megamenu-fixed-width megamenu-3cols">
									<div class="row">
										<div class="col-lg-4">
											<a href="#" class="nolink">VARIATION 1</a>
											<ul class="submenu">
												<li><a href="category.html">Fullwidth Banner</a></li>
												<li><a href="category-banner-boxed-slider.html">Boxed Slider Banner</a>
												</li>
												<li><a href="category-banner-boxed-image.html">Boxed Image Banner</a>
												</li>
												<li><a href="category.html">Left Sidebar</a></li>
												<li><a href="category-sidebar-right.html">Right Sidebar</a></li>
												<li><a href="category-off-canvas.html">Off Canvas Filter</a></li>
												<li><a href="category-horizontal-filter1.html">Horizontal Filter1</a>
												</li>
												<li><a href="category-horizontal-filter2.blade.php">Horizontal Filter2</a>
												</li>
											</ul>
										</div>
										<div class="col-lg-4">
											<a href="#" class="nolink">VARIATION 2</a>
											<ul class="submenu">
												<li><a href="category-list.html">List Types</a></li>
												<li><a href="category-infinite-scroll.html">Ajax Infinite Scroll</a>
												</li>
												<li><a href="category.html">3 Columns Products</a></li>
												<li><a href="category-4col.html">4 Columns Products</a></li>
												<li><a href="category-5col.html">5 Columns Products</a></li>
												<li><a href="category-6col.html">6 Columns Products</a></li>
												<li><a href="category-7col.html">7 Columns Products</a></li>
												<li><a href="category-8col.html">8 Columns Products</a></li>
											</ul>
										</div>
										<div class="col-lg-4 p-0">
											<div class="menu-banner">
												<figure>
													<img src="assets/images/menu-banner.jpg" width="192" height="313"
														alt="Menu banner">
												</figure>
												<div class="banner-content">
													<h4>
														<span class="">UP TO</span><br />
														<b class="">50%</b>
														<i>OFF</i>
													</h4>
													<a href="category.html" class="btn btn-sm btn-dark">SHOP NOW</a>
												</div>
											</div>
										</div>
									</div>
								</div><!-- End .megamenu -->
							</li>
							<li class="active">
								<a href="product.html">Products</a>
								<div class="megamenu megamenu-fixed-width">
									<div class="row">
										<div class="col-lg-4">
											<a href="#" class="nolink">PRODUCT PAGES</a>
											<ul class="submenu">
												<li><a href="product.html">SIMPLE PRODUCT</a></li>
												<li><a href="product-variable.html">VARIABLE PRODUCT</a></li>
												<li><a href="product.html">SALE PRODUCT</a></li>
												<li><a href="product.html">FEATURED & ON SALE</a></li>
												<li><a href="product-custom-tab.html">WITH CUSTOM TAB</a></li>
												<li><a href="product-sidebar-left.html">WITH LEFT SIDEBAR</a></li>
												<li><a href="product-sidebar-right.html">WITH RIGHT SIDEBAR</a></li>
												<li><a href="product-addcart-sticky.html">ADD CART STICKY</a></li>
											</ul>
										</div><!-- End .col-lg-4 -->

										<div class="col-lg-4">
											<a href="#" class="nolink">PRODUCT LAYOUTS</a>
											<ul class="submenu">
												<li><a href="product-extended-layout.html">EXTENDED LAYOUT</a></li>
												<li><a href="product-grid-layout.html">GRID IMAGE</a></li>
												<li><a href="product-full-width.html">FULL WIDTH LAYOUT</a></li>
												<li><a href="product-sticky-info.html">STICKY INFO</a></li>
												<li><a href="product-sticky-both.html">LEFT & RIGHT STICKY</a></li>
												<li><a href="product-transparent-image.html">TRANSPARENT IMAGE</a></li>
												<li><a href="product-center-vertical.html">CENTER VERTICAL</a></li>
												<li><a href="#">BUILD YOUR OWN</a></li>
											</ul>
										</div><!-- End .col-lg-4 -->

										<div class="col-lg-4 p-0">
											<div class="menu-banner menu-banner-2">
												<figure>
													<img src="assets/images/menu-banner-1.jpg" width="182" height="317"
														alt="Menu banner" class="product-promo">
												</figure>
												<i>OFF</i>
												<div class="banner-content">
													<h4>
														<span class="">UP TO</span><br />
														<b class="">50%</b>
													</h4>
												</div>
												<a href="category.html" class="btn btn-sm btn-dark">SHOP NOW</a>
											</div>
										</div><!-- End .col-lg-4 -->
									</div><!-- End .row -->
								</div><!-- End .megamenu -->
							</li>
							<li>
								<a href="#">Pages</a>
								<ul>
									<li><a href="wishlist.html">Wishlist</a></li>
									<li><a href="cart.html">Shopping Cart</a></li>
									<li><a href="checkout.html">Checkout</a></li>
									<li><a href="dashboard.html">Dashboard</a></li>
									<li><a href="about.html">About Us</a></li>
									<li><a href="#">Blog</a>
										<ul>
											<li><a href="blog.html">Blog</a></li>
											<li><a href="single.html">Blog Post</a></li>
										</ul>
									</li>
									<li><a href="contact.html">Contact Us</a></li>
									<li><a href="login.html">Login</a></li>
									<li><a href="forgot-password.html">Forgot Password</a></li>
								</ul>
							</li>
							<li><a href="blog.html">Blog</a></li>
							<li>
								<a href="#">Elements</a>
								<ul class="custom-scrollbar">
									<li><a href="element-accordions.html">Accordion</a></li>
									<li><a href="element-alerts.html">Alerts</a></li>
									<li><a href="element-animations.html">Animations</a></li>
									<li><a href="element-banners.html">Banners</a></li>
									<li><a href="element-buttons.html">Buttons</a></li>
									<li><a href="element-call-to-action.html">Call to Action</a></li>
									<li><a href="element-countdown.html">Count Down</a></li>
									<li><a href="element-counters.html">Counters</a></li>
									<li><a href="element-headings.html">Headings</a></li>
									<li><a href="element-icons.html">Icons</a></li>
									<li><a href="element-info-box.html">Info box</a></li>
									<li><a href="element-posts.html">Posts</a></li>
									<li><a href="element-products.html">Products</a></li>
									<li><a href="element-product-categories.html">Product Categories</a></li>
									<li><a href="element-tabs.html">Tabs</a></li>
									<li><a href="element-testimonial.html">Testimonials</a></li>
								</ul>
							</li>
							<li><a href="contact.html">Contact Us</a></li>
							<li class="float-right"><a href="https://1.envato.market/DdLk5" class="pl-5"
									target="_blank">Buy X!</a></li>
							<li class="float-right"><a href="#" class="pl-5">Special Offer!</a></li>
						</ul>
					</nav>
				</div><!-- End .container -->
			</div><!-- End .header-bottom -->
		</header><!-- End .header -->

		<main class="main">
			<div class="container">
				<nav aria-label="breadcrumb" class="breadcrumb-nav">
					<ol class="breadcrumb">
						<li class="breadcrumb-item"><a href="demo4.html"><i class="icon-home"></i></a></li>
						<li class="breadcrumb-item"><a href="#">Products</a></li>
					</ol>
				</nav>

				<div class="product-single-container product-single-info">
					<div class="cart-message d-none">
						<strong class="single-cart-notice">“Men Black Sports Shoes”</strong>
						<span>has been added to your cart.</span>
					</div>

					<div class="row">
                        <div class="col-lg-5 col-md-6 product-single-gallery">
                            @if ($product->getFeatureProductImageUrl())
                                <div class="product-item position-relative">
                                    <div class="inner position-relative">

                                        <!-- Labels Group (Similar Style as "HOT" and "SALE") -->
                                        <div class="label-group position-absolute" style="top: 10px; left: 10px; z-index: 10;">
                                            @forelse($product->labels as $label)
                                                <div class="product-label"
                                                     style="background-color: {{ $label->background_color_code }};
                                color: {{ $label->color_code }};
                                font-size: 14px;
                                font-weight: bold;">
                                                    {{ $label->getTranslation('title', app()->getLocale()) }}
                                                </div>
                                            @empty
                                                <!-- No Labels -->
                                            @endforelse
                                        </div>

                                        <!-- Product Image -->
                                        <img src="{{ $product->getFeatureProductImageUrl() }}"
                                             data-zoom-image="{{ $product->getFeatureProductImageUrl() }}"
                                             width="480" height="480" alt="feature-image">

                                        <!-- Full Screen Icon -->
                                        <span class="prod-full-screen">
                <i class="icon-plus"></i>
            </span>
                                    </div>
                                </div><!-- End .product-item -->
                            @endif


                        @if ($product->getSecondFeatureProductImageUrl())
                                <div class="product-item">
                                    <div class="inner">
                                        <img src="{{ $product->getSecondFeatureProductImageUrl() }}"
                                             data-zoom-image="{{ $product->getSecondFeatureProductImageUrl() }}"
                                             width="480" height="480" alt="second-feature-image">
                                        <span class="prod-full-screen">
                    <i class="icon-plus"></i>
                </span>
                                    </div>
                                </div><!-- End .product-item -->
                            @endif

                                @foreach (array_reverse($product->getMoreProductImagesAndVideosUrls()) as $mediaUrl)
                                    <div class="product-item">
                                        <div class="inner">
                                            @if (Str::endsWith($mediaUrl, ['.mp4', '.mpeg', '.mov', '.avi']))
                                                <video width="480" height="480" controls>
                                                    <source src="{{ $mediaUrl }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>
                                            @else
                                                <img src="{{ $mediaUrl }}" data-zoom-image="{{ $mediaUrl }}"
                                                     width="480" height="480" alt="product-media">
                                            @endif
                                            <span class="prod-full-screen">
                <i class="icon-plus"></i>
            </span>
                                        </div>
                                    </div><!-- End .product-item -->
                                @endforeach

                        </div><!-- End .col-md-5 -->


                        <div class="col-lg-7 col-md-6">
							<div class="sidebar-wrapper">
								<div class="product-single-details">
									<h1 class="product-title">{{ $product->name }}</h1>

									<div class="product-nav">
										<div class="product-prev">
											<a href="#">
												<span class="product-link"></span>

												<span class="product-popup">
													<span class="box-content">
														<img alt="product" width="150" height="150"
															src="assets/images/products/product-3.jpg"
															style="padding-top: 0px;">

														<span>Circled Ultimate 3D Speaker</span>
													</span>
												</span>
											</a>
										</div>

										<div class="product-next">
											<a href="#">
												<span class="product-link"></span>

												<span class="product-popup">
													<span class="box-content">
														<img alt="product" width="150" height="150"
															src="assets/images/products/product-4.jpg"
															style="padding-top: 0px;">

														<span>Blue Backpack for the Young</span>
													</span>
												</span>
											</a>
										</div>
									</div>

                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            <span class="ratings" style="width: {{ $product->fake_average_rating * 20 }}%"></span><!-- End .ratings -->
                                            <span class="tooltiptext tooltip-top"></span>
                                        </div><!-- End .product-ratings -->

                                        <a href="#" class="rating-link">( {{ $product->ratings()->count() }} Reviews )</a>
                                    </div><!-- End .ratings-container -->

                                    <hr class="short-divider">

                                    <div class="price-box">
                                        @if($product->after_discount_price)
                                            <span class="old-price">{{ $product->price_for_current_country }}</span>
                                            <span class="new-price">{{ $product->discount_price_for_current_country }}</span>
                                        @else
                                            <span class="new-price">{{ $product->price_for_current_country }}</span>
                                        @endif
                                    </div><!-- End .price-box -->

                                    <div class="product-desc">
										<p>
											{{ $product->summary }}
										</p>
									</div><!-- End .product-desc -->

									<ul class="single-info-list">
										<!---->
										<li>
											SKU:
											<strong>
                                                {{ $product->sku }}
                                            </strong>
										</li>

										<li>
											CATEGORY:
											<strong>
												<a href="{{ route('category.products', $product->category->slug) }}" class="product-category">
                                                    {{ $product->category->name }}
                                                </a>
											</strong>
										</li>
									</ul>

									<div class="product-filters-container custom-product-filters">
                                        <div class="product-single-filter">
                                            <label>Size:</label>
                                            <ul class="config-size-list">
                                                @foreach($product->sizes as $size)
                                                    <li>
                                                        <a href="javascript:;" class="d-flex align-items-center justify-content-center">
                                                            {{ $size->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>


                                        <div class="product-single-filter">
                                            <label>{{ __('Color:') }}</label>
                                            <ul class="config-size-list config-color-list">
                                                @forelse ($product->colorsWithImages as $productColor)
                                                    <li>
                                                        <a href="javascript:;"
                                                           class="d-flex align-items-center justify-content-center p-0 color-swatch"
                                                           style="background-color: {{ $productColor->color->code }}; border: 2px solid #ddd;"
                                                           title="{{ $productColor->color->name }}"
                                                           data-image="{{ asset('storage/' . $productColor->image) }}">
                                                            &nbsp;
                                                        </a>
                                                    </li>
                                                @empty
                                                    <p>{{ __('No colors available at the moment.') }}</p>
                                                    <p>{{ __('لا توجد ألوان متاحة في الوقت الحالي.') }}</p>
                                                @endforelse
                                            </ul>
                                        </div>

                                        {{-- Display the selected color image --}}
                                        <div class="selected-color-image mt-3">
                                            <img id="color-image" src="" alt="Selected Color" style="display: none; width: 200px; height: auto;">
                                        </div>

                                        <script>
                                            document.addEventListener("DOMContentLoaded", function () {
                                                const colorSwatches = document.querySelectorAll(".color-swatch");
                                                const colorImage = document.getElementById("color-image");

                                                colorSwatches.forEach(swatch => {
                                                    swatch.addEventListener("click", function () {
                                                        const imageUrl = this.getAttribute("data-image");

                                                        if (imageUrl) {
                                                            colorImage.src = imageUrl;
                                                            colorImage.style.display = "block";
                                                        }
                                                    });
                                                });
                                            });
                                        </script>


                                        {{-- Inline CSS for styling --}}
                                        <style>
                                            .config-color-list {
                                                list-style: none;
                                                padding: 0;
                                                display: flex;
                                                gap: 8px;
                                            }

                                            .color-swatch {
                                                width: 30px;
                                                height: 30px;
                                                display: block;
                                                border-radius: 50%; /* Round shape */
                                                transition: transform 0.2s ease, border-color 0.2s ease;
                                            }

                                            .color-swatch:hover {
                                                transform: scale(1.1);
                                                border-color: #666; /* Darker border on hover */
                                            }
                                        </style>
										<!---->
									</div>

									<div class="product-action">
										<div class="price-box product-filtered-price">
											<del class="old-price"> <span>$286.00</span></del>
											<span class="product-price">$245.00</span>
										</div>

										<div class="product-single-qty">
											<input class="horizontal-quantity form-control" type="text">
										</div><!-- End .product-single-qty -->

										<a href="cart.html"
											class="btn btn-dark disabled add-cart icon-shopping-cart mr-2"
											title="Add to Cart">Add to Cart</a>

										<a href="cart.html" class="btn btn-gray view-cart d-none">View cart</a>
									</div><!-- End .product-action -->

									<hr class="divider mb-0 mt-0">

                                    <livewire:wishlist-button :product-id="$product->id" />

                                    <style>
                                        .product-bundles {
                                            background: #fff;
                                            border-radius: 8px;
                                            padding: 20px;
                                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                                        }

                                        .bundle-title {
                                            font-size: 20px;
                                            font-weight: bold;
                                            margin-bottom: 15px;
                                            color: #333;
                                        }

                                        .bundle-list {
                                            list-style: none;
                                            padding: 0;
                                            margin: 0;
                                        }

                                        .bundle-item {
                                            border: 1px solid #eee;
                                            border-radius: 8px;
                                            padding: 15px;
                                            margin-bottom: 15px;
                                        }

                                        .bundle-header {
                                            display: flex;
                                            justify-content: space-between;
                                            align-items: center;
                                            font-size: 18px;
                                            font-weight: bold;
                                            color: #222;
                                            margin-bottom: 10px;
                                        }

                                        .bundle-text {
                                            font-size: 14px;
                                            color: #666;
                                            margin-bottom: 12px;
                                        }

                                        .bundle-products {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 15px;
                                            align-items: center;
                                        }

                                        .product-item {
                                            display: flex;
                                            align-items: center;
                                            background: #f9f9f9;
                                            padding: 10px;
                                            border-radius: 6px;
                                            border: 1px solid #ddd;
                                            width: fit-content;
                                        }

                                        .product-image {
                                            width: 50px;
                                            height: 50px;
                                            object-fit: cover;
                                            border-radius: 5px;
                                            margin-right: 10px;
                                        }

                                        .product-info {
                                            display: flex;
                                            flex-direction: column;
                                            font-size: 14px;
                                        }

                                        .product-name {
                                            font-weight: 500;
                                            color: #333;
                                        }

                                        .product-quantity {
                                            font-size: 12px;
                                            color: #777;
                                        }

                                        .bundle-buy {
                                            display: flex;
                                            justify-content: flex-end;
                                            margin-top: 10px;
                                        }

                                        .buy-button {
                                            background: #007bff;
                                            color: white;
                                            font-size: 13px;
                                            padding: 8px 12px;
                                            border-radius: 5px;
                                            text-decoration: none;
                                            transition: 0.3s;
                                        }

                                        .buy-button:hover {
                                            background: #0056b3;
                                        }

                                    </style>
                                    <br>

                                    @if($product->bundles->isNotEmpty())
                                        <div x-data="{ showBundles: false }" class="product-bundles">
                                            <!-- Bundle Title & Toggle Button -->
                                            <div class="bundle-header">
                                                <h3 class="bundle-title">{{ __('Available Bundles') }}</h3>
                                                <button @click="showBundles = !showBundles" class="toggle-button">
                                                    <span x-text="showBundles ? '{{ __('Hide Bundles') }}' : '{{ __('Show Bundles') }}'"></span>
                                                    <svg x-show="!showBundles" class="icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                                    </svg>
                                                    <svg x-show="showBundles" class="icon rotate-180" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Bundle List -->
                                            <ul class="bundle-list" x-show="showBundles" x-collapse>
                                                @foreach ($product->bundles as $bundle)
                                                    <li class="bundle-item">
                                                        <div class="bundle-content">
                                                            <strong class="bundle-name">{{ $bundle->getTranslation('name', app()->getLocale()) }}</strong>

                                                            <div class="bundle-details">
                                                                @if ($bundle->bundle_type === \App\Enums\BundleType::BUY_X_GET_Y)
                                                                    @if (!is_null($bundle->buy_x) && !is_null($bundle->get_y))
                                                                        <p class="bundle-text">{{ __('Buy :x and get :y free', ['x' => $bundle->buy_x, 'y' => $bundle->get_y]) }}</p>
                                                                    @elseif (!is_null($bundle->buy_x) && is_null($bundle->get_y) && !is_null($bundle->discount_price))
                                                                        <p class="bundle-text">{{ __('Buy :x with a discount price of :price', ['x' => $bundle->buy_x, 'price' => number_format($bundle->discount_price, 2)]) }}</p>
                                                                    @endif
                                                                @elseif ($bundle->bundle_type === \App\Enums\BundleType::FIXED_PRICE)
                                                                    <p class="bundle-text">{{ __('Get this bundle for :price instead of :original', ['price' => number_format($bundle->discount_price, 2), 'original' => number_format($bundle->products->sum('after_discount_price'), 2)]) }}</p>
                                                                @elseif ($bundle->bundle_type === \App\Enums\BundleType::DISCOUNT_PERCENTAGE)
                                                                    <p class="bundle-text">{{ __('Buy this bundle and save :discount%', ['discount' => $bundle->discount_percentage]) }}</p>
                                                                @endif
                                                            </div>

                                                            <div class="bundle-products">
                                                                @foreach ($bundle->products as $product)
                                                                    <div class="product-item">
                                                                        <a href="{{ route('product.show', $product->slug) }}">
                                                                            <img src="{{ $product->getFeatureProductImageUrl() }}" class="product-image" alt="{{ $product->name }}">
                                                                        </a>
                                                                        <div class="product-info">
                                                                            <span class="product-name">{{ $product->name }}</span>
                                                                            @if ($bundle->bundle_type !== \App\Enums\BundleType::BUY_X_GET_Y)
                                                                                <span class="product-quantity">({{ __('Quantity:') }} {{ $product->pivot->quantity }})</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <div class="bundle-buy">
                                                                <a href="#" class="buy-button">{{ __('Buy Now') }}</a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif




                                @if (session()->has('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    @if (session()->has('error'))
                                        <div class="alert alert-danger">
                                            {{ session('error') }}
                                        </div>
                                    @endif


                                </div><!-- End .product-single-details -->
							</div>
						</div><!-- End .col-md-7 -->
					</div>

					<div class="row align-items-start">
                        <div
							class="product-single-share col-md-3 col-xl-6 align-items-start justify-content-md-end mt-0">
							<label class="sr-only">Share:</label>

							<div class="social-icons mt-0 pb-5 pb-md-0">
								<a href="#" class="social-icon social-facebook icon-facebook" target="_blank"
									title="Facebook"></a>
								<a href="#" class="social-icon social-twitter icon-twitter" target="_blank"
									title="Twitter"></a>
								<a href="#" class="social-icon social-linkedin fab fa-linkedin-in" target="_blank"
									title="Linkedin"></a>
								<a href="#" class="social-icon social-gplus fab fa-google-plus-g" target="_blank"
									title="Google +"></a>
								<a href="#" class="social-icon social-mail icon-mail-alt" target="_blank"
									title="Mail"></a>
							</div><!-- End .social-icons -->
						</div><!-- End .product-single-share -->
					</div><!-- End .row -->
				</div><!-- End .product-single-container -->
			</div><!-- End .products-section -->

			<div class="product-single-tabs custom-product-single-tabs bg-gray mb-4">
				<div class="container">
					<ul class="nav nav-tabs" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" id="product-tab-desc" data-toggle="tab"
								href="#product-desc-content" role="tab" aria-controls="product-desc-content"
								aria-selected="true">Description</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="product-tab-size" data-toggle="tab" href="#product-size-content"
								role="tab" aria-controls="product-size-content" aria-selected="true">Size Guide</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="product-tab-reviews" data-toggle="tab"
								href="#product-reviews-content" role="tab" aria-controls="product-reviews-content"
								aria-selected="false">Reviews</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" id="product-tab-tags" data-toggle="tab" href="#product-tags-content"
								role="tab" aria-controls="product-tags-content" aria-selected="false">Custom Tab</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade show active" id="product-desc-content" role="tabpanel"
							aria-labelledby="product-tab-desc">
							<div class="product-desc-content">
								<p>
                                    {!! \Illuminate\Support\Str::markdown($product->description) !!}
                                </p>
							</div><!-- End .product-desc-content -->
						</div><!-- End .tab-pane -->

                        <div class="tab-pane fade" id="product-size-content" role="tabpanel" aria-labelledby="product-tab-size">
                            <div class="text-center">
                                <img src="{{ $product->getProductSizeImageUrl() ?? asset('assets/images/products/default-size.png') }}"
                                     alt="Size Guide"
                                     class="img-fluid rounded shadow-lg">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="product-reviews-content" role="tabpanel"
							aria-labelledby="product-tab-reviews">
							<div class="product-reviews-content">
                                @livewire('product-reviews', ['product' => $product])
                            </div><!-- End .product-reviews-content -->
						</div><!-- End .tab-pane -->

                        <div class="tab-pane fade" id="product-tags-content" role="tabpanel" aria-labelledby="product-tab-tags">
                            <h4>Product Features and Attributes</h4>


                            @if (!empty($customAttributes))
                                <ul class="list-unstyled">
                                    @foreach ($customAttributes[app()->getLocale()] ?? [] as $key => $value)
                                        <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Display Database Attributes --}}
                            @if ($product->attributes->isNotEmpty())
                                <ul class="list-unstyled">
                                    @foreach ($product->attributes as $attribute)
                                        <li>
                                            <strong>{{ $attribute->getTranslation('name', app()->getLocale()) }}</strong>:

                                            @if ($attribute->type === 'boolean')
                                                {{ $attribute->pivot->value ? 'Yes' : 'No' }}
                                            @elseif ($attribute->type === 'select')
                                                {{ collect(json_decode($attribute->values, true))->firstWhere('key', $attribute->pivot->value)['label'] ?? $attribute->pivot->value }}
                                            @else
                                                {{ $attribute->pivot->value }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            {{-- Display Product Types --}}
                            @if ($product->types->isNotEmpty())
                                <h4>Available Types</h4>
                                <div class="row">
                                    @foreach ($product->types as $type)
                                        <div class="col-md-4 text-center">
                                            <img src="{{ asset('storage/' . $type->image) }}" alt="{{ $type->name }}" class="img-fluid mb-2">
                                            <p><strong>{{ $type->name }}</strong></p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>


                    </div><!-- End .tab-content -->
				</div>
			</div><!-- End .product-single-tabs -->

			<div class="container">
				<div class="products-section pt-0">
					<h2 class="section-title">Related Products</h2>

                    <div class="products-slider owl-carousel owl-theme dots-top dots-small">
                        @foreach ($relatedProducts as $relatedProduct)
                            <div class="product-default">
                                <figure>
                                    <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                        <img src="{{ $relatedProduct->getFirstMediaUrl('feature_product_image') }}" width="280" height="280" alt="product">
                                        <img src="{{ $relatedProduct->getFirstMediaUrl('second_feature_product_image') }}" width="280" height="280" alt="product">
                                    </a>
                                </figure>
                                <div class="product-details">
                                    <div class="category-list">
                                        <a href="{{ route('category.products',$relatedProduct->category->slug ) }}" class="product-category">
                                            {{ $relatedProduct->category->name }}
                                        </a>
                                    </div>
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                            {{ $relatedProduct->name }}
                                        </a>
                                    </h3>
                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            @php
                                                $ratingPercentage = ($relatedProduct->average_rating / 5) * 100;
                                            @endphp
                                            <span class="ratings" style="width:{{ $ratingPercentage }}%"></span>
                                            <span class="tooltiptext tooltip-top">{{ number_format($relatedProduct->average_rating, 1) }} / 5</span>
                                        </div>
                                    </div>
                                    <div class="price-box">
                                        @if ($relatedProduct->after_discount_price)
                                            <del class="old-price">${{ $relatedProduct->price_for_current_country }}</del>
                                        @endif
                                        <span class="product-price">${{ $relatedProduct->discount_price_for_current_country }}</span>
                                    </div>
                                    <livewire:product-actions :product="$relatedProduct" wire:key="relatedProduct-{{ $product->id }}" />
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div><!-- End .products-section -->

				<hr class="mt-0 m-b-5" />

				<div class="product-widgets-container row pb-2">
                    <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                        <h4 class="section-sub-title">Featured Products</h4>

                        @foreach($featuredProducts as $featuredProduct)
                            <div class="product-default left-details product-widget">
                                <figure>
                                    <a href="{{ route('product.show', $featuredProduct->slug) }}">
                                        <img src="{{ $featuredProduct->getFirstMediaUrl('feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                        <img src="{{ $featuredProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                    </a>
                                </figure>

                                <div class="product-details">
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $featuredProduct->slug) }}">
                                            {{ $featuredProduct->name }}
                                        </a>
                                    </h3>

                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            <span class="ratings" style="width: {{ $featuredProduct->getRatingPercentage() }}%"></span>
                                            <span class="tooltiptext tooltip-top"></span>
                                        </div>
                                    </div>

                                    <div class="price-box">
                                        <span class="product-price">${{ $featuredProduct->discount_price_for_current_country }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                        <h4 class="section-sub-title">Best Selling Products</h4>

                        @foreach($bestSellingProducts as $bestProduct)
                            <div class="product-default left-details product-widget">
                                <figure>
                                    <a href="{{ route('product.show', $bestProduct->slug) }}">
                                        <img src="{{ $bestProduct->getFirstMediaUrl('feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                        <img src="{{ $bestProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                    </a>
                                </figure>

                                <div class="product-details">
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $bestProduct->slug) }}">
                                            {{ $bestProduct->name }}
                                        </a>
                                    </h3>

                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            <span class="ratings" style="width: {{ $bestProduct->getRatingPercentage() }}%"></span>
                                            <span class="tooltiptext tooltip-top">{{ number_format($bestProduct->getRatingPercentage(), 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="price-box">
                                        <span class="product-price">${{ $bestProduct->discount_price_for_current_country }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>


                    <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                        <h4 class="section-sub-title">Latest Products</h4>

                        @foreach($latestProducts as $latestProduct)
                            <div class="product-default left-details product-widget">
                                <figure>
                                    <a href="{{ route('product.show', $latestProduct->slug) }}">
                                        <img src="{{ $latestProduct->getFirstMediaUrl('feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                        <img src="{{ $latestProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                    </a>
                                </figure>

                                <div class="product-details">
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $latestProduct->slug) }}">
                                            {{ $latestProduct->name }}
                                        </a>
                                    </h3>

                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            <span class="ratings" style="width: {{ $latestProduct->getRatingPercentage() }}%"></span>
                                            <span class="tooltiptext tooltip-top">{{ number_format($latestProduct->getRatingPercentage(), 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="price-box">
                                        @if ($latestProduct->after_discount_price)
                                            <del class="old-price">${{ $latestProduct->price_for_current_country }}</del>
                                        @endif
                                        <span class="product-price">${{ $latestProduct->discount_price_for_current_country }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-lg-3 col-sm-6 pb-5 pb-md-0">
                        <h4 class="section-sub-title">Top Rated Products</h4>

                        @foreach($topRatedProducts as $topRatedProduct)
                            <div class="product-default left-details product-widget">
                                <figure>
                                    <a href="{{ route('product.show', $topRatedProduct->slug) }}">
                                        <img src="{{ $topRatedProduct->getFirstMediaUrl('feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                        <img src="{{ $topRatedProduct->getFirstMediaUrl('second_feature_product_image') }}"
                                             width="74" height="74" alt="product">
                                    </a>
                                </figure>

                                <div class="product-details">
                                    <h3 class="product-title">
                                        <a href="{{ route('product.show', $topRatedProduct->slug) }}">
                                            {{ $topRatedProduct->name }}
                                        </a>
                                    </h3>

                                    <div class="ratings-container">
                                        <div class="product-ratings">
                                            <span class="ratings" style="width: {{ ($topRatedProduct->final_average_rating / 5) * 100 }}%"></span>
                                            <span class="tooltiptext tooltip-top">{{ number_format($topRatedProduct->final_average_rating, 2) }}</span>
                                        </div>
                                    </div>

                                    <div class="price-box">
                                        <span class="product-price">${{ $topRatedProduct->discount_price_for_current_country }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

				</div><!-- End .row -->
			</div>
		</main><!-- End .main -->

		<footer class="footer bg-dark">
			<div class="footer-middle">
				<div class="container">
					<div class="row">
						<div class="col-lg-3 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Contact Info</h4>
								<ul class="contact-info">
									<li>
										<span class="contact-info-label">Address:</span>123 Street Name, City, England
									</li>
									<li>
										<span class="contact-info-label">Phone:</span><a href="tel:">(123)
											456-7890</a>
									</li>
									<li>
										<span class="contact-info-label">Email:</span> <a
											href="mailto:mail@example.com">mail@example.com</a>
									</li>
									<li>
										<span class="contact-info-label">Working Days/Hours:</span>
										Mon - Sun / 9:00 AM - 8:00 PM
									</li>
								</ul>
								<div class="social-icons">
									<a href="#" class="social-icon social-facebook icon-facebook" target="_blank"
										title="Facebook"></a>
									<a href="#" class="social-icon social-twitter icon-twitter" target="_blank"
										title="Twitter"></a>
									<a href="#" class="social-icon social-instagram icon-instagram" target="_blank"
										title="Instagram"></a>
								</div><!-- End .social-icons -->
							</div><!-- End .widget -->
						</div><!-- End .col-lg-3 -->

						<div class="col-lg-3 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Customer Service</h4>

								<ul class="links">
									<li><a href="#">Help & FAQs</a></li>
									<li><a href="#">Order Tracking</a></li>
									<li><a href="#">Shipping & Delivery</a></li>
									<li><a href="#">Orders History</a></li>
									<li><a href="#">Advanced Search</a></li>
									<li><a href="dashboard.html">My Account</a></li>
									<li><a href="#">Careers</a></li>
									<li><a href="about.html">About Us</a></li>
									<li><a href="#">Corporate Sales</a></li>
									<li><a href="#">Privacy</a></li>
								</ul>
							</div><!-- End .widget -->
						</div><!-- End .col-lg-3 -->

						<div class="col-lg-3 col-sm-6">
							<div class="widget">
								<h4 class="widget-title">Popular Tags</h4>

								<div class="tagcloud">
									<a href="#">Bag</a>
									<a href="#">Black</a>
									<a href="#">Blue</a>
									<a href="#">Clothes</a>
									<a href="#">Fashion</a>
									<a href="#">Hub</a>
									<a href="#">Shirt</a>
									<a href="#">Shoes</a>
									<a href="#">Skirt</a>
									<a href="#">Sports</a>
									<a href="#">Sweater</a>
								</div>
							</div><!-- End .widget -->
						</div><!-- End .col-lg-3 -->

						<div class="col-lg-3 col-sm-6">
							<div class="widget widget-newsletter">
								<h4 class="widget-title">Subscribe newsletter</h4>
								<p>Get all the latest information on events, sales and offers. Sign up for newsletter:
								</p>
								<form action="#" class="mb-0">
									<input type="email" class="form-control m-b-3" placeholder="Email address" required>

									<input type="submit" class="btn btn-primary shadow-none" value="Subscribe">
								</form>
							</div><!-- End .widget -->
						</div><!-- End .col-lg-3 -->
					</div><!-- End .row -->
				</div><!-- End .container -->
			</div><!-- End .footer-middle -->

			<div class="container">
				<div class="footer-bottom">
					<div class="container d-sm-flex align-items-center">
						<div class="footer-left">
							<span class="footer-copyright">© Piky Host eCommerce. 2021. All Rights Reserved</span>
						</div>

						<div class="footer-right ml-auto mt-1 mt-sm-0">
							<div class="payment-icons">
								<span class="payment-icon visa"
									style="background-image: url(assets/images/payments/payment-visa.svg)"></span>
								<span class="payment-icon paypal"
									style="background-image: url(assets/images/payments/payment-paypal.svg)"></span>
								<span class="payment-icon stripe"
									style="background-image: url(assets/images/payments/payment-stripe.png)"></span>
								<span class="payment-icon verisign"
									style="background-image:  url(assets/images/payments/payment-verisign.svg)"></span>
							</div>
						</div>
					</div>
				</div><!-- End .footer-bottom -->
			</div><!-- End .container -->
		</footer><!-- End .footer -->
	</div><!-- End .page-wrapper -->

	<div class="loading-overlay">
		<div class="bounce-loader">
			<div class="bounce1"></div>
			<div class="bounce2"></div>
			<div class="bounce3"></div>
		</div>
	</div>

	<div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->

	<div class="mobile-menu-container">
		<div class="mobile-menu-wrapper">
			<span class="mobile-menu-close"><i class="fa fa-times"></i></span>
			<nav class="mobile-nav">$
				<ul class="mobile-menu">
					<li><a href="demo4.html">Home</a></li>
					<li>
						<a href="category.html">Categories</a>
						<ul>
							<li><a href="category.html">Full Width Banner</a></li>
							<li><a href="category-banner-boxed-slider.html">Boxed Slider Banner</a></li>
							<li><a href="category-banner-boxed-image.html">Boxed Image Banner</a></li>
							<li><a href="category-sidebar-left.html">Left Sidebar</a></li>
							<li><a href="category-sidebar-right.html">Right Sidebar</a></li>
							<li><a href="category-off-canvas.html">Off Canvas Filter</a></li>
							<li><a href="category-horizontal-filter1.html">Horizontal Filter 1</a></li>
							<li><a href="category-horizontal-filter2.blade.php">Horizontal Filter 2</a></li>
							<li><a href="#">List Types</a></li>
							<li><a href="category-infinite-scroll.html">Ajax Infinite Scroll<span
										class="tip tip-new">New</span></a></li>
							<li><a href="category.html">3 Columns Products</a></li>
							<li><a href="category-4col.html">4 Columns Products</a></li>
							<li><a href="category-5col.html">5 Columns Products</a></li>
							<li><a href="category-6col.html">6 Columns Products</a></li>
							<li><a href="category-7col.html">7 Columns Products</a></li>
							<li><a href="category-8col.html">8 Columns Products</a></li>
						</ul>
					</li>
					<li>
						<a href="product.html">Products</a>
						<ul>
							<li>
								<a href="#" class="nolink">PRODUCT PAGES</a>
								<ul>
									<li><a href="product.html">SIMPLE PRODUCT</a></li>
									<li><a href="product-variable.html">VARIABLE PRODUCT</a></li>
									<li><a href="product.html">SALE PRODUCT</a></li>
									<li><a href="product.html">FEATURED & ON SALE</a></li>
									<li><a href="product-sticky-info.html">WIDTH CUSTOM TAB</a></li>
									<li><a href="product-sidebar-left.html">WITH LEFT SIDEBAR</a></li>
									<li><a href="product-sidebar-right.html">WITH RIGHT SIDEBAR</a></li>
									<li><a href="product-addcart-sticky.html">ADD CART STICKY</a></li>
								</ul>
							</li>
							<li>
								<a href="#" class="nolink">PRODUCT LAYOUTS</a>
								<ul>
									<li><a href="product-extended-layout.html">EXTENDED LAYOUT</a></li>
									<li><a href="product-grid-layout.html">GRID IMAGE</a></li>
									<li><a href="product-full-width.html">FULL WIDTH LAYOUT</a></li>
									<li><a href="product-sticky-info.html">STICKY INFO</a></li>
									<li><a href="product-sticky-both.html">LEFT & RIGHT STICKY</a></li>
									<li><a href="product-transparent-image.html">TRANSPARENT IMAGE</a></li>
									<li><a href="product-center-vertical.html">CENTER VERTICAL</a></li>
									<li><a href="#">BUILD YOUR OWN</a></li>
								</ul>
							</li>
						</ul>
					</li>
					<li>
						<a href="#">Pages<span class="tip tip-hot">Hot!</span></a>
						<ul>
							<li>
								<a href="wishlist.html">Wishlist</a>
							</li>
							<li>
								<a href="cart.html">Shopping Cart</a>
							</li>
							<li>
								<a href="checkout.html">Checkout</a>
							</li>
							<li>
								<a href="dashboard.html">Dashboard</a>
							</li>
							<li>
								<a href="login.html">Login</a>
							</li>
							<li>
								<a href="forgot-password.html">Forgot Password</a>
							</li>
						</ul>
					</li>
					<li><a href="blog.html">Blog</a></li>
					<li><a href="#">Elements</a>
						<ul class="custom-scrollbar">
							<li><a href="element-accordions.html">Accordion</a></li>
							<li><a href="element-alerts.html">Alerts</a></li>
							<li><a href="element-animations.html">Animations</a></li>
							<li><a href="element-banners.html">Banners</a></li>
							<li><a href="element-buttons.html">Buttons</a></li>
							<li><a href="element-call-to-action.html">Call to Action</a></li>
							<li><a href="element-countdown.html">Count Down</a></li>
							<li><a href="element-counters.html">Counters</a></li>
							<li><a href="element-headings.html">Headings</a></li>
							<li><a href="element-icons.html">Icons</a></li>
							<li><a href="element-info-box.html">Info box</a></li>
							<li><a href="element-posts.html">Posts</a></li>
							<li><a href="element-products.html">Products</a></li>
							<li><a href="element-product-categories.html">Product Categories</a></li>
							<li><a href="element-tabs.html">Tabs</a></li>
							<li><a href="element-testimonial.html">Testimonials</a></li>
						</ul>
					</li>
				</ul>

				<ul class="mobile-menu mt-2 mb-2">
					<li class="border-0">
						<a href="#">
							Special Offer!
						</a>
					</li>
					<li class="border-0">
						<a href="#" target="_blank">
							Buy X!
							<span class="tip tip-hot">Hot</span>
						</a>
					</li>
				</ul>

				<ul class="mobile-menu">
					<li><a href="login.html">My Account</a></li>
					<li><a href="contact.html">Contact Us</a></li>
					<li><a href="blog.html">Blog</a></li>
					<li><a href="wishlist.html">My Wishlist</a></li>
					<li><a href="cart.html">Cart</a></li>
					<li><a href="login.html" class="login-link">Log In</a></li>
				</ul>
			</nav><!-- End .mobile-nav -->

			<form class="search-wrapper mb-2" action="#">
				<input type="text" class="form-control mb-0" placeholder="Search..." required />
				<button class="btn icon-search text-white bg-transparent p-0" type="submit"></button>
			</form>

			<div class="social-icons">
				<a href="#" class="social-icon social-facebook icon-facebook" target="_blank">
				</a>
				<a href="#" class="social-icon social-twitter icon-twitter" target="_blank">
				</a>
				<a href="#" class="social-icon social-instagram icon-instagram" target="_blank">
				</a>
			</div>
		</div><!-- End .mobile-menu-wrapper -->
	</div><!-- End .mobile-menu-container -->

	<div class="sticky-navbar">
		<div class="sticky-info">
			<a href="/">
				<i class="icon-home"></i>Home
			</a>
		</div>
		<div class="sticky-info">
			<a href="/categoires" class="">
				<i class="icon-bars"></i>Categories
			</a>
		</div>
		<div class="sticky-info">
			<a href="wishlist.html" class="">
				<i class="icon-wishlist-2"></i>Wishlist
			</a>
		</div>
		<div class="sticky-info">
			<a href="login.html" class="">
				<i class="icon-user-2"></i>Account
			</a>
		</div>
		<div class="sticky-info">
			<a href="cart.html" class="">
				<i class="icon-shopping-cart position-relative">
					<span class="cart-count badge-circle">3</span>
				</i>Cart
			</a>
		</div>
	</div>



	<a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

	<!-- Plugins JS File -->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/plugins.min.js"></script>

	<!-- Main JS File -->
	<script src="assets/js/main.min.js"></script>
@livewireScripts
</body>

</html>
