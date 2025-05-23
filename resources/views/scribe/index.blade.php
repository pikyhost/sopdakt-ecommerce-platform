<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>SOPDAKT API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "https://backend.sopdakt.com";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.2.1.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.2.1.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-about-us" class="tocify-header">
                <li class="tocify-item level-1" data-unique="about-us">
                    <a href="#about-us">About Us</a>
                </li>
                                    <ul id="tocify-subheader-about-us" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="about-us-GETapi-about-us">
                                <a href="#about-us-GETapi-about-us">Retrieve About Us page data</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-authentication" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authentication">
                    <a href="#authentication">Authentication</a>
                </li>
                                    <ul id="tocify-subheader-authentication" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="authentication-POSTapi-login">
                                <a href="#authentication-POSTapi-login">Handle an incoming authentication request</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="authentication-POSTapi-logout">
                                <a href="#authentication-POSTapi-logout">Destroy an authenticated session</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-banners" class="tocify-header">
                <li class="tocify-item level-1" data-unique="banners">
                    <a href="#banners">Banners</a>
                </li>
                                    <ul id="tocify-subheader-banners" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="banners-GETapi-banners-product">
                                <a href="#banners-GETapi-banners-product">Retrieve banners of type 'product'</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="banners-GETapi-banners-category">
                                <a href="#banners-GETapi-banners-category">Retrieve banners of type 'category'</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-cart" class="tocify-header">
                <li class="tocify-item level-1" data-unique="cart">
                    <a href="#cart">Cart</a>
                </li>
                                    <ul id="tocify-subheader-cart" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="cart-POSTapi-cart">
                                <a href="#cart-POSTapi-cart">Add Item to Cart</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-PUTapi-cart--itemId-">
                                <a href="#cart-PUTapi-cart--itemId-">Update Cart Item Quantity</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-DELETEapi-cart--itemId-">
                                <a href="#cart-DELETEapi-cart--itemId-">Remove Item from Cart</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-cart-management" class="tocify-header">
                <li class="tocify-item level-1" data-unique="cart-management">
                    <a href="#cart-management">Cart Management</a>
                </li>
                                    <ul id="tocify-subheader-cart-management" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="cart-management-GETapi-cart">
                                <a href="#cart-management-GETapi-cart">Get the user's cart</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-management-POSTapi-cart-shipping">
                                <a href="#cart-management-POSTapi-cart-shipping">Update cart shipping and location details</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-management-POSTapi-cart-item--cartItemId--quantity">
                                <a href="#cart-management-POSTapi-cart-item--cartItemId--quantity">Update cart item quantity</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-management-DELETEapi-cart-item--cartItemId-">
                                <a href="#cart-management-DELETEapi-cart-item--cartItemId-">Remove a cart item</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="cart-management-POSTapi-cart-checkout">
                                <a href="#cart-management-POSTapi-cart-checkout">Proceed to checkout</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-categories" class="tocify-header">
                <li class="tocify-item level-1" data-unique="categories">
                    <a href="#categories">Categories</a>
                </li>
                                    <ul id="tocify-subheader-categories" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="categories-GETapi-categories--slug-">
                                <a href="#categories-GETapi-categories--slug-">Show Category with Products</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-checkout" class="tocify-header">
                <li class="tocify-item level-1" data-unique="checkout">
                    <a href="#checkout">Checkout</a>
                </li>
                                    <ul id="tocify-subheader-checkout" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="checkout-POSTapi-checkout">
                                <a href="#checkout-POSTapi-checkout">Process the checkout and place an order</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-contact-messages" class="tocify-header">
                <li class="tocify-item level-1" data-unique="contact-messages">
                    <a href="#contact-messages">Contact Messages</a>
                </li>
                                    <ul id="tocify-subheader-contact-messages" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="contact-messages-POSTapi-contact">
                                <a href="#contact-messages-POSTapi-contact">Store a new contact message</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-contact-settings" class="tocify-header">
                <li class="tocify-item level-1" data-unique="contact-settings">
                    <a href="#contact-settings">Contact Settings</a>
                </li>
                                    <ul id="tocify-subheader-contact-settings" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="contact-settings-GETapi-contact-settings">
                                <a href="#contact-settings-GETapi-contact-settings">Retrieve all contact settings</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-endpoints" class="tocify-header">
                <li class="tocify-item level-1" data-unique="endpoints">
                    <a href="#endpoints">Endpoints</a>
                </li>
                                    <ul id="tocify-subheader-endpoints" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="endpoints-POSTapi-register">
                                <a href="#endpoints-POSTapi-register">POST api/register</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-forgot-password">
                                <a href="#endpoints-POSTapi-forgot-password">Handle an incoming password reset link request.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-reset-password">
                                <a href="#endpoints-POSTapi-reset-password">Handle an incoming new password request.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-verify-email--id---hash-">
                                <a href="#endpoints-GETapi-verify-email--id---hash-">Mark the authenticated user's email address as verified.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-email-verification-notification">
                                <a href="#endpoints-POSTapi-email-verification-notification">Send a new email verification notification.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-user">
                                <a href="#endpoints-GETapi-user">GET api/user</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-jt-express-webhook">
                                <a href="#endpoints-POSTapi-jt-express-webhook">POST api/jt-express-webhook</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-bosta-webhook">
                                <a href="#endpoints-POSTapi-bosta-webhook">POST api/bosta/webhook</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-aramex-webhook">
                                <a href="#endpoints-POSTapi-aramex-webhook">Webhook for ARAMEX to update shipment status</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-aramex-orders--order_id--create-shipment">
                                <a href="#endpoints-POSTapi-aramex-orders--order_id--create-shipment">Create ARAMEX shipment for an order</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-aramex-orders--order_id--track-shipment">
                                <a href="#endpoints-GETapi-aramex-orders--order_id--track-shipment">Track ARAMEX shipment</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-payment-process">
                                <a href="#endpoints-POSTapi-payment-process">POST api/payment/process</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-payment-callback">
                                <a href="#endpoints-GETapi-payment-callback">GET api/payment/callback</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-categories">
                                <a href="#endpoints-GETapi-categories">GET api/categories</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-products-realBestSellers">
                                <a href="#endpoints-GETapi-products-realBestSellers">Get the real top 10 best-selling products based on the total quantity sold.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-homepage-slider">
                                <a href="#endpoints-GETapi-homepage-slider">Get homepage slider and CTA content.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-wishlist-toggle">
                                <a href="#endpoints-POSTapi-wishlist-toggle">POST api/wishlist/toggle</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-wishlist">
                                <a href="#endpoints-GETapi-wishlist">GET api/wishlist</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-orders">
                                <a href="#endpoints-GETapi-orders">List all orders for the authenticated user.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-orders-track--tracking_number-">
                                <a href="#endpoints-GETapi-orders-track--tracking_number-">Track a specific order by its tracking number.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-orders--order_id-">
                                <a href="#endpoints-PUTapi-orders--order_id-">Update the order (only if it belongs to the authenticated user).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-orders--order_id-">
                                <a href="#endpoints-DELETEapi-orders--order_id-">Delete the order (only if it belongs to the authenticated user).</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-discounts">
                                <a href="#endpoints-GETapi-discounts">Display a paginated list of active discounts with filtering, sorting, and search.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-newsletter-subscribe">
                                <a href="#endpoints-POSTapi-newsletter-subscribe">Store a new newsletter subscriber.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-recommended-products">
                                <a href="#endpoints-GETapi-recommended-products">Retrieve a list of recommended products.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs">
                                <a href="#endpoints-GETapi-blogs">Retrieve a paginated list of active blogs.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-categories">
                                <a href="#endpoints-GETapi-blogs-categories">Retrieve all active blog categories with their active blogs count.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-popular">
                                <a href="#endpoints-GETapi-blogs-popular">Retrieve popular blogs based on likes count.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-recent">
                                <a href="#endpoints-GETapi-blogs-recent">Retrieve recent blogs based on publication date.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-search">
                                <a href="#endpoints-GETapi-blogs-search">Search blogs by title or content.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-category--categorySlug-">
                                <a href="#endpoints-GETapi-blogs-category--categorySlug-">Retrieve paginated blogs by category slug.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs-tag--tagId-">
                                <a href="#endpoints-GETapi-blogs-tag--tagId-">Retrieve paginated blogs by tag ID.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-blogs--slug-">
                                <a href="#endpoints-GETapi-blogs--slug-">Retrieve a single blog post by its slug.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-blogs--blogId--like">
                                <a href="#endpoints-POSTapi-blogs--blogId--like">Toggle like status for a blog post.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-global-search">
                                <a href="#endpoints-GETapi-global-search">Global Search API</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-top-bars">
                                <a href="#endpoints-GETapi-top-bars">GET api/top-bars</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-footer-contact-info">
                                <a href="#endpoints-GETapi-footer-contact-info">GET api/footer/contact-info</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-service-features">
                                <a href="#endpoints-GETapi-service-features">GET api/service-features</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-GETapi-products--product_slug--ratings">
                                <a href="#endpoints-GETapi-products--product_slug--ratings">GET api/products/{product_slug}/ratings</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-POSTapi-products--product_slug--ratings">
                                <a href="#endpoints-POSTapi-products--product_slug--ratings">POST api/products/{product_slug}/ratings</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-PUTapi-products--product_slug--ratings--rating_id-">
                                <a href="#endpoints-PUTapi-products--product_slug--ratings--rating_id-">PUT api/products/{product_slug}/ratings/{rating_id}</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="endpoints-DELETEapi-products--product_slug--ratings--rating_id-">
                                <a href="#endpoints-DELETEapi-products--product_slug--ratings--rating_id-">DELETE api/products/{product_slug}/ratings/{rating_id}</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-homepage" class="tocify-header">
                <li class="tocify-item level-1" data-unique="homepage">
                    <a href="#homepage">Homepage</a>
                </li>
                                    <ul id="tocify-subheader-homepage" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="homepage-GETapi-home-featured-categories">
                                <a href="#homepage-GETapi-home-featured-categories">Get Featured Categories</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="homepage-GETapi-products-fakeBestSellers">
                                <a href="#homepage-GETapi-products-fakeBestSellers">Get Best Selling Products</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-policies" class="tocify-header">
                <li class="tocify-item level-1" data-unique="policies">
                    <a href="#policies">Policies</a>
                </li>
                                    <ul id="tocify-subheader-policies" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="policies-GETapi-policies-privacy">
                                <a href="#policies-GETapi-policies-privacy">Retrieve the Privacy Policy</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="policies-GETapi-policies-refund">
                                <a href="#policies-GETapi-policies-refund">Retrieve the Refund Policy</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="policies-GETapi-policies-terms">
                                <a href="#policies-GETapi-policies-terms">Retrieve the Terms of Service</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="policies-GETapi-policies-shipping">
                                <a href="#policies-GETapi-policies-shipping">Retrieve the Shipping Policy</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-popups" class="tocify-header">
                <li class="tocify-item level-1" data-unique="popups">
                    <a href="#popups">Popups</a>
                </li>
                                    <ul id="tocify-subheader-popups" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="popups-GETapi-popups">
                                <a href="#popups-GETapi-popups">Retrieve all active popups</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-products" class="tocify-header">
                <li class="tocify-item level-1" data-unique="products">
                    <a href="#products">Products</a>
                </li>
                                    <ul id="tocify-subheader-products" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="products-GETapi-products--id--colors-sizes">
                                <a href="#products-GETapi-products--id--colors-sizes">Get all color and size variants for a product.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="products-POSTapi-compare">
                                <a href="#products-POSTapi-compare">Compare Products</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="products-GETapi-products-featured">
                                <a href="#products-GETapi-products-featured">Get up to 3 featured published products.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="products-GETapi-products--slug-">
                                <a href="#products-GETapi-products--slug-">Get a single published product by its slug.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="products-GETapi-all-products">
                                <a href="#products-GETapi-all-products">Get all active products with filtering and pagination

This endpoint returns a paginated list of all published products with their details, variants, and available filters.
Products can be filtered by various criteria like color, size, category, and rating.</a>
                            </li>
                                                                        </ul>
                            </ul>
                    <ul id="tocify-header-wheel-of-fortune" class="tocify-header">
                <li class="tocify-item level-1" data-unique="wheel-of-fortune">
                    <a href="#wheel-of-fortune">Wheel of Fortune</a>
                </li>
                                    <ul id="tocify-subheader-wheel-of-fortune" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="wheel-of-fortune-GETapi-wheel">
                                <a href="#wheel-of-fortune-GETapi-wheel">Get the active wheel and its prizes</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="wheel-of-fortune-POSTapi-wheel-spin">
                                <a href="#wheel-of-fortune-POSTapi-wheel-spin">Spin the wheel of fortune</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: May 20, 2025</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<aside>
    <strong>Base URL</strong>: <code>https://backend.sopdakt.com</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="about-us">About Us</h1>

    

                                <h2 id="about-us-GETapi-about-us">Retrieve About Us page data</h2>

<p>
</p>

<p>This endpoint fetches the About Us page content, including header, breadcrumbs, about section,
accordion, team members, testimonials, and SEO metadata. The response is localized based on the
current application locale (en or ar). If no data is found or the data is empty, an error message
is returned indicating that the content is null or empty.</p>

<span id="example-requests-GETapi-about-us">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/about-us" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/about-us"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-about-us">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;direction&quot;: &quot;ltr&quot;,
        &quot;header&quot;: {
            &quot;title&quot;: &quot;Who we are&quot;,
            &quot;subtitle&quot;: null,
            &quot;background_image&quot;: &quot;https://your-domain.com/assets/images/demoes/demo12/page-header-bg.jpg&quot;
        },
        &quot;breadcrumbs&quot;: {
            &quot;home&quot;: {
                &quot;title&quot;: &quot;Home&quot;,
                &quot;url&quot;: &quot;https://your-domain.com/&quot;
            },
            &quot;current&quot;: {
                &quot;title&quot;: &quot;About Us&quot;
            }
        },
        &quot;about&quot;: {
            &quot;title&quot;: &quot;About Us&quot;,
            &quot;description_1&quot;: &quot;We are a forward-thinking company...&quot;,
            &quot;description_2&quot;: &quot;Our goal is to innovate and lead...&quot;,
            &quot;image&quot;: &quot;https://your-domain.com/storage/about/history.jpg&quot;
        },
        &quot;accordion&quot;: [
            {
                &quot;title&quot;: &quot;Company History&quot;,
                &quot;content&quot;: &quot;We started in 2005 with a mission...&quot;,
                &quot;is_open&quot;: true
            },
            {
                &quot;title&quot;: &quot;Our Vision&quot;,
                &quot;content&quot;: &quot;To be the best in class...&quot;,
                &quot;is_open&quot;: false
            },
            {
                &quot;title&quot;: &quot;Our Mission&quot;,
                &quot;content&quot;: &quot;To deliver quality products...&quot;,
                &quot;is_open&quot;: false
            },
            {
                &quot;title&quot;: &quot;Fun Facts&quot;,
                &quot;content&quot;: &quot;We&rsquo;ve served over 1M users...&quot;,
                &quot;is_open&quot;: false
            }
        ],
        &quot;team&quot;: {
            &quot;title&quot;: &quot;Team&quot;,
            &quot;members&quot;: [
                {
                    &quot;name&quot;: &quot;John Doe&quot;,
                    &quot;image&quot;: &quot;https://your-domain.com/storage/team/team1.jpg&quot;
                },
                {
                    &quot;name&quot;: &quot;Jessica Doe&quot;,
                    &quot;image&quot;: &quot;https://your-domain.com/storage/team/team2.jpg&quot;
                }
            ],
            &quot;cta&quot;: {
                &quot;text&quot;: &quot;Join Our Team&quot;,
                &quot;url&quot;: &quot;https://example.com/join&quot;
            }
        },
        &quot;testimonials&quot;: {
            &quot;title&quot;: &quot;Testimonials&quot;,
            &quot;items&quot;: [
                {
                    &quot;content&quot;: &quot;Long established fact...&quot;,
                    &quot;name&quot;: &quot;Ahmed Mohsen&quot;,
                    &quot;role&quot;: &quot;Role X&quot;,
                    &quot;image&quot;: &quot;https://your-domain.com/storage/clients/client1.jpg&quot;,
                    &quot;rating&quot;: 5
                }
            ]
        },
        &quot;seo&quot;: {
            &quot;meta_title&quot;: &quot;About Us&quot;,
            &quot;meta_description&quot;: &quot;Learn about our company and team.&quot;
        }
    },
    &quot;message&quot;: &quot;About Us data retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;About Us data is null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving About Us data. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-about-us" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-about-us"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-about-us"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-about-us" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-about-us">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-about-us" data-method="GET"
      data-path="api/about-us"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-about-us', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-about-us"
                    onclick="tryItOut('GETapi-about-us');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-about-us"
                    onclick="cancelTryOut('GETapi-about-us');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-about-us"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/about-us</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-about-us"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-about-us"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="authentication">Authentication</h1>

    <p>APIs for handling user authentication, including login and logout operations.</p>

                                <h2 id="authentication-POSTapi-login">Handle an incoming authentication request</h2>

<p>
</p>

<p>Authenticates a user with the provided credentials. If successful, regenerates the session and returns user data. Returns an error if the user is already logged in or if credentials are invalid.</p>

<span id="example-requests-POSTapi-login">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/login" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"user@example.com\",
    \"password\": \"password123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/login"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "user@example.com",
    "password": "password123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-login">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Login successful&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;email&quot;: &quot;user@example.com&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Authentication failed&quot;,
    &quot;error&quot;: &quot;Invalid credentials&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (409):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;User is already logged in&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;email&quot;: &quot;user@example.com&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Validation failed&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email field is required.&quot;
        ],
        &quot;password&quot;: [
            &quot;The password field is required.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-login" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-login"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-login"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-login" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-login">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-login" data-method="POST"
      data-path="api/login"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-login', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-login"
                    onclick="tryItOut('POSTapi-login');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-login"
                    onclick="cancelTryOut('POSTapi-login');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-login"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/login</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-login"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-login"
               value="user@example.com"
               data-component="body">
    <br>
<p>The user's email address. Example: <code>user@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-login"
               value="password123"
               data-component="body">
    <br>
<p>The user's password. Example: <code>password123</code></p>
        </div>
        </form>

                    <h2 id="authentication-POSTapi-logout">Destroy an authenticated session</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Logs out the authenticated Rectangular user, invalidates the session, and regenerates the CSRF token. Returns an error if no active session is found or if logout attempts are rate-limited.</p>

<span id="example-requests-POSTapi-logout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/logout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/logout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-logout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Logout successful&quot;,
    &quot;user&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;email&quot;: &quot;user@example.com&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;No active session found&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (429):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Too many logout attempts. Please try again later.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Logout failed&quot;,
    &quot;error&quot;: &quot;An unexpected error occurred&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-logout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-logout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-logout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-logout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-logout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-logout" data-method="POST"
      data-path="api/logout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-logout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-logout"
                    onclick="tryItOut('POSTapi-logout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-logout"
                    onclick="cancelTryOut('POSTapi-logout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-logout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/logout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-logout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="banners">Banners</h1>

    

                                <h2 id="banners-GETapi-banners-product">Retrieve banners of type &#039;product&#039;</h2>

<p>
</p>

<p>This endpoint fetches all banners with the type 'product' in the current application locale (English or Arabic).
The response includes an array of banners with their title, subtitle, discount, button text, button URL, image URL,
and type. Translatable fields (title, subtitle, discount, button_text) are returned in the current locale.
If no product banners are found, an error message is returned indicating that the banners are null or empty.</p>

<span id="example-requests-GETapi-banners-product">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/banners/product" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/banners/product"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-banners-product">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;50% OFF&quot;,
            &quot;subtitle&quot;: &quot;UP TO&quot;,
            &quot;discount&quot;: &quot;50%&quot;,
            &quot;button_text&quot;: &quot;SHOP NOW&quot;,
            &quot;button_url&quot;: &quot;/shop&quot;,
            &quot;image&quot;: &quot;https://your-domain.com/assets/images/menu-banner.jpg&quot;,
            &quot;type&quot;: &quot;product&quot;,
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        }
    ],
    &quot;message&quot;: &quot;Product banners retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;خصم 50%&quot;,
            &quot;subtitle&quot;: &quot;حتى&quot;,
            &quot;discount&quot;: &quot;٥٠٪&quot;,
            &quot;button_text&quot;: &quot;تسوق الآن&quot;,
            &quot;button_url&quot;: &quot;/shop&quot;,
            &quot;image&quot;: &quot;https://your-domain.com/assets/images/menu-banner.jpg&quot;,
            &quot;type&quot;: &quot;product&quot;,
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        }
    ],
    &quot;message&quot;: &quot;Product banners retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No product banners found&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving product banners. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-banners-product" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-banners-product"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-banners-product"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-banners-product" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-banners-product">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-banners-product" data-method="GET"
      data-path="api/banners/product"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-banners-product', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-banners-product"
                    onclick="tryItOut('GETapi-banners-product');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-banners-product"
                    onclick="cancelTryOut('GETapi-banners-product');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-banners-product"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/banners/product</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-banners-product"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-banners-product"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="banners-GETapi-banners-category">Retrieve banners of type &#039;category&#039;</h2>

<p>
</p>

<p>This endpoint fetches all banners with the type 'category' in the current application locale (English or Arabic).
The response includes an array of banners with their title, subtitle, discount, button text, button URL, image URL,
and type. Translatable fields (title, subtitle, discount, button_text) are returned in the current locale.
If no category banners are found, an error message is returned indicating that the banners are null or empty.</p>

<span id="example-requests-GETapi-banners-category">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/banners/category" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/banners/category"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-banners-category">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;New Arrivals&quot;,
            &quot;subtitle&quot;: &quot;EXPLORE&quot;,
            &quot;discount&quot;: null,
            &quot;button_text&quot;: &quot;VIEW CATEGORIES&quot;,
            &quot;button_url&quot;: &quot;/categories&quot;,
            &quot;image&quot;: &quot;https://your-domain.com/assets/images/category-banner.jpg&quot;,
            &quot;type&quot;: &quot;category&quot;,
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        }
    ],
    &quot;message&quot;: &quot;Category banners retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;وصل حديثاً&quot;,
            &quot;subtitle&quot;: &quot;استكشاف&quot;,
            &quot;discount&quot;: null,
            &quot;button_text&quot;: &quot;عرض الفئات&quot;,
            &quot;button_url&quot;: &quot;/categories&quot;,
            &quot;image&quot;: &quot;https://your-domain.com/assets/images/category-banner.jpg&quot;,
            &quot;type&quot;: &quot;category&quot;,
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        }
    ],
    &quot;message&quot;: &quot;Category banners retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No category banners found&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving category banners. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-banners-category" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-banners-category"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-banners-category"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-banners-category" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-banners-category">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-banners-category" data-method="GET"
      data-path="api/banners/category"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-banners-category', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-banners-category"
                    onclick="tryItOut('GETapi-banners-category');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-banners-category"
                    onclick="cancelTryOut('GETapi-banners-category');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-banners-category"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/banners/category</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-banners-category"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-banners-category"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="cart">Cart</h1>

    <p>Adds a product to the cart with optional color/size selection.</p>

                                <h2 id="cart-POSTapi-cart">Add Item to Cart</h2>

<p>
</p>



<span id="example-requests-POSTapi-cart">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/cart" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"product_id\": 1,
    \"quantity\": 2,
    \"color_id\": 3,
    \"size_id\": 5
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "product_id": 1,
    "quantity": 2,
    "color_id": 3,
    "size_id": 5
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-cart">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Product added to cart successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Please select a color.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Please select a size.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Selected variant not available.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;This product is out of stock!&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Requested quantity exceeds stock!&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-cart" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-cart"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-cart"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-cart" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-cart">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-cart" data-method="POST"
      data-path="api/cart"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-cart', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-cart"
                    onclick="tryItOut('POSTapi-cart');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-cart"
                    onclick="cancelTryOut('POSTapi-cart');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-cart"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/cart</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-cart"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-cart"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>product_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="product_id"                data-endpoint="POSTapi-cart"
               value="1"
               data-component="body">
    <br>
<p>The ID of the product to add. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="POSTapi-cart"
               value="2"
               data-component="body">
    <br>
<p>Quantity (1-10). Example: <code>2</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>color_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="color_id"                data-endpoint="POSTapi-cart"
               value="3"
               data-component="body">
    <br>
<p>nullable ID of selected color (required if product has colors). Example: <code>3</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>size_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="size_id"                data-endpoint="POSTapi-cart"
               value="5"
               data-component="body">
    <br>
<p>nullable ID of selected size (required if product has sizes for selected color). Example: <code>5</code></p>
        </div>
        </form>

                    <h2 id="cart-PUTapi-cart--itemId-">Update Cart Item Quantity</h2>

<p>
</p>



<span id="example-requests-PUTapi-cart--itemId-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "https://backend.sopdakt.com/api/cart/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"quantity\": 3
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "quantity": 3
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-cart--itemId-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Cart item updated successfully.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Item not found.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Requested quantity exceeds stock!&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-PUTapi-cart--itemId-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-cart--itemId-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-cart--itemId-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-cart--itemId-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-cart--itemId-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-cart--itemId-" data-method="PUT"
      data-path="api/cart/{itemId}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-cart--itemId-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-cart--itemId-"
                    onclick="tryItOut('PUTapi-cart--itemId-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-cart--itemId-"
                    onclick="cancelTryOut('PUTapi-cart--itemId-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-cart--itemId-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/cart/{itemId}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-cart--itemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-cart--itemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>itemId</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="itemId"                data-endpoint="PUTapi-cart--itemId-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the cart item to update. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>quantity</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="quantity"                data-endpoint="PUTapi-cart--itemId-"
               value="3"
               data-component="body">
    <br>
<p>New quantity (1-10). Example: <code>3</code></p>
        </div>
        </form>

                    <h2 id="cart-DELETEapi-cart--itemId-">Remove Item from Cart</h2>

<p>
</p>



<span id="example-requests-DELETEapi-cart--itemId-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "https://backend.sopdakt.com/api/cart/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-cart--itemId-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Item removed from cart.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Item not found.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-cart--itemId-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-cart--itemId-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-cart--itemId-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-cart--itemId-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-cart--itemId-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-cart--itemId-" data-method="DELETE"
      data-path="api/cart/{itemId}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-cart--itemId-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-cart--itemId-"
                    onclick="tryItOut('DELETEapi-cart--itemId-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-cart--itemId-"
                    onclick="cancelTryOut('DELETEapi-cart--itemId-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-cart--itemId-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/cart/{itemId}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-cart--itemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-cart--itemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>itemId</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="itemId"                data-endpoint="DELETEapi-cart--itemId-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the cart item to remove. Example: <code>1</code></p>
            </div>
                    </form>

                <h1 id="cart-management">Cart Management</h1>

    <p>APIs for managing the shopping cart, including viewing cart contents, updating shipping details, modifying item quantities, removing items, and proceeding to checkout.</p>

                                <h2 id="cart-management-GETapi-cart">Get the user&#039;s cart</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Retrieves the current user's cart or a session-based cart if the user is not authenticated. Includes cart items, totals, shipping options, and complementary products.</p>

<span id="example-requests-GETapi-cart">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/cart" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"coupon_code\": \"SUMMER20\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "coupon_code": "SUMMER20"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-cart">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;cart&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;session_id&quot;: null,
        &quot;subtotal&quot;: 99.9899999999999948840923025272786617279052734375,
        &quot;total&quot;: 109.9899999999999948840923025272786617279052734375,
        &quot;tax_percentage&quot;: 5,
        &quot;tax_amount&quot;: 5,
        &quot;shipping_cost&quot;: 5,
        &quot;country_id&quot;: 1,
        &quot;governorate_id&quot;: 1,
        &quot;city_id&quot;: 1,
        &quot;shipping_type_id&quot;: 1
    },
    &quot;cartItems&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;cart_id&quot;: 1,
            &quot;product_id&quot;: 1,
            &quot;quantity&quot;: 2,
            &quot;subtotal&quot;: 49.97999999999999687361196265555918216705322265625,
            &quot;product&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Sample Product&quot;,
                &quot;slug&quot;: &quot;sample-product&quot;,
                &quot;discount_price_for_current_country&quot;: &quot;24.99 USD&quot;
            }
        }
    ],
    &quot;totals&quot;: {
        &quot;subtotal&quot;: 99.9899999999999948840923025272786617279052734375,
        &quot;shipping_cost&quot;: 5,
        &quot;tax&quot;: 5,
        &quot;total&quot;: 109.9899999999999948840923025272786617279052734375,
        &quot;currency&quot;: &quot;USD&quot;,
        &quot;free_shipping_applied&quot;: false,
        &quot;discount_applied&quot;: 10
    },
    &quot;countries&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;USA&quot;,
            &quot;cost&quot;: 5
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Canada&quot;,
            &quot;cost&quot;: 7
        }
    ],
    &quot;governorates&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;California&quot;,
            &quot;country_id&quot;: 1
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Ontario&quot;,
            &quot;country_id&quot;: 2
        }
    ],
    &quot;cities&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Los Angeles&quot;,
            &quot;governorate_id&quot;: 1
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Toronto&quot;,
            &quot;governorate_id&quot;: 2
        }
    ],
    &quot;shipping_types&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Standard Shipping&quot;,
            &quot;cost&quot;: 5,
            &quot;status&quot;: true
        },
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Express Shipping&quot;,
            &quot;cost&quot;: 10,
            &quot;status&quot;: true
        }
    ],
    &quot;complementary_products&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;name&quot;: &quot;Complementary Product&quot;,
            &quot;slug&quot;: &quot;complementary-product&quot;,
            &quot;discount_price_for_current_country&quot;: &quot;19.99 USD&quot;
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-cart" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-cart"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-cart"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-cart" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-cart">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-cart" data-method="GET"
      data-path="api/cart"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-cart', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-cart"
                    onclick="tryItOut('GETapi-cart');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-cart"
                    onclick="cancelTryOut('GETapi-cart');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-cart"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/cart</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-cart"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-cart"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>coupon_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="coupon_code"                data-endpoint="GETapi-cart"
               value="SUMMER20"
               data-component="body">
    <br>
<p>nullable Coupon code to apply. Example: <code>SUMMER20</code></p>
        </div>
        </form>

                    <h2 id="cart-management-POSTapi-cart-shipping">Update cart shipping and location details</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Updates the cart's shipping and location details (country, governorate, city, and shipping type). Returns updated cart details and dependent location data.</p>

<span id="example-requests-POSTapi-cart-shipping">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/cart/shipping" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"country_id\": 1,
    \"governorate_id\": 1,
    \"city_id\": 1,
    \"shipping_type_id\": 1,
    \"coupon_code\": \"SUMMER20\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/shipping"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "country_id": 1,
    "governorate_id": 1,
    "city_id": 1,
    "shipping_type_id": 1,
    "coupon_code": "SUMMER20"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-cart-shipping">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;cart&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;session_id&quot;: null,
        &quot;subtotal&quot;: 99.9899999999999948840923025272786617279052734375,
        &quot;total&quot;: 109.9899999999999948840923025272786617279052734375,
        &quot;tax_percentage&quot;: 5,
        &quot;tax_amount&quot;: 5,
        &quot;shipping_cost&quot;: 5,
        &quot;country_id&quot;: 1,
        &quot;governorate_id&quot;: 1,
        &quot;city_id&quot;: 1,
        &quot;shipping_type_id&quot;: 1
    },
    &quot;cartItems&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;cart_id&quot;: 1,
            &quot;product_id&quot;: 1,
            &quot;quantity&quot;: 2,
            &quot;subtotal&quot;: 49.97999999999999687361196265555918216705322265625,
            &quot;product&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Sample Product&quot;,
                &quot;slug&quot;: &quot;sample-product&quot;,
                &quot;discount_price_for_current_country&quot;: &quot;24.99 USD&quot;
            }
        }
    ],
    &quot;totals&quot;: {
        &quot;subtotal&quot;: 99.9899999999999948840923025272786617279052734375,
        &quot;shipping_cost&quot;: 5,
        &quot;tax&quot;: 5,
        &quot;total&quot;: 109.9899999999999948840923025272786617279052734375,
        &quot;currency&quot;: &quot;USD&quot;,
        &quot;free_shipping_applied&quot;: false,
        &quot;discount_applied&quot;: 10
    },
    &quot;governorates&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;California&quot;,
            &quot;country_id&quot;: 1
        }
    ],
    &quot;cities&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Los Angeles&quot;,
            &quot;governorate_id&quot;: 1
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;errors&quot;: {
        &quot;country_id&quot;: [
            &quot;The selected country id is invalid.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-cart-shipping" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-cart-shipping"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-cart-shipping"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-cart-shipping" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-cart-shipping">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-cart-shipping" data-method="POST"
      data-path="api/cart/shipping"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-cart-shipping', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-cart-shipping"
                    onclick="tryItOut('POSTapi-cart-shipping');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-cart-shipping"
                    onclick="cancelTryOut('POSTapi-cart-shipping');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-cart-shipping"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/cart/shipping</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-cart-shipping"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-cart-shipping"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="country_id"                data-endpoint="POSTapi-cart-shipping"
               value="1"
               data-component="body">
    <br>
<p>nullable The ID of the country. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>governorate_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="governorate_id"                data-endpoint="POSTapi-cart-shipping"
               value="1"
               data-component="body">
    <br>
<p>nullable The ID of the governorate. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>city_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="city_id"                data-endpoint="POSTapi-cart-shipping"
               value="1"
               data-component="body">
    <br>
<p>nullable The ID of the city. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>shipping_type_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="shipping_type_id"                data-endpoint="POSTapi-cart-shipping"
               value="1"
               data-component="body">
    <br>
<p>nullable The ID of the shipping type. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>coupon_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="coupon_code"                data-endpoint="POSTapi-cart-shipping"
               value="SUMMER20"
               data-component="body">
    <br>
<p>nullable Coupon code to apply. Example: <code>SUMMER20</code></p>
        </div>
        </form>

                    <h2 id="cart-management-POSTapi-cart-item--cartItemId--quantity">Update cart item quantity</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Increases or decreases the quantity of a specific cart item. If the quantity reaches 0, the item is removed.</p>

<span id="example-requests-POSTapi-cart-item--cartItemId--quantity">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/cart/item/1/quantity" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"action\": \"increase\",
    \"coupon_code\": \"SUMMER20\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/item/1/quantity"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "action": "increase",
    "coupon_code": "SUMMER20"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-cart-item--cartItemId--quantity">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;cart&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;session_id&quot;: null,
        &quot;subtotal&quot;: 74.969999999999998863131622783839702606201171875,
        &quot;total&quot;: 82.469999999999998863131622783839702606201171875,
        &quot;tax_percentage&quot;: 5,
        &quot;tax_amount&quot;: 3.75,
        &quot;shipping_cost&quot;: 5
    },
    &quot;cartItems&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;cart_id&quot;: 1,
            &quot;product_id&quot;: 1,
            &quot;quantity&quot;: 3,
            &quot;subtotal&quot;: 74.969999999999998863131622783839702606201171875,
            &quot;product&quot;: {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Sample Product&quot;,
                &quot;slug&quot;: &quot;sample-product&quot;,
                &quot;discount_price_for_current_country&quot;: &quot;24.99 USD&quot;
            }
        }
    ],
    &quot;totals&quot;: {
        &quot;subtotal&quot;: 74.969999999999998863131622783839702606201171875,
        &quot;shipping_cost&quot;: 5,
        &quot;tax&quot;: 3.75,
        &quot;total&quot;: 82.469999999999998863131622783839702606201171875,
        &quot;currency&quot;: &quot;USD&quot;,
        &quot;free_shipping_applied&quot;: false,
        &quot;discount_applied&quot;: 7.5
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Cart item not found&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;errors&quot;: {
        &quot;action&quot;: [
            &quot;The action must be increase or decrease.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-cart-item--cartItemId--quantity" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-cart-item--cartItemId--quantity"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-cart-item--cartItemId--quantity"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-cart-item--cartItemId--quantity" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-cart-item--cartItemId--quantity">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-cart-item--cartItemId--quantity" data-method="POST"
      data-path="api/cart/item/{cartItemId}/quantity"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-cart-item--cartItemId--quantity', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-cart-item--cartItemId--quantity"
                    onclick="tryItOut('POSTapi-cart-item--cartItemId--quantity');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-cart-item--cartItemId--quantity"
                    onclick="cancelTryOut('POSTapi-cart-item--cartItemId--quantity');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-cart-item--cartItemId--quantity"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/cart/item/{cartItemId}/quantity</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-cart-item--cartItemId--quantity"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-cart-item--cartItemId--quantity"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>cartItemId</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="cartItemId"                data-endpoint="POSTapi-cart-item--cartItemId--quantity"
               value="1"
               data-component="url">
    <br>
<p>The ID of the cart item. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>action</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="action"                data-endpoint="POSTapi-cart-item--cartItemId--quantity"
               value="increase"
               data-component="body">
    <br>
<p>Must be &quot;increase&quot; or &quot;decrease&quot;. Example: <code>increase</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>coupon_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="coupon_code"                data-endpoint="POSTapi-cart-item--cartItemId--quantity"
               value="SUMMER20"
               data-component="body">
    <br>
<p>nullable Coupon code to apply. Example: <code>SUMMER20</code></p>
        </div>
        </form>

                    <h2 id="cart-management-DELETEapi-cart-item--cartItemId-">Remove a cart item</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Removes a specific cart item from the cart. If the item is part of a bundle, the entire bundle is removed.</p>

<span id="example-requests-DELETEapi-cart-item--cartItemId-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "https://backend.sopdakt.com/api/cart/item/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"coupon_code\": \"SUMMER20\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/item/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "coupon_code": "SUMMER20"
};

fetch(url, {
    method: "DELETE",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-cart-item--cartItemId-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;cart&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;session_id&quot;: null,
        &quot;subtotal&quot;: 0,
        &quot;total&quot;: 0,
        &quot;tax_percentage&quot;: 5,
        &quot;tax_amount&quot;: 0,
        &quot;shipping_cost&quot;: 0
    },
    &quot;cartItems&quot;: [],
    &quot;totals&quot;: {
        &quot;subtotal&quot;: 0,
        &quot;shipping_cost&quot;: 0,
        &quot;tax&quot;: 0,
        &quot;total&quot;: 0,
        &quot;currency&quot;: &quot;USD&quot;,
        &quot;free_shipping_applied&quot;: false,
        &quot;discount_applied&quot;: 0
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Cart item not found&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-DELETEapi-cart-item--cartItemId-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-cart-item--cartItemId-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-cart-item--cartItemId-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-cart-item--cartItemId-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-cart-item--cartItemId-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-cart-item--cartItemId-" data-method="DELETE"
      data-path="api/cart/item/{cartItemId}"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-cart-item--cartItemId-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-cart-item--cartItemId-"
                    onclick="tryItOut('DELETEapi-cart-item--cartItemId-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-cart-item--cartItemId-"
                    onclick="cancelTryOut('DELETEapi-cart-item--cartItemId-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-cart-item--cartItemId-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/cart/item/{cartItemId}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-cart-item--cartItemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-cart-item--cartItemId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>cartItemId</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="cartItemId"                data-endpoint="DELETEapi-cart-item--cartItemId-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the cart item to remove. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>coupon_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="coupon_code"                data-endpoint="DELETEapi-cart-item--cartItemId-"
               value="SUMMER20"
               data-component="body">
    <br>
<p>nullable Coupon code to apply. Example: <code>SUMMER20</code></p>
        </div>
        </form>

                    <h2 id="cart-management-POSTapi-cart-checkout">Proceed to checkout</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Validates the cart and prepares it for checkout. Ensures valid quantities and shipping details, then returns a checkout URL.</p>

<span id="example-requests-POSTapi-cart-checkout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/cart/checkout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"selected_shipping\": 1,
    \"country_id\": 1,
    \"governorate_id\": 1,
    \"city_id\": 1,
    \"coupon_code\": \"SUMMER20\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/cart/checkout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "selected_shipping": 1,
    "country_id": 1,
    "governorate_id": 1,
    "city_id": 1,
    "coupon_code": "SUMMER20"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-cart-checkout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Cart ready for checkout&quot;,
    &quot;checkout_url&quot;: &quot;http://example.com/checkout&quot;,
    &quot;cart&quot;: {
        &quot;id&quot;: 1,
        &quot;user_id&quot;: 1,
        &quot;session_id&quot;: null,
        &quot;subtotal&quot;: 99.9899999999999948840923025272786617279052734375,
        &quot;total&quot;: 109.9899999999999948840923025272786617279052734375,
        &quot;tax_percentage&quot;: 5,
        &quot;tax_amount&quot;: 5,
        &quot;shipping_cost&quot;: 5,
        &quot;country_id&quot;: 1,
        &quot;governorate_id&quot;: 1,
        &quot;city_id&quot;: 1,
        &quot;shipping_type_id&quot;: 1
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;The maximum quantity allowed per product is 10. Need more? Contact us via our support page.&quot;,
    &quot;support_link&quot;: &quot;http://example.com/contact&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;errors&quot;: {
        &quot;country_id&quot;: [
            &quot;The country id field is required.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-cart-checkout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-cart-checkout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-cart-checkout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-cart-checkout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-cart-checkout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-cart-checkout" data-method="POST"
      data-path="api/cart/checkout"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-cart-checkout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-cart-checkout"
                    onclick="tryItOut('POSTapi-cart-checkout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-cart-checkout"
                    onclick="cancelTryOut('POSTapi-cart-checkout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-cart-checkout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/cart/checkout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-cart-checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-cart-checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>selected_shipping</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="selected_shipping"                data-endpoint="POSTapi-cart-checkout"
               value="1"
               data-component="body">
    <br>
<p>nullable ID of the selected shipping type. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="country_id"                data-endpoint="POSTapi-cart-checkout"
               value="1"
               data-component="body">
    <br>
<p>ID of the country. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>governorate_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="governorate_id"                data-endpoint="POSTapi-cart-checkout"
               value="1"
               data-component="body">
    <br>
<p>ID of the governorate. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>city_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="city_id"                data-endpoint="POSTapi-cart-checkout"
               value="1"
               data-component="body">
    <br>
<p>nullable ID of the city. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>coupon_code</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="coupon_code"                data-endpoint="POSTapi-cart-checkout"
               value="SUMMER20"
               data-component="body">
    <br>
<p>nullable Coupon code to apply. Example: <code>SUMMER20</code></p>
        </div>
        </form>

                <h1 id="categories">Categories</h1>

    <p>Display a category and its associated products with translations and details.</p>

                                <h2 id="categories-GETapi-categories--slug-">Show Category with Products</h2>

<p>
</p>



<span id="example-requests-GETapi-categories--slug-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/categories/architecto?locale=sr_BA" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/categories/architecto"
);

const params = {
    "locale": "sr_BA",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-categories--slug-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
  &quot;success&quot;: true,
  &quot;data&quot;: {
    &quot;id&quot;: 1,
    &quot;name&quot;: &quot;Electronics&quot;,
    &quot;slug&quot;: &quot;electronics&quot;,
    &quot;description&quot;: &quot;Localized description&quot;,
    &quot;products&quot;: [ ... ]
  }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-categories--slug-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-categories--slug-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-categories--slug-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-categories--slug-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-categories--slug-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-categories--slug-" data-method="GET"
      data-path="api/categories/{slug}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-categories--slug-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-categories--slug-"
                    onclick="tryItOut('GETapi-categories--slug-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-categories--slug-"
                    onclick="cancelTryOut('GETapi-categories--slug-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-categories--slug-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/categories/{slug}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-categories--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-categories--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="slug"                data-endpoint="GETapi-categories--slug-"
               value="architecto"
               data-component="url">
    <br>
<p>The slug of the category. Example: <code>architecto</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="GETapi-categories--slug-"
               value="sr_BA"
               data-component="query">
    <br>
<p>The locale to use for translations (default: en). Example: <code>sr_BA</code></p>
            </div>
                </form>

                <h1 id="checkout">Checkout</h1>

    

                                <h2 id="checkout-POSTapi-checkout">Process the checkout and place an order</h2>

<p>
</p>

<p>This endpoint handles the checkout process, validating user or guest contact details, processing payments,
and creating an order. It supports both authenticated users and guests, with optional account creation for guests.
Payment methods include Paymob (returns a payment iframe URL) and Cash on Delivery (creates the order directly).
The endpoint updates user/contact information, manages cart items, updates stock, sends email/WhatsApp notifications,
and integrates with JT Express for shipping. It also validates and applies any coupons associated with the cart.
The response includes the order details or payment URL on success, or an error message for failures (e.g., empty cart, invalid payment, invalid coupon).</p>

<span id="example-requests-POSTapi-checkout">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/checkout" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"payment_method_id\": 1,
    \"name\": \"John Doe\",
    \"address\": \"123 Main St, Cairo\",
    \"email\": \"john.doe@example.com\",
    \"phone\": \"01025263865\",
    \"second_phone\": \"01125263865\",
    \"notes\": \"Please deliver after 5 PM\",
    \"create_account\": true,
    \"password\": \"password123\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/checkout"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "payment_method_id": 1,
    "name": "John Doe",
    "address": "123 Main St, Cairo",
    "email": "john.doe@example.com",
    "phone": "01025263865",
    "second_phone": "01125263865",
    "notes": "Please deliver after 5 PM",
    "create_account": true,
    "password": "password123"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-checkout">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;payment_url&quot;: &quot;https://paymob.com/iframe/123456&quot;
    },
    &quot;message&quot;: &quot;Payment initiated successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;order_id&quot;: 1,
        &quot;total&quot;: 150,
        &quot;status&quot;: &quot;shipping&quot;,
        &quot;tracking_number&quot;: null,
        &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
    },
    &quot;message&quot;: &quot;Order placed successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Cart is empty or not found&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The given data was invalid.&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email has already been taken.&quot;
        ],
        &quot;phone&quot;: [
            &quot;The phone number is blocked. Please contact support.&quot;
        ],
        &quot;payment_method_id&quot;: [
            &quot;The selected payment method is invalid.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid or expired coupon.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred during checkout. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-checkout" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-checkout"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-checkout"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-checkout" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-checkout">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-checkout" data-method="POST"
      data-path="api/checkout"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-checkout', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-checkout"
                    onclick="tryItOut('POSTapi-checkout');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-checkout"
                    onclick="cancelTryOut('POSTapi-checkout');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-checkout"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/checkout</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-checkout"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>payment_method_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="payment_method_id"                data-endpoint="POSTapi-checkout"
               value="1"
               data-component="body">
    <br>
<p>The ID of the payment method (1 for COD, 2 for Paymob). Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-checkout"
               value="John Doe"
               data-component="body">
    <br>
<p>The name of the customer. Example: <code>John Doe</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>address</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="address"                data-endpoint="POSTapi-checkout"
               value="123 Main St, Cairo"
               data-component="body">
    <br>
<p>The shipping address. Example: <code>123 Main St, Cairo</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-checkout"
               value="john.doe@example.com"
               data-component="body">
    <br>
<p>The email address of the customer. Must be unique if creating an account. Example: <code>john.doe@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="POSTapi-checkout"
               value="01025263865"
               data-component="body">
    <br>
<p>The primary phone number of the customer (min 10 characters). Example: <code>01025263865</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>second_phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="second_phone"                data-endpoint="POSTapi-checkout"
               value="01125263865"
               data-component="body">
    <br>
<p>A secondary phone number, different from the primary (min 10 characters). Example: <code>01125263865</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="POSTapi-checkout"
               value="Please deliver after 5 PM"
               data-component="body">
    <br>
<p>Optional notes for the order. Example: <code>Please deliver after 5 PM</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>create_account</code></b>&nbsp;&nbsp;
<small>boolean</small>&nbsp;
<i>optional</i> &nbsp;
                <label data-endpoint="POSTapi-checkout" style="display: none">
            <input type="radio" name="create_account"
                   value="true"
                   data-endpoint="POSTapi-checkout"
                   data-component="body"             >
            <code>true</code>
        </label>
        <label data-endpoint="POSTapi-checkout" style="display: none">
            <input type="radio" name="create_account"
                   value="false"
                   data-endpoint="POSTapi-checkout"
                   data-component="body"             >
            <code>false</code>
        </label>
    <br>
<p>Whether to create a user account for guests. Example: <code>true</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string|null</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-checkout"
               value="password123"
               data-component="body">
    <br>
<p>Required if create_account is true (min 6 characters). Example: <code>password123</code></p>
        </div>
        </form>

                <h1 id="contact-messages">Contact Messages</h1>

    

                                <h2 id="contact-messages-POSTapi-contact">Store a new contact message</h2>

<p>
</p>

<p>This endpoint allows users to submit a contact message, which is validated and stored in the system.
The message includes the user's name, email, phone number, optional subject, and message content.
Upon successful submission, a notification is sent via the ContactMessageNotifier, and the stored
message is returned in the response. The user's IP address and authenticated user ID (if logged in)
are also recorded.</p>

<span id="example-requests-POSTapi-contact">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/contact" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"John Doe\",
    \"email\": \"john.doe@example.com\",
    \"phone\": \"+1234567890\",
    \"subject\": \"Inquiry about services\",
    \"message\": \"I have a question about your products.\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/contact"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "subject": "Inquiry about services",
    "message": "I have a question about your products."
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-contact">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Your message has been sent successfully.&quot;,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;name&quot;: &quot;John Doe&quot;,
        &quot;email&quot;: &quot;john.doe@example.com&quot;,
        &quot;phone&quot;: &quot;+1234567890&quot;,
        &quot;subject&quot;: &quot;Inquiry about services&quot;,
        &quot;message&quot;: &quot;I have a question about your products.&quot;,
        &quot;ip_address&quot;: &quot;192.168.1.1&quot;,
        &quot;user_id&quot;: null,
        &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
        &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The given data was invalid.&quot;,
    &quot;errors&quot;: {
        &quot;email&quot;: [
            &quot;The email field is required.&quot;
        ],
        &quot;name&quot;: [
            &quot;The name field is required.&quot;
        ],
        &quot;phone&quot;: [
            &quot;The phone field is required.&quot;
        ],
        &quot;message&quot;: [
            &quot;The message field is required.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while processing your message. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-contact" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-contact"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-contact"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-contact" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-contact">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-contact" data-method="POST"
      data-path="api/contact"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-contact', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-contact"
                    onclick="tryItOut('POSTapi-contact');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-contact"
                    onclick="cancelTryOut('POSTapi-contact');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-contact"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/contact</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-contact"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-contact"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-contact"
               value="John Doe"
               data-component="body">
    <br>
<p>The name of the sender. Example: <code>John Doe</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-contact"
               value="john.doe@example.com"
               data-component="body">
    <br>
<p>The email address of the sender. Must be a valid email. Example: <code>john.doe@example.com</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="POSTapi-contact"
               value="+1234567890"
               data-component="body">
    <br>
<p>The phone number of the sender. Example: <code>+1234567890</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>subject</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="subject"                data-endpoint="POSTapi-contact"
               value="Inquiry about services"
               data-component="body">
    <br>
<p>nullable The subject of the message. Example: <code>Inquiry about services</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>message</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="message"                data-endpoint="POSTapi-contact"
               value="I have a question about your products."
               data-component="body">
    <br>
<p>The content of the message. Example: <code>I have a question about your products.</code></p>
        </div>
        </form>

                <h1 id="contact-settings">Contact Settings</h1>

    

                                <h2 id="contact-settings-GETapi-contact-settings">Retrieve all contact settings</h2>

<p>
</p>

<p>This endpoint fetches all contact settings stored in the system, such as phone numbers, email addresses,
Skype handles, and social media links. The settings are returned as an associative array where the key is
the setting identifier (e.g., phone1, email1), and the value is the corresponding setting value. Social media
links are returned in a nested <code>social_media</code> object. If no settings are found, an error message is returned.</p>

<span id="example-requests-GETapi-contact-settings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/contact-settings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/contact-settings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-contact-settings">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;phone1&quot;: &quot;0201 203 2032&quot;,
        &quot;phone2&quot;: &quot;0201 203 2032&quot;,
        &quot;mobile1&quot;: &quot;201-123-39223&quot;,
        &quot;mobile2&quot;: &quot;02-123-3928&quot;,
        &quot;email1&quot;: &quot;porto@gmail.com&quot;,
        &quot;email2&quot;: &quot;porto@portotemplate.com&quot;,
        &quot;skype1&quot;: &quot;porto_skype&quot;,
        &quot;skype2&quot;: &quot;porto_templete&quot;
    },
    &quot;social_media&quot;: {
        &quot;facebook&quot;: &quot;https://facebook.com/company&quot;,
        &quot;instagram&quot;: &quot;https://instagram.com/company.ae&quot;,
        &quot;linkedin&quot;: null,
        &quot;twitter&quot;: null,
        &quot;youtube&quot;: null,
        &quot;tiktok&quot;: null
    },
    &quot;message&quot;: &quot;Contact settings retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Contact settings are null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving contact settings. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-contact-settings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-contact-settings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-contact-settings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-contact-settings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-contact-settings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-contact-settings" data-method="GET"
      data-path="api/contact-settings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-contact-settings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-contact-settings"
                    onclick="tryItOut('GETapi-contact-settings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-contact-settings"
                    onclick="cancelTryOut('GETapi-contact-settings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-contact-settings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/contact-settings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-contact-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-contact-settings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="endpoints">Endpoints</h1>

    

                                <h2 id="endpoints-POSTapi-register">POST api/register</h2>

<p>
</p>



<span id="example-requests-POSTapi-register">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/register" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"name\": \"b\",
    \"email\": \"zbailey@example.net\",
    \"password\": \"architecto\",
    \"phone\": \"architecto\",
    \"second_phone\": \"architecto\",
    \"preferred_language\": \"n\",
    \"avatar_url\": \"http:\\/\\/crooks.biz\\/et-fugiat-sunt-nihil-accusantium\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/register"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "name": "b",
    "email": "zbailey@example.net",
    "password": "architecto",
    "phone": "architecto",
    "second_phone": "architecto",
    "preferred_language": "n",
    "avatar_url": "http:\/\/crooks.biz\/et-fugiat-sunt-nihil-accusantium"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-register">
</span>
<span id="execution-results-POSTapi-register" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-register"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-register"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-register" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-register">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-register" data-method="POST"
      data-path="api/register"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-register', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-register"
                    onclick="tryItOut('POSTapi-register');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-register"
                    onclick="cancelTryOut('POSTapi-register');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-register"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/register</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-register"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="POSTapi-register"
               value="b"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-register"
               value="zbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>zbailey@example.net</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-register"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="phone"                data-endpoint="POSTapi-register"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>second_phone</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="second_phone"                data-endpoint="POSTapi-register"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>preferred_language</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="preferred_language"                data-endpoint="POSTapi-register"
               value="n"
               data-component="body">
    <br>
<p>Must not be greater than 5 characters. Example: <code>n</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>avatar_url</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="avatar_url"                data-endpoint="POSTapi-register"
               value="http://crooks.biz/et-fugiat-sunt-nihil-accusantium"
               data-component="body">
    <br>
<p>Must be a valid URL. Example: <code>http://crooks.biz/et-fugiat-sunt-nihil-accusantium</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>country_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="country_id"                data-endpoint="POSTapi-register"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the countries table.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>governorate_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="governorate_id"                data-endpoint="POSTapi-register"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the governorates table.</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>city_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="city_id"                data-endpoint="POSTapi-register"
               value=""
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the cities table.</p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-forgot-password">Handle an incoming password reset link request.</h2>

<p>
</p>



<span id="example-requests-POSTapi-forgot-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/forgot-password" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"gbailey@example.net\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/forgot-password"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "gbailey@example.net"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-forgot-password">
</span>
<span id="execution-results-POSTapi-forgot-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-forgot-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-forgot-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-forgot-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-forgot-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-forgot-password" data-method="POST"
      data-path="api/forgot-password"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-forgot-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-forgot-password"
                    onclick="tryItOut('POSTapi-forgot-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-forgot-password"
                    onclick="cancelTryOut('POSTapi-forgot-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-forgot-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/forgot-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-forgot-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-forgot-password"
               value="gbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>gbailey@example.net</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-reset-password">Handle an incoming new password request.</h2>

<p>
</p>



<span id="example-requests-POSTapi-reset-password">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/reset-password" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"token\": \"architecto\",
    \"email\": \"zbailey@example.net\",
    \"password\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/reset-password"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "token": "architecto",
    "email": "zbailey@example.net",
    "password": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-reset-password">
</span>
<span id="execution-results-POSTapi-reset-password" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-reset-password"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-reset-password"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-reset-password" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-reset-password">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-reset-password" data-method="POST"
      data-path="api/reset-password"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-reset-password', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-reset-password"
                    onclick="tryItOut('POSTapi-reset-password');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-reset-password"
                    onclick="cancelTryOut('POSTapi-reset-password');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-reset-password"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/reset-password</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-reset-password"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>token</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="token"                data-endpoint="POSTapi-reset-password"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-reset-password"
               value="zbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Example: <code>zbailey@example.net</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>password</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="password"                data-endpoint="POSTapi-reset-password"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-verify-email--id---hash-">Mark the authenticated user&#039;s email address as verified.</h2>

<p>
</p>



<span id="example-requests-GETapi-verify-email--id---hash-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/verify-email/architecto/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/verify-email/architecto/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-verify-email--id---hash-">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-verify-email--id---hash-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-verify-email--id---hash-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-verify-email--id---hash-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-verify-email--id---hash-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-verify-email--id---hash-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-verify-email--id---hash-" data-method="GET"
      data-path="api/verify-email/{id}/{hash}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-verify-email--id---hash-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-verify-email--id---hash-"
                    onclick="tryItOut('GETapi-verify-email--id---hash-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-verify-email--id---hash-"
                    onclick="cancelTryOut('GETapi-verify-email--id---hash-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-verify-email--id---hash-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/verify-email/{id}/{hash}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-verify-email--id---hash-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-verify-email--id---hash-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-verify-email--id---hash-"
               value="architecto"
               data-component="url">
    <br>
<p>The ID of the verify email. Example: <code>architecto</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>hash</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="hash"                data-endpoint="GETapi-verify-email--id---hash-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-email-verification-notification">Send a new email verification notification.</h2>

<p>
</p>



<span id="example-requests-POSTapi-email-verification-notification">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/email/verification-notification" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/email/verification-notification"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-email-verification-notification">
</span>
<span id="execution-results-POSTapi-email-verification-notification" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-email-verification-notification"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-email-verification-notification"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-email-verification-notification" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-email-verification-notification">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-email-verification-notification" data-method="POST"
      data-path="api/email/verification-notification"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-email-verification-notification', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-email-verification-notification"
                    onclick="tryItOut('POSTapi-email-verification-notification');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-email-verification-notification"
                    onclick="cancelTryOut('POSTapi-email-verification-notification');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-email-verification-notification"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/email/verification-notification</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-email-verification-notification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-email-verification-notification"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-user">GET api/user</h2>

<p>
</p>



<span id="example-requests-GETapi-user">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/user" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/user"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-user">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-user" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-user"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-user"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-user" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-user">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-user" data-method="GET"
      data-path="api/user"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-user', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-user"
                    onclick="tryItOut('GETapi-user');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-user"
                    onclick="cancelTryOut('GETapi-user');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-user"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/user</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-user"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-jt-express-webhook">POST api/jt-express-webhook</h2>

<p>
</p>



<span id="example-requests-POSTapi-jt-express-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/jt-express-webhook" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/jt-express-webhook"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-jt-express-webhook">
</span>
<span id="execution-results-POSTapi-jt-express-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-jt-express-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-jt-express-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-jt-express-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-jt-express-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-jt-express-webhook" data-method="POST"
      data-path="api/jt-express-webhook"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-jt-express-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-jt-express-webhook"
                    onclick="tryItOut('POSTapi-jt-express-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-jt-express-webhook"
                    onclick="cancelTryOut('POSTapi-jt-express-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-jt-express-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/jt-express-webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-jt-express-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-jt-express-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-bosta-webhook">POST api/bosta/webhook</h2>

<p>
</p>



<span id="example-requests-POSTapi-bosta-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/bosta/webhook" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/bosta/webhook"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-bosta-webhook">
</span>
<span id="execution-results-POSTapi-bosta-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-bosta-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-bosta-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-bosta-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-bosta-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-bosta-webhook" data-method="POST"
      data-path="api/bosta/webhook"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-bosta-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-bosta-webhook"
                    onclick="tryItOut('POSTapi-bosta-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-bosta-webhook"
                    onclick="cancelTryOut('POSTapi-bosta-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-bosta-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/bosta/webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-bosta-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-bosta-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-aramex-webhook">Webhook for ARAMEX to update shipment status</h2>

<p>
</p>



<span id="example-requests-POSTapi-aramex-webhook">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/aramex/webhook" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/aramex/webhook"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-aramex-webhook">
</span>
<span id="execution-results-POSTapi-aramex-webhook" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-aramex-webhook"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-aramex-webhook"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-aramex-webhook" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-aramex-webhook">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-aramex-webhook" data-method="POST"
      data-path="api/aramex/webhook"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-aramex-webhook', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-aramex-webhook"
                    onclick="tryItOut('POSTapi-aramex-webhook');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-aramex-webhook"
                    onclick="cancelTryOut('POSTapi-aramex-webhook');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-aramex-webhook"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/aramex/webhook</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-aramex-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-aramex-webhook"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-POSTapi-aramex-orders--order_id--create-shipment">Create ARAMEX shipment for an order</h2>

<p>
</p>



<span id="example-requests-POSTapi-aramex-orders--order_id--create-shipment">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/aramex/orders/1/create-shipment" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/aramex/orders/1/create-shipment"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-aramex-orders--order_id--create-shipment">
</span>
<span id="execution-results-POSTapi-aramex-orders--order_id--create-shipment" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-aramex-orders--order_id--create-shipment"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-aramex-orders--order_id--create-shipment"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-aramex-orders--order_id--create-shipment" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-aramex-orders--order_id--create-shipment">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-aramex-orders--order_id--create-shipment" data-method="POST"
      data-path="api/aramex/orders/{order_id}/create-shipment"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-aramex-orders--order_id--create-shipment', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-aramex-orders--order_id--create-shipment"
                    onclick="tryItOut('POSTapi-aramex-orders--order_id--create-shipment');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-aramex-orders--order_id--create-shipment"
                    onclick="cancelTryOut('POSTapi-aramex-orders--order_id--create-shipment');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-aramex-orders--order_id--create-shipment"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/aramex/orders/{order_id}/create-shipment</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-aramex-orders--order_id--create-shipment"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-aramex-orders--order_id--create-shipment"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>order_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order_id"                data-endpoint="POSTapi-aramex-orders--order_id--create-shipment"
               value="1"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-aramex-orders--order_id--track-shipment">Track ARAMEX shipment</h2>

<p>
</p>



<span id="example-requests-GETapi-aramex-orders--order_id--track-shipment">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/aramex/orders/1/track-shipment" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/aramex/orders/1/track-shipment"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-aramex-orders--order_id--track-shipment">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Failed to track ARAMEX shipment: No ARAMEX shipment ID found for this order&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-aramex-orders--order_id--track-shipment" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-aramex-orders--order_id--track-shipment"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-aramex-orders--order_id--track-shipment"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-aramex-orders--order_id--track-shipment" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-aramex-orders--order_id--track-shipment">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-aramex-orders--order_id--track-shipment" data-method="GET"
      data-path="api/aramex/orders/{order_id}/track-shipment"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-aramex-orders--order_id--track-shipment', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-aramex-orders--order_id--track-shipment"
                    onclick="tryItOut('GETapi-aramex-orders--order_id--track-shipment');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-aramex-orders--order_id--track-shipment"
                    onclick="cancelTryOut('GETapi-aramex-orders--order_id--track-shipment');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-aramex-orders--order_id--track-shipment"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/aramex/orders/{order_id}/track-shipment</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-aramex-orders--order_id--track-shipment"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-aramex-orders--order_id--track-shipment"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>order_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order_id"                data-endpoint="GETapi-aramex-orders--order_id--track-shipment"
               value="1"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-payment-process">POST api/payment/process</h2>

<p>
</p>



<span id="example-requests-POSTapi-payment-process">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/payment/process" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/payment/process"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-payment-process">
</span>
<span id="execution-results-POSTapi-payment-process" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-payment-process"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-payment-process"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-payment-process" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-payment-process">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-payment-process" data-method="POST"
      data-path="api/payment/process"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-payment-process', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-payment-process"
                    onclick="tryItOut('POSTapi-payment-process');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-payment-process"
                    onclick="cancelTryOut('POSTapi-payment-process');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-payment-process"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/payment/process</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-payment-process"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-payment-process"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-payment-callback">GET api/payment/callback</h2>

<p>
</p>



<span id="example-requests-GETapi-payment-callback">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/payment/callback" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/payment/callback"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-payment-callback">
            <blockquote>
            <p>Example response (302):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
location: https://backend.sopdakt.com/api/cart
content-type: text/html; charset=utf-8
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">&lt;!DOCTYPE html&gt;
&lt;html&gt;
    &lt;head&gt;
        &lt;meta charset=&quot;UTF-8&quot; /&gt;
        &lt;meta http-equiv=&quot;refresh&quot; content=&quot;0;url=&#039;https://backend.sopdakt.com/api/cart&#039;&quot; /&gt;

        &lt;title&gt;Redirecting to https://backend.sopdakt.com/api/cart&lt;/title&gt;
    &lt;/head&gt;
    &lt;body&gt;
        Redirecting to &lt;a href=&quot;https://backend.sopdakt.com/api/cart&quot;&gt;https://backend.sopdakt.com/api/cart&lt;/a&gt;.
    &lt;/body&gt;
&lt;/html&gt;</code>
 </pre>
    </span>
<span id="execution-results-GETapi-payment-callback" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-payment-callback"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-payment-callback"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-payment-callback" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-payment-callback">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-payment-callback" data-method="GET"
      data-path="api/payment/callback"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-payment-callback', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-payment-callback"
                    onclick="tryItOut('GETapi-payment-callback');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-payment-callback"
                    onclick="cancelTryOut('GETapi-payment-callback');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-payment-callback"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/payment/callback</code></b>
        </p>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/payment/callback</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-payment-callback"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-payment-callback"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-categories">GET api/categories</h2>

<p>
</p>



<span id="example-requests-GETapi-categories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/categories" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/categories"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-categories">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;categories&quot;: [
            {
                &quot;id&quot;: 10,
                &quot;name&quot;: &quot;OVER OVER SIZE&quot;,
                &quot;slug&quot;: &quot;overoversize&quot;,
                &quot;description&quot;: &quot;&quot;,
                &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/45/01JVHZ9KJPRZSXJZQ1TP3Q0Y6T.jpeg&quot;,
                &quot;products_count&quot;: 3,
                &quot;url&quot;: &quot;https://backend.sopdakt.com/api/categories/overoversize&quot;
            },
            {
                &quot;id&quot;: 7,
                &quot;name&quot;: &quot;OVER SIZE&quot;,
                &quot;slug&quot;: &quot;t-shirt&quot;,
                &quot;description&quot;: &quot;&quot;,
                &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/35/01JV4JP79353H3NEE1WRNGPZ65.jpeg&quot;,
                &quot;products_count&quot;: 7,
                &quot;url&quot;: &quot;https://backend.sopdakt.com/api/categories/t-shirt&quot;
            }
        ],
        &quot;pagination&quot;: {
            &quot;current_page&quot;: 1,
            &quot;last_page&quot;: 1,
            &quot;per_page&quot;: 10,
            &quot;total&quot;: 2
        }
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-categories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-categories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-categories" data-method="GET"
      data-path="api/categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-categories"
                    onclick="tryItOut('GETapi-categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-categories"
                    onclick="cancelTryOut('GETapi-categories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-categories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-products-realBestSellers">Get the real top 10 best-selling products based on the total quantity sold.</h2>

<p>
</p>

<p>This method retrieves products that have been ordered at least once,
calculates the total quantity sold (<code>total_sold</code>) via the <code>orderItems</code> relationship,
and returns the top 10 products sorted by the highest sales.</p>
<p>Each product includes:</p>
<ul>
<li>Basic info: id, name (translated), price, discounted price, sales count, slug, image URL</li>
<li>Category (with name and slug)</li>
<li>Available color variants with:
<ul>
<li>Color name, code, and image</li>
<li>Sizes available for that color with quantity &gt; 0</li>
</ul></li>
<li>Action URLs:
<ul>
<li>Add to cart</li>
<li>Toggle wishlist</li>
<li>Compare</li>
<li>View product details</li>
</ul></li>
</ul>
<p>Translation is handled automatically via Spatie Laravel Translatable package.</p>

<span id="example-requests-GETapi-products-realBestSellers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/realBestSellers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/realBestSellers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products-realBestSellers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Best selling products retrieved successfully.&quot;,
    &quot;products&quot;: [
        {
            &quot;id&quot;: 10,
            &quot;name&quot;: &quot;t-shirt&quot;,
            &quot;price&quot;: 0,
            &quot;after_discount_price&quot;: null,
            &quot;sales&quot;: 0,
            &quot;slug&quot;: &quot;shirt&quot;,
            &quot;media&quot;: {
                &quot;feature_product_image&quot;: &quot;https://backend.sopdakt.com/storage/36/01JV4QFG903F5PEQWC433MW7BD.jpeg&quot;,
                &quot;second_feature_product_image&quot;: &quot;https://backend.sopdakt.com/storage/37/01JV4QFGADN01XVRZGXTS738DR.jpeg&quot;
            },
            &quot;category&quot;: {
                &quot;name&quot;: &quot;OVER SIZE&quot;,
                &quot;slug&quot;: &quot;t-shirt&quot;
            },
            &quot;colors_with_sizes&quot;: [
                {
                    &quot;color_id&quot;: 3,
                    &quot;color_name&quot;: &quot;Green&quot;,
                    &quot;color_code&quot;: &quot;#008000&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JV4QFG09C4045QMQ268EN7X1.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 2,
                            &quot;size_name&quot;: &quot;S&quot;,
                            &quot;quantity&quot;: 95
                        },
                        {
                            &quot;size_id&quot;: 3,
                            &quot;size_name&quot;: &quot;M&quot;,
                            &quot;quantity&quot;: 83
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 100
                        }
                    ]
                },
                {
                    &quot;color_id&quot;: 5,
                    &quot;color_name&quot;: &quot;Black&quot;,
                    &quot;color_code&quot;: &quot;#000000&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JV4QFG0EMA5BC5H9NN6N8HD6.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 4,
                            &quot;size_name&quot;: &quot;L&quot;,
                            &quot;quantity&quot;: 93
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 89
                        }
                    ]
                },
                {
                    &quot;color_id&quot;: 2,
                    &quot;color_name&quot;: &quot;Blue&quot;,
                    &quot;color_code&quot;: &quot;#0000FF&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JV4QFG0HVGCE6K0QCR6CVT82.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 3,
                            &quot;size_name&quot;: &quot;M&quot;,
                            &quot;quantity&quot;: 100
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 98
                        }
                    ]
                },
                {
                    &quot;color_id&quot;: 6,
                    &quot;color_name&quot;: &quot;White&quot;,
                    &quot;color_code&quot;: &quot;#FFFFFF&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JV4QFG0NGWA743R9Z19W1DED.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 3,
                            &quot;size_name&quot;: &quot;M&quot;,
                            &quot;quantity&quot;: 99
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 100
                        }
                    ]
                }
            ],
            &quot;actions&quot;: {
                &quot;add_to_cart&quot;: &quot;https://backend.sopdakt.com/api/cart&quot;,
                &quot;toggle_love&quot;: &quot;https://backend.sopdakt.com/api/wishlist/toggle&quot;,
                &quot;compare&quot;: &quot;https://backend.sopdakt.com/api/compare&quot;,
                &quot;view&quot;: &quot;https://backend.sopdakt.com/api/products/shirt&quot;
            }
        },
        {
            &quot;id&quot;: 14,
            &quot;name&quot;: &quot;tshirt 3&quot;,
            &quot;price&quot;: 1000,
            &quot;after_discount_price&quot;: 800,
            &quot;sales&quot;: 19658214,
            &quot;slug&quot;: &quot;overoversize&quot;,
            &quot;media&quot;: {
                &quot;feature_product_image&quot;: &quot;https://backend.sopdakt.com/storage/50/01JVM0FNQEV7N6XB36FGQ64F4F.jpeg&quot;,
                &quot;second_feature_product_image&quot;: &quot;https://backend.sopdakt.com/storage/51/01JVM0FNS1MKXCQ28CA781TZT2.jpeg&quot;
            },
            &quot;category&quot;: {
                &quot;name&quot;: &quot;OVER OVER SIZE&quot;,
                &quot;slug&quot;: &quot;overoversize&quot;
            },
            &quot;colors_with_sizes&quot;: [
                {
                    &quot;color_id&quot;: 5,
                    &quot;color_name&quot;: &quot;Black&quot;,
                    &quot;color_code&quot;: &quot;#000000&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JVM0FNGX1A6P5RWW6MM63MXJ.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 4,
                            &quot;size_name&quot;: &quot;L&quot;,
                            &quot;quantity&quot;: 100
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 98
                        },
                        {
                            &quot;size_id&quot;: 7,
                            &quot;size_name&quot;: &quot; 3XL&quot;,
                            &quot;quantity&quot;: 100
                        },
                        {
                            &quot;size_id&quot;: 9,
                            &quot;size_name&quot;: &quot;4xl&quot;,
                            &quot;quantity&quot;: 100
                        }
                    ]
                },
                {
                    &quot;color_id&quot;: 6,
                    &quot;color_name&quot;: &quot;White&quot;,
                    &quot;color_code&quot;: &quot;#FFFFFF&quot;,
                    &quot;color_image&quot;: &quot;https://backend.sopdakt.com/storage/01JVM0FNH2VJC6KZQQ79NPS989.jpeg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_id&quot;: 4,
                            &quot;size_name&quot;: &quot;L&quot;,
                            &quot;quantity&quot;: 100
                        },
                        {
                            &quot;size_id&quot;: 3,
                            &quot;size_name&quot;: &quot;M&quot;,
                            &quot;quantity&quot;: 98
                        },
                        {
                            &quot;size_id&quot;: 5,
                            &quot;size_name&quot;: &quot;XL&quot;,
                            &quot;quantity&quot;: 100
                        }
                    ]
                }
            ],
            &quot;actions&quot;: {
                &quot;add_to_cart&quot;: &quot;https://backend.sopdakt.com/api/cart&quot;,
                &quot;toggle_love&quot;: &quot;https://backend.sopdakt.com/api/wishlist/toggle&quot;,
                &quot;compare&quot;: &quot;https://backend.sopdakt.com/api/compare&quot;,
                &quot;view&quot;: &quot;https://backend.sopdakt.com/api/products/overoversize&quot;
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products-realBestSellers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products-realBestSellers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products-realBestSellers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products-realBestSellers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products-realBestSellers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products-realBestSellers" data-method="GET"
      data-path="api/products/realBestSellers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products-realBestSellers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products-realBestSellers"
                    onclick="tryItOut('GETapi-products-realBestSellers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products-realBestSellers"
                    onclick="cancelTryOut('GETapi-products-realBestSellers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products-realBestSellers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/realBestSellers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products-realBestSellers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products-realBestSellers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-homepage-slider">Get homepage slider and CTA content.</h2>

<p>
</p>

<p>This endpoint returns the main slider, second slider, center section,
last sections, and latest section based on the current app locale
(either English or Arabic).</p>

<span id="example-requests-GETapi-homepage-slider">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/homepage/slider?locale=sr_BA" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/homepage/slider"
);

const params = {
    "locale": "sr_BA",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-homepage-slider">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
  &quot;data&quot;: {
    &quot;main_slider&quot;: {
      &quot;image_url&quot;: &quot;string&quot;,
      &quot;thumbnail_url&quot;: &quot;string&quot;,
      &quot;heading&quot;: &quot;string&quot;,
      &quot;discount_text&quot;: &quot;string&quot;,
      &quot;discount_value&quot;: &quot;string&quot;,
      &quot;starting_price&quot;: &quot;string&quot;,
      &quot;currency_symbol&quot;: &quot;string&quot;,
      &quot;button_text&quot;: &quot;string&quot;,
      &quot;button_url&quot;: &quot;string&quot;
    },
    &quot;second_slider&quot;: {
      &quot;image_url&quot;: &quot;string&quot;,
      &quot;thumbnail_url&quot;: &quot;string&quot;
    },
    &quot;center_section&quot;: {
      &quot;image_url&quot;: &quot;string&quot;,
      &quot;heading&quot;: &quot;string&quot;,
      &quot;button_text&quot;: &quot;string&quot;,
      &quot;button_url&quot;: &quot;string&quot;
    },
    &quot;last_sections&quot;: [
      {
        &quot;image_url&quot;: &quot;string&quot;,
        &quot;heading&quot;: &quot;string&quot;,
        &quot;subheading&quot;: &quot;string&quot;,
        &quot;button_text&quot;: &quot;string&quot;,
        &quot;button_url&quot;: &quot;string&quot;
      },
      ...
    ],
    &quot;latest_section&quot;: {
      &quot;image_url&quot;: &quot;string&quot;,
      &quot;heading&quot;: &quot;string&quot;,
      &quot;button_text&quot;: &quot;string&quot;,
      &quot;button_url&quot;: &quot;string&quot;
    }
  }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-homepage-slider" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-homepage-slider"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-homepage-slider"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-homepage-slider" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-homepage-slider">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-homepage-slider" data-method="GET"
      data-path="api/homepage/slider"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-homepage-slider', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-homepage-slider"
                    onclick="tryItOut('GETapi-homepage-slider');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-homepage-slider"
                    onclick="cancelTryOut('GETapi-homepage-slider');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-homepage-slider"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/homepage/slider</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-homepage-slider"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-homepage-slider"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="GETapi-homepage-slider"
               value="sr_BA"
               data-component="query">
    <br>
<p>Optional. Language code ('en' or 'ar'). Example: <code>sr_BA</code></p>
            </div>
                </form>

                    <h2 id="endpoints-POSTapi-wishlist-toggle">POST api/wishlist/toggle</h2>

<p>
</p>



<span id="example-requests-POSTapi-wishlist-toggle">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/wishlist/toggle" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"product_id\": \"architecto\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/wishlist/toggle"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "product_id": "architecto"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-wishlist-toggle">
</span>
<span id="execution-results-POSTapi-wishlist-toggle" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-wishlist-toggle"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wishlist-toggle"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-wishlist-toggle" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wishlist-toggle">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-wishlist-toggle" data-method="POST"
      data-path="api/wishlist/toggle"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-wishlist-toggle', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-wishlist-toggle"
                    onclick="tryItOut('POSTapi-wishlist-toggle');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-wishlist-toggle"
                    onclick="cancelTryOut('POSTapi-wishlist-toggle');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-wishlist-toggle"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/wishlist/toggle</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-wishlist-toggle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-wishlist-toggle"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>product_id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="product_id"                data-endpoint="POSTapi-wishlist-toggle"
               value="architecto"
               data-component="body">
    <br>
<p>The <code>id</code> of an existing record in the products table. Example: <code>architecto</code></p>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-wishlist">GET api/wishlist</h2>

<p>
</p>



<span id="example-requests-GETapi-wishlist">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/wishlist" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/wishlist"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-wishlist">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Session store not set on request.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-wishlist" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-wishlist"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wishlist"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-wishlist" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wishlist">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-wishlist" data-method="GET"
      data-path="api/wishlist"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-wishlist', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-wishlist"
                    onclick="tryItOut('GETapi-wishlist');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-wishlist"
                    onclick="cancelTryOut('GETapi-wishlist');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-wishlist"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/wishlist</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-wishlist"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-wishlist"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-orders">List all orders for the authenticated user.</h2>

<p>
</p>



<span id="example-requests-GETapi-orders">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/orders" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/orders"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-orders">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-orders" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-orders"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-orders"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-orders" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-orders">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-orders" data-method="GET"
      data-path="api/orders"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-orders', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-orders"
                    onclick="tryItOut('GETapi-orders');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-orders"
                    onclick="cancelTryOut('GETapi-orders');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-orders"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/orders</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-orders"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-orders-track--tracking_number-">Track a specific order by its tracking number.</h2>

<p>
</p>



<span id="example-requests-GETapi-orders-track--tracking_number-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/orders/track/architecto" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/orders/track/architecto"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-orders-track--tracking_number-">
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: &quot;error&quot;,
    &quot;message&quot;: &quot;Unauthenticated.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-orders-track--tracking_number-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-orders-track--tracking_number-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-orders-track--tracking_number-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-orders-track--tracking_number-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-orders-track--tracking_number-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-orders-track--tracking_number-" data-method="GET"
      data-path="api/orders/track/{tracking_number}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-orders-track--tracking_number-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-orders-track--tracking_number-"
                    onclick="tryItOut('GETapi-orders-track--tracking_number-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-orders-track--tracking_number-"
                    onclick="cancelTryOut('GETapi-orders-track--tracking_number-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-orders-track--tracking_number-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/orders/track/{tracking_number}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-orders-track--tracking_number-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-orders-track--tracking_number-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>tracking_number</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tracking_number"                data-endpoint="GETapi-orders-track--tracking_number-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-PUTapi-orders--order_id-">Update the order (only if it belongs to the authenticated user).</h2>

<p>
</p>



<span id="example-requests-PUTapi-orders--order_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "https://backend.sopdakt.com/api/orders/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"notes\": \"architecto\",
    \"status\": \"shipping\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/orders/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "notes": "architecto",
    "status": "shipping"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-orders--order_id-">
</span>
<span id="execution-results-PUTapi-orders--order_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-orders--order_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-orders--order_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-orders--order_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-orders--order_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-orders--order_id-" data-method="PUT"
      data-path="api/orders/{order_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-orders--order_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-orders--order_id-"
                    onclick="tryItOut('PUTapi-orders--order_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-orders--order_id-"
                    onclick="cancelTryOut('PUTapi-orders--order_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-orders--order_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/orders/{order_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-orders--order_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-orders--order_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>order_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order_id"                data-endpoint="PUTapi-orders--order_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>1</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>notes</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="notes"                data-endpoint="PUTapi-orders--order_id-"
               value="architecto"
               data-component="body">
    <br>
<p>Example: <code>architecto</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>status</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="status"                data-endpoint="PUTapi-orders--order_id-"
               value="shipping"
               data-component="body">
    <br>
<p>Example: <code>shipping</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>pending</code></li> <li><code>preparing</code></li> <li><code>shipping</code></li> <li><code>delayed</code></li> <li><code>refund</code></li> <li><code>cancelled</code></li> <li><code>completed</code></li></ul>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-orders--order_id-">Delete the order (only if it belongs to the authenticated user).</h2>

<p>
</p>



<span id="example-requests-DELETEapi-orders--order_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "https://backend.sopdakt.com/api/orders/1" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/orders/1"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-orders--order_id-">
</span>
<span id="execution-results-DELETEapi-orders--order_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-orders--order_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-orders--order_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-orders--order_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-orders--order_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-orders--order_id-" data-method="DELETE"
      data-path="api/orders/{order_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-orders--order_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-orders--order_id-"
                    onclick="tryItOut('DELETEapi-orders--order_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-orders--order_id-"
                    onclick="cancelTryOut('DELETEapi-orders--order_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-orders--order_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/orders/{order_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-orders--order_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-orders--order_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>order_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="order_id"                data-endpoint="DELETEapi-orders--order_id-"
               value="1"
               data-component="url">
    <br>
<p>The ID of the order. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-discounts">Display a paginated list of active discounts with filtering, sorting, and search.</h2>

<p>
</p>



<span id="example-requests-GETapi-discounts">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/discounts" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"locale\": \"iy\",
    \"search\": \"v\",
    \"discount_type\": \"free_shipping\",
    \"applies_to\": \"product\",
    \"sort\": \"ends_at\",
    \"direction\": \"desc\",
    \"per_page\": 1,
    \"page\": 40
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/discounts"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "locale": "iy",
    "search": "v",
    "discount_type": "free_shipping",
    "applies_to": "product",
    "sort": "ends_at",
    "direction": "desc",
    "per_page": 1,
    "page": 40
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-discounts">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [],
    &quot;meta&quot;: {
        &quot;current_page&quot;: 40,
        &quot;last_page&quot;: 1,
        &quot;per_page&quot;: 1,
        &quot;total&quot;: 0
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-discounts" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-discounts"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-discounts"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-discounts" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-discounts">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-discounts" data-method="GET"
      data-path="api/discounts"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-discounts', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-discounts"
                    onclick="tryItOut('GETapi-discounts');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-discounts"
                    onclick="cancelTryOut('GETapi-discounts');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-discounts"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/discounts</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-discounts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-discounts"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>locale</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="locale"                data-endpoint="GETapi-discounts"
               value="iy"
               data-component="body">
    <br>
<p>Must match the regex /^[a-zA-Z]{2}(-[a-zA-Z]{2})?$/. Example: <code>iy</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>search</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="search"                data-endpoint="GETapi-discounts"
               value="v"
               data-component="body">
    <br>
<p>Must not be greater than 255 characters. Example: <code>v</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>discount_type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="discount_type"                data-endpoint="GETapi-discounts"
               value="free_shipping"
               data-component="body">
    <br>
<p>Example: <code>free_shipping</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>percentage</code></li> <li><code>fixed</code></li> <li><code>free_shipping</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>applies_to</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="applies_to"                data-endpoint="GETapi-discounts"
               value="product"
               data-component="body">
    <br>
<p>Example: <code>product</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>product</code></li> <li><code>category</code></li> <li><code>cart</code></li> <li><code>collection</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>sort</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="sort"                data-endpoint="GETapi-discounts"
               value="ends_at"
               data-component="body">
    <br>
<p>Example: <code>ends_at</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>created_at</code></li> <li><code>starts_at</code></li> <li><code>ends_at</code></li> <li><code>value</code></li> <li><code>price</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>direction</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="direction"                data-endpoint="GETapi-discounts"
               value="desc"
               data-component="body">
    <br>
<p>Example: <code>desc</code></p>
Must be one of:
<ul style="list-style-type: square;"><li><code>asc</code></li> <li><code>desc</code></li></ul>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-discounts"
               value="1"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 50. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-discounts"
               value="40"
               data-component="body">
    <br>
<p>Must be at least 1. Example: <code>40</code></p>
        </div>
        </form>

                    <h2 id="endpoints-POSTapi-newsletter-subscribe">Store a new newsletter subscriber.</h2>

<p>
</p>

<p>This endpoint allows users to subscribe to the newsletter by providing their email address.
The email is validated for format and uniqueness, and the client's IP address is recorded.
A verification email is sent to the provided email address. The subscription is not active
until the email is verified. Returns a success message or an error for invalid inputs
or duplicate emails.</p>

<span id="example-requests-POSTapi-newsletter-subscribe">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/newsletter/subscribe" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"email\": \"gbailey@example.net\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/newsletter/subscribe"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "email": "gbailey@example.net"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-newsletter-subscribe">
            <blockquote>
            <p>Example response (201):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Subscription request received. Please check your email to verify.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (409):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Email already subscribed.&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Invalid input&quot;,
    &quot;details&quot;: {
        &quot;email&quot;: [
            &quot;The email field is required.&quot;,
            &quot;The email must be a valid email address.&quot;
        ]
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Failed to process subscription. Please try again later.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-newsletter-subscribe" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-newsletter-subscribe"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-newsletter-subscribe"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-newsletter-subscribe" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-newsletter-subscribe">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-newsletter-subscribe" data-method="POST"
      data-path="api/newsletter/subscribe"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-newsletter-subscribe', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-newsletter-subscribe"
                    onclick="tryItOut('POSTapi-newsletter-subscribe');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-newsletter-subscribe"
                    onclick="cancelTryOut('POSTapi-newsletter-subscribe');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-newsletter-subscribe"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/newsletter/subscribe</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-newsletter-subscribe"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-newsletter-subscribe"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>email</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="email"                data-endpoint="POSTapi-newsletter-subscribe"
               value="gbailey@example.net"
               data-component="body">
    <br>
<p>Must be a valid email address. Must not be greater than 255 characters. Example: <code>gbailey@example.net</code></p>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-recommended-products">Retrieve a list of recommended products.</h2>

<p>
</p>

<p>This endpoint fetches up to 4 randomly selected active (published) products as recommendations.
It includes detailed information such as categories, variants (colors and sizes), ratings, and
media URLs. The response is localized based on the current application locale (e.g., 'en', 'fr').
This endpoint is ideal for frontend use cases like displaying a &quot;Recommended Products&quot; section
on a homepage or product page in an e-commerce application.</p>
<h3>Request Details</h3>
<ul>
<li><strong>Method</strong>: GET</li>
<li><strong>URL</strong>: <code>/api/products/recommended</code></li>
<li><strong>Query Parameters</strong>: None</li>
<li><strong>Headers</strong>:
<ul>
<li><code>Accept: application/json</code></li>
<li><code>X-Locale: &lt;locale&gt;</code> (optional, defaults to app locale, e.g., 'en')</li>
</ul></li>
</ul>
<h3>Response Structure</h3>
<p>The response contains a <code>recommended_products</code> key with an array of up to 4 product objects.
Each product includes:</p>
<ul>
<li><strong>id</strong>: The product's unique identifier (integer).</li>
<li><strong>category_id</strong>: The ID of the product's category (integer).</li>
<li><strong>category_name</strong>: The localized name of the category (string, nullable).</li>
<li><strong>name</strong>: The localized name of the product (string).</li>
<li><strong>price</strong>: The original price of the product (float).</li>
<li><strong>after_discount_price</strong>: The discounted price, if applicable (float, nullable).</li>
<li><strong>description</strong>: The localized description of the product (string).</li>
<li><strong>slug</strong>: The URL-friendly slug for the product (string).</li>
<li><strong>views</strong>: Number of views for the product (integer).</li>
<li><strong>sales</strong>: Number of sales for the product (integer).</li>
<li><strong>fake_average_rating</strong>: A manually set average rating for display (float, nullable).</li>
<li><strong>label_id</strong>: The ID of the product's label, if any (integer, nullable).</li>
<li><strong>summary</strong>: The localized summary of the product (string).</li>
<li><strong>quantity</strong>: Total available quantity of the product (integer).</li>
<li><strong>created_at</strong>: Timestamp when the product was created (ISO 8601 string).</li>
<li><strong>updated_at</strong>: Timestamp when the product was last updated (ISO 8601 string).</li>
<li><strong>media</strong>: Object containing product images.
<ul>
<li><strong>feature_product_image</strong>: URL of the primary feature image (string, nullable).</li>
<li><strong>second_feature_product_image</strong>: URL of the secondary feature image (string, nullable).</li>
</ul></li>
<li><strong>variants</strong>: Array of product variants (colors and sizes).
<ul>
<li><strong>id</strong>: Variant ID (integer).</li>
<li><strong>color_id</strong>: Color ID (integer).</li>
<li><strong>color_name</strong>: Name of the color (string, nullable).</li>
<li><strong>image_url</strong>: URL of the variant image (string).</li>
<li><strong>sizes</strong>: Array of size options for the variant.</li>
<li><strong>id</strong>: Product color size ID (integer).</li>
<li><strong>size_id</strong>: Size ID (integer).</li>
<li><strong>size_name</strong>: Name of the size (string, nullable).</li>
<li><strong>quantity</strong>: Available quantity for this size (integer).</li>
</ul></li>
<li><strong>real_average_rating</strong>: Computed average rating from user ratings, rounded to 1 decimal (float).</li>
<li><strong>actions</strong>: Array of available actions for the product (implementation-specific, e.g., URLs or methods).</li>
</ul>
<h3>Notes for Frontend Developers</h3>
<ul>
<li><strong>Randomization</strong>: Products are returned in random order using <code>inRandomOrder()</code>. The selection may
vary with each request, and fewer than 4 products may be returned if there are not enough active products.</li>
<li><strong>Limit</strong>: The response is limited to 4 products. Ensure your frontend UI can handle fewer items (0-4).</li>
<li><strong>Locale Handling</strong>: The <code>name</code>, <code>category_name</code>, <code>description</code>, and <code>summary</code> fields are localized based
on the current app locale. Use the <code>X-Locale</code> header to override the default locale if needed.</li>
<li><strong>Nullable Fields</strong>: Fields like <code>category_name</code>, <code>color_name</code>, <code>size_name</code>, <code>after_discount_price</code>,
<code>feature_product_image</code>, and <code>second_feature_product_image</code> may be <code>null</code>. Provide fallback values
(e.g., &quot;N/A&quot; or a default image).</li>
<li><strong>Image URLs</strong>: The <code>image_url</code> in <code>variants</code> and media URLs are absolute URLs using the <code>storage</code>
directory. Ensure <code>/storage/</code> is accessible (run <code>php artisan storage:link</code> on the server).</li>
<li><strong>Ratings</strong>: <code>real_average_rating</code> is computed from user ratings, while <code>fake_average_rating</code> is a preset
value. Prefer <code>real_average_rating</code> for authenticity, or use <code>fake_average_rating</code> for display if set.</li>
<li><strong>Actions</strong>: The <code>actions</code> field is implementation-specific and may contain URLs or methods for actions
like &quot;add to cart&quot; or &quot;view details&quot;. Parse this field based on your frontend requirements.</li>
</ul>

<span id="example-requests-GETapi-recommended-products">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/recommended-products" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/recommended-products"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-recommended-products">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;recommended_products&quot;: [
        {
            &quot;id&quot;: 3,
            &quot;category_id&quot;: 1,
            &quot;category_name&quot;: &quot;Electronics&quot;,
            &quot;name&quot;: &quot;Wireless Headphones&quot;,
            &quot;price&quot;: 99.9899999999999948840923025272786617279052734375,
            &quot;after_discount_price&quot;: 79.9899999999999948840923025272786617279052734375,
            &quot;description&quot;: &quot;High-quality wireless headphones with noise cancellation.&quot;,
            &quot;slug&quot;: &quot;wireless-headphones&quot;,
            &quot;views&quot;: 200,
            &quot;sales&quot;: 50,
            &quot;fake_average_rating&quot;: 4.79999999999999982236431605997495353221893310546875,
            &quot;label_id&quot;: 2,
            &quot;summary&quot;: &quot;Immersive sound with long battery life.&quot;,
            &quot;quantity&quot;: 80,
            &quot;created_at&quot;: &quot;2025-05-04T12:00:00Z&quot;,
            &quot;updated_at&quot;: &quot;2025-05-04T12:00:00Z&quot;,
            &quot;media&quot;: {
                &quot;feature_product_image&quot;: &quot;https://yourapp.com/storage/images/headphones.jpg&quot;,
                &quot;second_feature_product_image&quot;: &quot;https://yourapp.com/storage/images/headphones-side.jpg&quot;
            },
            &quot;variants&quot;: [
                {
                    &quot;id&quot;: 5,
                    &quot;color_id&quot;: 2,
                    &quot;color_name&quot;: &quot;Black&quot;,
                    &quot;image_url&quot;: &quot;https://yourapp.com/storage/variants/headphones-black.jpg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;id&quot;: 7,
                            &quot;size_id&quot;: 1,
                            &quot;size_name&quot;: &quot;One Size&quot;,
                            &quot;quantity&quot;: 80
                        }
                    ]
                }
            ],
            &quot;real_average_rating&quot;: 4.5,
            &quot;actions&quot;: {
                &quot;view&quot;: &quot;https://yourapp.com/api/products/3&quot;,
                &quot;add_to_cart&quot;: &quot;https://yourapp.com/api/cart/add/3&quot;
            }
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;recommended_products&quot;: []
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Failed to retrieve recommended products. Please try again later.&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-recommended-products" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-recommended-products"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-recommended-products"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-recommended-products" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-recommended-products">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-recommended-products" data-method="GET"
      data-path="api/recommended-products"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-recommended-products', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-recommended-products"
                    onclick="tryItOut('GETapi-recommended-products');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-recommended-products"
                    onclick="cancelTryOut('GETapi-recommended-products');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-recommended-products"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/recommended-products</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-recommended-products"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-recommended-products"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-blogs">Retrieve a paginated list of active blogs.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs?per_page=16&amp;page=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs"
);

const params = {
    "per_page": "16",
    "page": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;title&quot;: &quot;Blog Title&quot;,
                &quot;slug&quot;: &quot;blog-slug&quot;,
                &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
                &quot;published_at&quot;: &quot;2023-01-01&quot;,
                &quot;category&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Category Name&quot;
                },
                &quot;author&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Author Name&quot;
                },
                &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
                &quot;likes_count&quot;: 10,
                &quot;tags&quot;: [
                    {&quot;id&quot;: 1, &quot;name&quot;: &quot;Tag Name&quot;},
                    ...
                ]
            },
            ...
        ],
        &quot;current_page&quot;: 1,
        &quot;last_page&quot;: 5,
        ...
    },
    &quot;message&quot;: &quot;Blogs retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs" data-method="GET"
      data-path="api/blogs"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs"
                    onclick="tryItOut('GETapi-blogs');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs"
                    onclick="cancelTryOut('GETapi-blogs');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-blogs"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs per page. Default: 10 Example: <code>16</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-blogs"
               value="16"
               data-component="query">
    <br>
<p>Page number. Default: 1 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs-categories">Retrieve all active blog categories with their active blogs count.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-categories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/categories" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/categories"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-categories">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Category Name&quot;,
            &quot;slug&quot;: &quot;category-slug&quot;,
            &quot;blogs_count&quot;: 5
        },
        ...
    ],
    &quot;message&quot;: &quot;Blog categories retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-categories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-categories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-categories" data-method="GET"
      data-path="api/blogs/categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-categories"
                    onclick="tryItOut('GETapi-blogs-categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-categories"
                    onclick="cancelTryOut('GETapi-blogs-categories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-categories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-blogs-popular">Retrieve popular blogs based on likes count.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-popular">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/popular?limit=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/popular"
);

const params = {
    "limit": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-popular">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Blog Title&quot;,
            &quot;slug&quot;: &quot;blog-slug&quot;,
            &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
            &quot;published_at&quot;: &quot;2023-01-01&quot;,
            &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
            &quot;likes_count&quot;: 10
        },
        ...
    ],
    &quot;message&quot;: &quot;Popular blogs retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-popular" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-popular"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-popular"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-popular" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-popular">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-popular" data-method="GET"
      data-path="api/blogs/popular"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-popular', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-popular"
                    onclick="tryItOut('GETapi-blogs-popular');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-popular"
                    onclick="cancelTryOut('GETapi-blogs-popular');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-popular"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/popular</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-popular"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-popular"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-blogs-popular"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs to return. Default: 5 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs-recent">Retrieve recent blogs based on publication date.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-recent">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/recent?limit=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/recent"
);

const params = {
    "limit": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-recent">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Blog Title&quot;,
            &quot;slug&quot;: &quot;blog-slug&quot;,
            &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
            &quot;published_at&quot;: &quot;2023-01-01&quot;,
            &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;
        },
        ...
    ],
    &quot;message&quot;: &quot;Recent blogs retrieved successfully&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-recent" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-recent"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-recent"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-recent" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-recent">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-recent" data-method="GET"
      data-path="api/blogs/recent"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-recent', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-recent"
                    onclick="tryItOut('GETapi-blogs-recent');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-recent"
                    onclick="cancelTryOut('GETapi-blogs-recent');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-recent"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/recent</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-recent"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-recent"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>limit</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="limit"                data-endpoint="GETapi-blogs-recent"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs to return. Default: 5 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs-search">Search blogs by title or content.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/search?query=architecto&amp;per_page=16&amp;page=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/search"
);

const params = {
    "query": "architecto",
    "per_page": "16",
    "page": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-search">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;title&quot;: &quot;Blog Title&quot;,
                &quot;slug&quot;: &quot;blog-slug&quot;,
                &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
                &quot;published_at&quot;: &quot;2023-01-01&quot;,
                &quot;author&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Author Name&quot;
                },
                &quot;category&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Category Name&quot;
                },
                &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
                &quot;likes_count&quot;: 10
            },
            ...
        ],
        &quot;search_query&quot;: &quot;search term&quot;,
        &quot;current_page&quot;: 1,
        &quot;last_page&quot;: 5,
        ...
    },
    &quot;message&quot;: &quot;Search results retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (400):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Search query is required&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-search" data-method="GET"
      data-path="api/blogs/search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-search"
                    onclick="tryItOut('GETapi-blogs-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-search"
                    onclick="cancelTryOut('GETapi-blogs-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>query</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="query"                data-endpoint="GETapi-blogs-search"
               value="architecto"
               data-component="query">
    <br>
<p>Search term Example: <code>architecto</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-blogs-search"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs per page. Default: 10 Example: <code>16</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-blogs-search"
               value="16"
               data-component="query">
    <br>
<p>Page number. Default: 1 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs-category--categorySlug-">Retrieve paginated blogs by category slug.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-category--categorySlug-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/category/t-shirt?per_page=16&amp;page=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/category/t-shirt"
);

const params = {
    "per_page": "16",
    "page": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-category--categorySlug-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;title&quot;: &quot;Blog Title&quot;,
                &quot;slug&quot;: &quot;blog-slug&quot;,
                &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
                &quot;published_at&quot;: &quot;2023-01-01&quot;,
                &quot;author&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Author Name&quot;
                },
                &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
                &quot;likes_count&quot;: 10,
                &quot;tags&quot;: [
                    {&quot;id&quot;: 1, &quot;name&quot;: &quot;Tag Name&quot;},
                    ...
                ]
            },
            ...
        ],
        &quot;category&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Category Name&quot;,
            &quot;slug&quot;: &quot;category-slug&quot;
        },
        &quot;current_page&quot;: 1,
        &quot;last_page&quot;: 5,
        ...
    },
    &quot;message&quot;: &quot;Blogs by category retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Category not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-category--categorySlug-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-category--categorySlug-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-category--categorySlug-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-category--categorySlug-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-category--categorySlug-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-category--categorySlug-" data-method="GET"
      data-path="api/blogs/category/{categorySlug}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-category--categorySlug-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-category--categorySlug-"
                    onclick="tryItOut('GETapi-blogs-category--categorySlug-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-category--categorySlug-"
                    onclick="cancelTryOut('GETapi-blogs-category--categorySlug-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-category--categorySlug-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/category/{categorySlug}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-category--categorySlug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-category--categorySlug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>categorySlug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="categorySlug"                data-endpoint="GETapi-blogs-category--categorySlug-"
               value="t-shirt"
               data-component="url">
    <br>
<p>Example: <code>t-shirt</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-blogs-category--categorySlug-"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs per page. Default: 10 Example: <code>16</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-blogs-category--categorySlug-"
               value="16"
               data-component="query">
    <br>
<p>Page number. Default: 1 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs-tag--tagId-">Retrieve paginated blogs by tag ID.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs-tag--tagId-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/tag/architecto?per_page=16&amp;page=16" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/tag/architecto"
);

const params = {
    "per_page": "16",
    "page": "16",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs-tag--tagId-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;data&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;title&quot;: &quot;Blog Title&quot;,
                &quot;slug&quot;: &quot;blog-slug&quot;,
                &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
                &quot;published_at&quot;: &quot;2023-01-01&quot;,
                &quot;author&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Author Name&quot;
                },
                &quot;category&quot;: {
                    &quot;id&quot;: 1,
                    &quot;name&quot;: &quot;Category Name&quot;
                },
                &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
                &quot;likes_count&quot;: 10
            },
            ...
        ],
        &quot;tag&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Tag Name&quot;
        },
        &quot;current_page&quot;: 1,
        &quot;last_page&quot;: 5,
        ...
    },
    &quot;message&quot;: &quot;Blogs by tag retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Tag not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs-tag--tagId-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs-tag--tagId-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs-tag--tagId-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs-tag--tagId-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs-tag--tagId-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs-tag--tagId-" data-method="GET"
      data-path="api/blogs/tag/{tagId}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs-tag--tagId-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs-tag--tagId-"
                    onclick="tryItOut('GETapi-blogs-tag--tagId-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs-tag--tagId-"
                    onclick="cancelTryOut('GETapi-blogs-tag--tagId-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs-tag--tagId-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/tag/{tagId}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs-tag--tagId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs-tag--tagId-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>tagId</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="tagId"                data-endpoint="GETapi-blogs-tag--tagId-"
               value="architecto"
               data-component="url">
    <br>
<p>Example: <code>architecto</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>per_page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="per_page"                data-endpoint="GETapi-blogs-tag--tagId-"
               value="16"
               data-component="query">
    <br>
<p>Number of blogs per page. Default: 10 Example: <code>16</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>page</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="page"                data-endpoint="GETapi-blogs-tag--tagId-"
               value="16"
               data-component="query">
    <br>
<p>Page number. Default: 1 Example: <code>16</code></p>
            </div>
                </form>

                    <h2 id="endpoints-GETapi-blogs--slug-">Retrieve a single blog post by its slug.</h2>

<p>
</p>



<span id="example-requests-GETapi-blogs--slug-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/blogs/english222" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/english222"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-blogs--slug-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;id&quot;: 1,
        &quot;title&quot;: &quot;Blog Title&quot;,
        &quot;slug&quot;: &quot;blog-slug&quot;,
        &quot;content&quot;: &quot;Blog content...&quot;,
        &quot;excerpt&quot;: &quot;Blog excerpt...&quot;,
        &quot;published_at&quot;: &quot;2023-01-01&quot;,
        &quot;category&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Category Name&quot;,
            &quot;slug&quot;: &quot;category-slug&quot;
        },
        &quot;author&quot;: {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Author Name&quot;,
            &quot;avatar&quot;: &quot;http://example.com/storage/avatar.jpg&quot;
        },
        &quot;image_url&quot;: &quot;http://example.com/image.jpg&quot;,
        &quot;likes_count&quot;: 10,
        &quot;is_liked&quot;: false,
        &quot;tags&quot;: [
            {&quot;id&quot;: 1, &quot;name&quot;: &quot;Tag Name&quot;},
            ...
        ],
        &quot;related_blogs&quot;: [
            {
                &quot;id&quot;: 2,
                &quot;title&quot;: &quot;Related Blog Title&quot;,
                &quot;slug&quot;: &quot;related-blog-slug&quot;,
                &quot;excerpt&quot;: &quot;Related blog excerpt...&quot;,
                &quot;published_at&quot;: &quot;2023-01-02&quot;,
                &quot;image_url&quot;: &quot;http://example.com/related-image.jpg&quot;
            },
            ...
        ]
    },
    &quot;message&quot;: &quot;Blog retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Blog not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-blogs--slug-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-blogs--slug-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-blogs--slug-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-blogs--slug-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-blogs--slug-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-blogs--slug-" data-method="GET"
      data-path="api/blogs/{slug}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-blogs--slug-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-blogs--slug-"
                    onclick="tryItOut('GETapi-blogs--slug-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-blogs--slug-"
                    onclick="cancelTryOut('GETapi-blogs--slug-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-blogs--slug-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/blogs/{slug}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-blogs--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-blogs--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="slug"                data-endpoint="GETapi-blogs--slug-"
               value="english222"
               data-component="url">
    <br>
<p>The slug of the blog. Example: <code>english222</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-blogs--blogId--like">Toggle like status for a blog post.</h2>

<p>
</p>



<span id="example-requests-POSTapi-blogs--blogId--like">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/blogs/english222/like" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/blogs/english222/like"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "POST",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-blogs--blogId--like">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;likes_count&quot;: 11,
        &quot;is_liked&quot;: true
    },
    &quot;message&quot;: &quot;Blog liked successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: {
        &quot;likes_count&quot;: 10,
        &quot;is_liked&quot;: false
    },
    &quot;message&quot;: &quot;Blog unliked successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (401):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Unauthenticated&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;Blog not found&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-blogs--blogId--like" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-blogs--blogId--like"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-blogs--blogId--like"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-blogs--blogId--like" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-blogs--blogId--like">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-blogs--blogId--like" data-method="POST"
      data-path="api/blogs/{blogId}/like"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-blogs--blogId--like', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-blogs--blogId--like"
                    onclick="tryItOut('POSTapi-blogs--blogId--like');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-blogs--blogId--like"
                    onclick="cancelTryOut('POSTapi-blogs--blogId--like');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-blogs--blogId--like"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/blogs/{blogId}/like</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-blogs--blogId--like"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-blogs--blogId--like"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>blogId</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="blogId"                data-endpoint="POSTapi-blogs--blogId--like"
               value="english222"
               data-component="url">
    <br>
<p>Example: <code>english222</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-GETapi-global-search">Global Search API</h2>

<p>
</p>

<p>Search across products, categories, blogs, discounts, and tags.</p>

<span id="example-requests-GETapi-global-search">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/global-search?query=phone" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"query\": \"b\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/global-search"
);

const params = {
    "query": "phone",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "query": "b"
};

fetch(url, {
    method: "GET",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-global-search">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;products&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Product Name&quot;,
                &quot;summary&quot;: &quot;Product Summary&quot;,
                &quot;labels&quot;: [&quot;label1&quot;, &quot;label2&quot;]
            },
            ...
        ],
        &quot;categories&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Category Name&quot;
            },
            ...
        ],
        &quot;blogs&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;title&quot;: &quot;Blog Title&quot;,
                &quot;slug&quot;: &quot;blog-slug&quot;,
                &quot;published_at&quot;: &quot;2025-05-07T00:00:00Z&quot;
            },
            ...
        ],
        &quot;discounts&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Discount Name&quot;,
                &quot;description&quot;: &quot;Discount Description&quot;,
                &quot;discount_type&quot;: &quot;percentage&quot;,
                &quot;value&quot;: 10.00
            },
            ...
        ],
        &quot;tags&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name_ar&quot;: &quot;تاغ&quot;,
                &quot;name_en&quot;: &quot;Tag&quot;
            },
            ...
        ]
    },
    &quot;meta&quot;: {
        &quot;timestamp&quot;: &quot;2025-05-07T12:34:56Z&quot;,
        &quot;query&quot;: &quot;search term&quot;
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The query field must be at least 2 characters.&quot;,
    &quot;errors&quot;: {
        &quot;query&quot;: [
            &quot;The query field must be at least 2 characters.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-global-search" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-global-search"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-global-search"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-global-search" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-global-search">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-global-search" data-method="GET"
      data-path="api/global-search"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-global-search', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-global-search"
                    onclick="tryItOut('GETapi-global-search');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-global-search"
                    onclick="cancelTryOut('GETapi-global-search');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-global-search"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/global-search</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-global-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-global-search"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>query</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="query"                data-endpoint="GETapi-global-search"
               value="phone"
               data-component="query">
    <br>
<p>The search term (min: 2, max: 255 characters). Example: <code>phone</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>query</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="query"                data-endpoint="GETapi-global-search"
               value="b"
               data-component="body">
    <br>
<p>Must be at least 2 characters. Must not be greater than 255 characters. Example: <code>b</code></p>
        </div>
        </form>

                    <h2 id="endpoints-GETapi-top-bars">GET api/top-bars</h2>

<p>
</p>



<span id="example-requests-GETapi-top-bars">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/top-bars" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/top-bars"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-top-bars">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;status&quot;: true,
    &quot;data&quot;: [
        {
            &quot;content&quot;: &quot;Profile Settings\nProfile Settings\nProfile Settings\nProfile Settings\nProfile Settings\nProfile Settings&quot;
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-top-bars" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-top-bars"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-top-bars"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-top-bars" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-top-bars">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-top-bars" data-method="GET"
      data-path="api/top-bars"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-top-bars', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-top-bars"
                    onclick="tryItOut('GETapi-top-bars');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-top-bars"
                    onclick="cancelTryOut('GETapi-top-bars');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-top-bars"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/top-bars</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-top-bars"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-top-bars"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-footer-contact-info">GET api/footer/contact-info</h2>

<p>
</p>



<span id="example-requests-GETapi-footer-contact-info">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/footer/contact-info" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/footer/contact-info"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-footer-contact-info">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;address&quot;: &quot;I am at Egypt now&quot;,
    &quot;phone&quot;: &quot;+201111111111&quot;,
    &quot;email&quot;: &quot;info@pikyhost.com&quot;,
    &quot;social_media&quot;: {
        &quot;facebook&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;,
        &quot;youtube&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;,
        &quot;instagram&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;,
        &quot;x&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;,
        &quot;snapchat&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;,
        &quot;tiktok&quot;: &quot;https://www.facebook.com/share/1JDi1HsuvB/&quot;
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-footer-contact-info" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-footer-contact-info"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-footer-contact-info"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-footer-contact-info" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-footer-contact-info">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-footer-contact-info" data-method="GET"
      data-path="api/footer/contact-info"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-footer-contact-info', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-footer-contact-info"
                    onclick="tryItOut('GETapi-footer-contact-info');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-footer-contact-info"
                    onclick="cancelTryOut('GETapi-footer-contact-info');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-footer-contact-info"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/footer/contact-info</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-footer-contact-info"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-footer-contact-info"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-service-features">GET api/service-features</h2>

<p>
</p>



<span id="example-requests-GETapi-service-features">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/service-features" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/service-features"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-service-features">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">[
    {
        &quot;id&quot;: 6,
        &quot;title&quot;: &quot;Customer Support&quot;,
        &quot;subtitle&quot;: &quot;Need Assistance?&quot;,
        &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/support-icon.svg&quot;
    },
    {
        &quot;id&quot;: 7,
        &quot;title&quot;: &quot;Secured Payment&quot;,
        &quot;subtitle&quot;: &quot;Safe &amp; Fast&quot;,
        &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/payment-icon.svg&quot;
    },
    {
        &quot;id&quot;: 8,
        &quot;title&quot;: &quot;Free Returns&quot;,
        &quot;subtitle&quot;: &quot;Easy &amp; Free&quot;,
        &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/returns-icon.svg&quot;
    },
    {
        &quot;id&quot;: 9,
        &quot;title&quot;: &quot;Free Shipping&quot;,
        &quot;subtitle&quot;: &quot;Made To Help You&quot;,
        &quot;image_url&quot;: &quot;https://backend.sopdakt.com/storage/01JVN77B5Y0ZJEBZDYA95ZCHK7.JPG&quot;
    }
]</code>
 </pre>
    </span>
<span id="execution-results-GETapi-service-features" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-service-features"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-service-features"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-service-features" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-service-features">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-service-features" data-method="GET"
      data-path="api/service-features"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-service-features', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-service-features"
                    onclick="tryItOut('GETapi-service-features');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-service-features"
                    onclick="cancelTryOut('GETapi-service-features');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-service-features"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/service-features</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-service-features"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-service-features"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="endpoints-GETapi-products--product_slug--ratings">GET api/products/{product_slug}/ratings</h2>

<p>
</p>



<span id="example-requests-GETapi-products--product_slug--ratings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/shirt/ratings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/shirt/ratings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products--product_slug--ratings">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <details class="annotation">
            <summary style="cursor: pointer;">
                <small onclick="textContent = parentElement.parentElement.open ? 'Show headers' : 'Hide headers'">Show headers</small>
            </summary>
            <pre><code class="language-http">cache-control: no-cache, private
content-type: application/json
vary: Origin
 </code></pre></details>         <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;current_page&quot;: 1,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 2,
            &quot;product_id&quot;: 10,
            &quot;user_id&quot;: 21,
            &quot;rating&quot;: 4,
            &quot;comment&quot;: &quot;this is rating&quot;,
            &quot;status&quot;: &quot;approved&quot;,
            &quot;created_at&quot;: &quot;2025-05-20T08:27:02.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-05-20T08:27:02.000000Z&quot;
        }
    ],
    &quot;first_page_url&quot;: &quot;https://backend.sopdakt.com/api/products/shirt/ratings?page=1&quot;,
    &quot;from&quot;: 1,
    &quot;last_page&quot;: 1,
    &quot;last_page_url&quot;: &quot;https://backend.sopdakt.com/api/products/shirt/ratings?page=1&quot;,
    &quot;links&quot;: [
        {
            &quot;url&quot;: null,
            &quot;label&quot;: &quot;&amp;laquo; Previous&quot;,
            &quot;active&quot;: false
        },
        {
            &quot;url&quot;: &quot;https://backend.sopdakt.com/api/products/shirt/ratings?page=1&quot;,
            &quot;label&quot;: &quot;1&quot;,
            &quot;active&quot;: true
        },
        {
            &quot;url&quot;: null,
            &quot;label&quot;: &quot;Next &amp;raquo;&quot;,
            &quot;active&quot;: false
        }
    ],
    &quot;next_page_url&quot;: null,
    &quot;path&quot;: &quot;https://backend.sopdakt.com/api/products/shirt/ratings&quot;,
    &quot;per_page&quot;: 5,
    &quot;prev_page_url&quot;: null,
    &quot;to&quot;: 1,
    &quot;total&quot;: 1
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products--product_slug--ratings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products--product_slug--ratings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products--product_slug--ratings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products--product_slug--ratings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products--product_slug--ratings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products--product_slug--ratings" data-method="GET"
      data-path="api/products/{product_slug}/ratings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products--product_slug--ratings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products--product_slug--ratings"
                    onclick="tryItOut('GETapi-products--product_slug--ratings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products--product_slug--ratings"
                    onclick="cancelTryOut('GETapi-products--product_slug--ratings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products--product_slug--ratings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/{product_slug}/ratings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products--product_slug--ratings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products--product_slug--ratings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>product_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="product_slug"                data-endpoint="GETapi-products--product_slug--ratings"
               value="shirt"
               data-component="url">
    <br>
<p>The slug of the product. Example: <code>shirt</code></p>
            </div>
                    </form>

                    <h2 id="endpoints-POSTapi-products--product_slug--ratings">POST api/products/{product_slug}/ratings</h2>

<p>
</p>



<span id="example-requests-POSTapi-products--product_slug--ratings">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/products/shirt/ratings" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"rating\": 1,
    \"comment\": \"ngzmiyvdljnikhwa\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/shirt/ratings"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "rating": 1,
    "comment": "ngzmiyvdljnikhwa"
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-products--product_slug--ratings">
</span>
<span id="execution-results-POSTapi-products--product_slug--ratings" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-products--product_slug--ratings"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-products--product_slug--ratings"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-products--product_slug--ratings" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-products--product_slug--ratings">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-products--product_slug--ratings" data-method="POST"
      data-path="api/products/{product_slug}/ratings"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-products--product_slug--ratings', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-products--product_slug--ratings"
                    onclick="tryItOut('POSTapi-products--product_slug--ratings');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-products--product_slug--ratings"
                    onclick="cancelTryOut('POSTapi-products--product_slug--ratings');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-products--product_slug--ratings"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/products/{product_slug}/ratings</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-products--product_slug--ratings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-products--product_slug--ratings"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>product_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="product_slug"                data-endpoint="POSTapi-products--product_slug--ratings"
               value="shirt"
               data-component="url">
    <br>
<p>The slug of the product. Example: <code>shirt</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rating</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rating"                data-endpoint="POSTapi-products--product_slug--ratings"
               value="1"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 5. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>comment</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="comment"                data-endpoint="POSTapi-products--product_slug--ratings"
               value="ngzmiyvdljnikhwa"
               data-component="body">
    <br>
<p>Must be at least 1 character. Example: <code>ngzmiyvdljnikhwa</code></p>
        </div>
        </form>

                    <h2 id="endpoints-PUTapi-products--product_slug--ratings--rating_id-">PUT api/products/{product_slug}/ratings/{rating_id}</h2>

<p>
</p>



<span id="example-requests-PUTapi-products--product_slug--ratings--rating_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request PUT \
    "https://backend.sopdakt.com/api/products/shirt/ratings/2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"rating\": 1,
    \"comment\": \"ngzmiyvdljnikhwa\"
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/shirt/ratings/2"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "rating": 1,
    "comment": "ngzmiyvdljnikhwa"
};

fetch(url, {
    method: "PUT",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-PUTapi-products--product_slug--ratings--rating_id-">
</span>
<span id="execution-results-PUTapi-products--product_slug--ratings--rating_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-PUTapi-products--product_slug--ratings--rating_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-PUTapi-products--product_slug--ratings--rating_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-PUTapi-products--product_slug--ratings--rating_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-PUTapi-products--product_slug--ratings--rating_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-PUTapi-products--product_slug--ratings--rating_id-" data-method="PUT"
      data-path="api/products/{product_slug}/ratings/{rating_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('PUTapi-products--product_slug--ratings--rating_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-PUTapi-products--product_slug--ratings--rating_id-"
                    onclick="tryItOut('PUTapi-products--product_slug--ratings--rating_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-PUTapi-products--product_slug--ratings--rating_id-"
                    onclick="cancelTryOut('PUTapi-products--product_slug--ratings--rating_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-PUTapi-products--product_slug--ratings--rating_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-darkblue">PUT</small>
            <b><code>api/products/{product_slug}/ratings/{rating_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>product_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="product_slug"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="shirt"
               data-component="url">
    <br>
<p>The slug of the product. Example: <code>shirt</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>rating_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rating_id"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the rating. Example: <code>2</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>rating</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rating"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="1"
               data-component="body">
    <br>
<p>Must be at least 1. Must not be greater than 5. Example: <code>1</code></p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>comment</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="comment"                data-endpoint="PUTapi-products--product_slug--ratings--rating_id-"
               value="ngzmiyvdljnikhwa"
               data-component="body">
    <br>
<p>Must be at least 1 character. Example: <code>ngzmiyvdljnikhwa</code></p>
        </div>
        </form>

                    <h2 id="endpoints-DELETEapi-products--product_slug--ratings--rating_id-">DELETE api/products/{product_slug}/ratings/{rating_id}</h2>

<p>
</p>



<span id="example-requests-DELETEapi-products--product_slug--ratings--rating_id-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request DELETE \
    "https://backend.sopdakt.com/api/products/shirt/ratings/2" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/shirt/ratings/2"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "DELETE",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-DELETEapi-products--product_slug--ratings--rating_id-">
</span>
<span id="execution-results-DELETEapi-products--product_slug--ratings--rating_id-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-DELETEapi-products--product_slug--ratings--rating_id-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-DELETEapi-products--product_slug--ratings--rating_id-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-DELETEapi-products--product_slug--ratings--rating_id-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-DELETEapi-products--product_slug--ratings--rating_id-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-DELETEapi-products--product_slug--ratings--rating_id-" data-method="DELETE"
      data-path="api/products/{product_slug}/ratings/{rating_id}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('DELETEapi-products--product_slug--ratings--rating_id-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-DELETEapi-products--product_slug--ratings--rating_id-"
                    onclick="tryItOut('DELETEapi-products--product_slug--ratings--rating_id-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-DELETEapi-products--product_slug--ratings--rating_id-"
                    onclick="cancelTryOut('DELETEapi-products--product_slug--ratings--rating_id-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-DELETEapi-products--product_slug--ratings--rating_id-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-red">DELETE</small>
            <b><code>api/products/{product_slug}/ratings/{rating_id}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="DELETEapi-products--product_slug--ratings--rating_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="DELETEapi-products--product_slug--ratings--rating_id-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>product_slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="product_slug"                data-endpoint="DELETEapi-products--product_slug--ratings--rating_id-"
               value="shirt"
               data-component="url">
    <br>
<p>The slug of the product. Example: <code>shirt</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>rating_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="rating_id"                data-endpoint="DELETEapi-products--product_slug--ratings--rating_id-"
               value="2"
               data-component="url">
    <br>
<p>The ID of the rating. Example: <code>2</code></p>
            </div>
                    </form>

                <h1 id="homepage">Homepage</h1>

    <p>Retrieves a list of featured categories for the homepage with their images.</p>

                                <h2 id="homepage-GETapi-home-featured-categories">Get Featured Categories</h2>

<p>
</p>



<span id="example-requests-GETapi-home-featured-categories">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/home/featured-categories" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/home/featured-categories"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-home-featured-categories">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;name&quot;: &quot;Electronics&quot;,
            &quot;image_url&quot;: &quot;https://example.com/media/categories/electronics.jpg&quot;
        },
        {
            &quot;name&quot;: &quot;Fashion&quot;,
            &quot;image_url&quot;: &quot;https://example.com/media/categories/fashion.jpg&quot;
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: false,
    &quot;message&quot;: &quot;Server Error&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-home-featured-categories" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-home-featured-categories"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-home-featured-categories"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-home-featured-categories" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-home-featured-categories">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-home-featured-categories" data-method="GET"
      data-path="api/home/featured-categories"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-home-featured-categories', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-home-featured-categories"
                    onclick="tryItOut('GETapi-home-featured-categories');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-home-featured-categories"
                    onclick="cancelTryOut('GETapi-home-featured-categories');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-home-featured-categories"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/home/featured-categories</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-home-featured-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-home-featured-categories"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="homepage-GETapi-products-fakeBestSellers">Get Best Selling Products</h2>

<p>
</p>



<span id="example-requests-GETapi-products-fakeBestSellers">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/fakeBestSellers" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/fakeBestSellers"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products-fakeBestSellers">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;success&quot;: true,
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Premium Headphones&quot;,
            &quot;price&quot;: 199.990000000000009094947017729282379150390625,
            &quot;after_discount_price&quot;: 179.990000000000009094947017729282379150390625,
            &quot;sales&quot;: 150,
            &quot;slug&quot;: &quot;premium-headphones&quot;,
            &quot;image_url&quot;: &quot;https://example.com/media/products/headphones.jpg&quot;,
            &quot;category&quot;: {
                &quot;name&quot;: &quot;Electronics&quot;,
                &quot;slug&quot;: &quot;electronics&quot;
            },
            &quot;colors_with_sizes&quot;: [
                {
                    &quot;color_name&quot;: &quot;Black&quot;,
                    &quot;color_code&quot;: &quot;#000000&quot;,
                    &quot;color_image&quot;: null,
                    &quot;sizes&quot;: [
                        {
                            &quot;size_name&quot;: &quot;One Size&quot;,
                            &quot;quantity&quot;: 50
                        }
                    ]
                }
            ],
            &quot;actions&quot;: {
                &quot;add_to_cart&quot;: &quot;http://example.com/api/cart/1&quot;,
                &quot;toggle_love&quot;: &quot;http://example.com/api/wishlist/toggle/1&quot;,
                &quot;compare&quot;: &quot;http://example.com/api/compare/1&quot;,
                &quot;view&quot;: &quot;http://example.com/products/premium-headphones&quot;
            }
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products-fakeBestSellers" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products-fakeBestSellers"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products-fakeBestSellers"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products-fakeBestSellers" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products-fakeBestSellers">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products-fakeBestSellers" data-method="GET"
      data-path="api/products/fakeBestSellers"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products-fakeBestSellers', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products-fakeBestSellers"
                    onclick="tryItOut('GETapi-products-fakeBestSellers');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products-fakeBestSellers"
                    onclick="cancelTryOut('GETapi-products-fakeBestSellers');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products-fakeBestSellers"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/fakeBestSellers</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products-fakeBestSellers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products-fakeBestSellers"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="policies">Policies</h1>

    

                                <h2 id="policies-GETapi-policies-privacy">Retrieve the Privacy Policy</h2>

<p>
</p>

<p>This endpoint fetches the Privacy Policy content in the current application locale (English or Arabic).
The response includes the policy content and the locale used. If no policy data is found, an error
message is returned indicating that the policy is null or empty.</p>

<span id="example-requests-GETapi-policies-privacy">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/policies/privacy" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/policies/privacy"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-policies-privacy">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# Privacy Policy in English&quot;,
        &quot;locale&quot;: &quot;en&quot;
    },
    &quot;message&quot;: &quot;Privacy Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# سياسة الخصوصية بالعربية&quot;,
        &quot;locale&quot;: &quot;ar&quot;
    },
    &quot;message&quot;: &quot;Privacy Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Privacy Policy is null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving the Privacy Policy. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-policies-privacy" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-policies-privacy"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-policies-privacy"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-policies-privacy" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-policies-privacy">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-policies-privacy" data-method="GET"
      data-path="api/policies/privacy"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-policies-privacy', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-policies-privacy"
                    onclick="tryItOut('GETapi-policies-privacy');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-policies-privacy"
                    onclick="cancelTryOut('GETapi-policies-privacy');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-policies-privacy"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/policies/privacy</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-policies-privacy"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-policies-privacy"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="policies-GETapi-policies-refund">Retrieve the Refund Policy</h2>

<p>
</p>

<p>This endpoint fetches the Refund Policy content in the current application locale (English or Arabic).
The response includes the policy content and the locale used. If no policy data is found, an error
message is returned indicating that the policy is null or empty.</p>

<span id="example-requests-GETapi-policies-refund">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/policies/refund" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/policies/refund"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-policies-refund">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# Refund Policy in English&quot;,
        &quot;locale&quot;: &quot;en&quot;
    },
    &quot;message&quot;: &quot;Refund Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# سياسة الاسترجاع بالعربية&quot;,
        &quot;locale&quot;: &quot;ar&quot;
    },
    &quot;message&quot;: &quot;Refund Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Refund Policy is null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving the Refund Policy. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-policies-refund" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-policies-refund"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-policies-refund"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-policies-refund" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-policies-refund">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-policies-refund" data-method="GET"
      data-path="api/policies/refund"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-policies-refund', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-policies-refund"
                    onclick="tryItOut('GETapi-policies-refund');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-policies-refund"
                    onclick="cancelTryOut('GETapi-policies-refund');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-policies-refund"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/policies/refund</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-policies-refund"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-policies-refund"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="policies-GETapi-policies-terms">Retrieve the Terms of Service</h2>

<p>
</p>

<p>This endpoint fetches the Terms of Service content in the current application locale (English or Arabic).
The response includes the policy content and the locale used. If no policy data is found, an error
message is returned indicating that the policy is null or empty.</p>

<span id="example-requests-GETapi-policies-terms">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/policies/terms" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/policies/terms"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-policies-terms">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# Terms of Service in English&quot;,
        &quot;locale&quot;: &quot;en&quot;
    },
    &quot;message&quot;: &quot;Terms of Service retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# شروط الخدمة بالعربية&quot;,
        &quot;locale&quot;: &quot;ar&quot;
    },
    &quot;message&quot;: &quot;Terms of Service retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Terms of Service is null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving the Terms of Service. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-policies-terms" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-policies-terms"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-policies-terms"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-policies-terms" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-policies-terms">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-policies-terms" data-method="GET"
      data-path="api/policies/terms"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-policies-terms', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-policies-terms"
                    onclick="tryItOut('GETapi-policies-terms');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-policies-terms"
                    onclick="cancelTryOut('GETapi-policies-terms');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-policies-terms"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/policies/terms</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-policies-terms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-policies-terms"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="policies-GETapi-policies-shipping">Retrieve the Shipping Policy</h2>

<p>
</p>

<p>This endpoint fetches the Shipping Policy content in the current application locale (English or Arabic).
The response includes the policy content and the locale used. If no policy data is found, an error
message is returned indicating that the policy is null or empty.</p>

<span id="example-requests-GETapi-policies-shipping">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/policies/shipping" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/policies/shipping"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-policies-shipping">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# Shipping Policy in English&quot;,
        &quot;locale&quot;: &quot;en&quot;
    },
    &quot;message&quot;: &quot;Shipping Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;content&quot;: &quot;# سياسة الشحن بالعربية&quot;,
        &quot;locale&quot;: &quot;ar&quot;
    },
    &quot;message&quot;: &quot;Shipping Policy retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;Shipping Policy is null or empty&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving the Shipping Policy. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-policies-shipping" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-policies-shipping"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-policies-shipping"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-policies-shipping" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-policies-shipping">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-policies-shipping" data-method="GET"
      data-path="api/policies/shipping"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-policies-shipping', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-policies-shipping"
                    onclick="tryItOut('GETapi-policies-shipping');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-policies-shipping"
                    onclick="cancelTryOut('GETapi-policies-shipping');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-policies-shipping"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/policies/shipping</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-policies-shipping"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-policies-shipping"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="popups">Popups</h1>

    

                                <h2 id="popups-GETapi-popups">Retrieve all active popups</h2>

<p>
</p>

<p>This endpoint fetches all active popups configured in the system, intended for display on the frontend.
Each popup includes translatable fields (title, description, cta_text) in the current application locale
(English or Arabic), along with configuration details such as image, call-to-action link, display rules,
timing settings, and specific pages. The response is designed to provide React developers with all necessary
data to implement popup rendering, timing, and user interaction logic (e.g., delay, duration, &quot;don't show again&quot;).
If no active popups are found, an error message is returned.</p>

<span id="example-requests-GETapi-popups">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/popups" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/popups"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-popups">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;title&quot;: &quot;Welcome Offer&quot;,
            &quot;description&quot;: &quot;Get 20% off your first purchase!&quot;,
            &quot;image_path&quot;: &quot;https://your-domain.com/storage/popups/welcome.jpg&quot;,
            &quot;cta_text&quot;: &quot;Shop Now&quot;,
            &quot;cta_link&quot;: &quot;/shop&quot;,
            &quot;is_active&quot;: true,
            &quot;email_needed&quot;: false,
            &quot;display_rules&quot;: &quot;all_pages&quot;,
            &quot;popup_order&quot;: 0,
            &quot;show_interval_minutes&quot;: 60,
            &quot;delay_seconds&quot;: 60,
            &quot;duration_seconds&quot;: 60,
            &quot;dont_show_again_days&quot;: 7,
            &quot;specific_pages&quot;: null,
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        },
        {
            &quot;id&quot;: 2,
            &quot;title&quot;: &quot;Newsletter Signup&quot;,
            &quot;description&quot;: &quot;Subscribe to our newsletter for exclusive updates.&quot;,
            &quot;image_path&quot;: null,
            &quot;cta_text&quot;: &quot;Subscribe&quot;,
            &quot;cta_link&quot;: &quot;/subscribe&quot;,
            &quot;is_active&quot;: true,
            &quot;email_needed&quot;: true,
            &quot;display_rules&quot;: &quot;specific_pages&quot;,
            &quot;popup_order&quot;: 1,
            &quot;show_interval_minutes&quot;: 120,
            &quot;delay_seconds&quot;: 30,
            &quot;duration_seconds&quot;: 120,
            &quot;dont_show_again_days&quot;: 14,
            &quot;specific_pages&quot;: [
                &quot;/home&quot;,
                &quot;/about&quot;
            ],
            &quot;created_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2025-04-30T12:00:00.000000Z&quot;
        }
    ],
    &quot;message&quot;: &quot;Active popups retrieved successfully&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;No active popups found&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: &quot;An unexpected error occurred while retrieving popups. Please try again.&quot;,
    &quot;support_link&quot;: &quot;https://your-domain.com/contact-us&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-popups" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-popups"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-popups"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-popups" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-popups">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-popups" data-method="GET"
      data-path="api/popups"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-popups', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-popups"
                    onclick="tryItOut('GETapi-popups');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-popups"
                    onclick="cancelTryOut('GETapi-popups');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-popups"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/popups</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-popups"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-popups"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                <h1 id="products">Products</h1>

    <p>APIs for managing product comparison</p>

                                <h2 id="products-GETapi-products--id--colors-sizes">Get all color and size variants for a product.</h2>

<p>
</p>

<p>This endpoint returns all color variants for a given product, each including:</p>
<ul>
<li>Color information (ID, name)</li>
<li>Associated image (if available)</li>
<li>Size variants under each color, including:
<ul>
<li>Size ID</li>
<li>Size name</li>
<li>Available quantity</li>
</ul></li>
</ul>
<p>This structure matches the <code>variants</code> format used in the product detail endpoint.</p>

<span id="example-requests-GETapi-products--id--colors-sizes">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/shirt/colors-sizes" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/shirt/colors-sizes"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products--id--colors-sizes">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;variants&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;color_id&quot;: 3,
            &quot;color_name&quot;: &quot;Red&quot;,
            &quot;image_url&quot;: &quot;https://example.com/storage/variant1.jpg&quot;,
            &quot;sizes&quot;: [
                {
                    &quot;id&quot;: 5,
                    &quot;size_id&quot;: 2,
                    &quot;size_name&quot;: &quot;L&quot;,
                    &quot;quantity&quot;: 8
                },
                {
                    &quot;id&quot;: 6,
                    &quot;size_id&quot;: 3,
                    &quot;size_name&quot;: &quot;XL&quot;,
                    &quot;quantity&quot;: 4
                }
            ]
        }
    ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products--id--colors-sizes" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products--id--colors-sizes"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products--id--colors-sizes"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products--id--colors-sizes" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products--id--colors-sizes">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products--id--colors-sizes" data-method="GET"
      data-path="api/products/{id}/colors-sizes"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products--id--colors-sizes', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products--id--colors-sizes"
                    onclick="tryItOut('GETapi-products--id--colors-sizes');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products--id--colors-sizes"
                    onclick="cancelTryOut('GETapi-products--id--colors-sizes');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products--id--colors-sizes"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/{id}/colors-sizes</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products--id--colors-sizes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products--id--colors-sizes"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="id"                data-endpoint="GETapi-products--id--colors-sizes"
               value="shirt"
               data-component="url">
    <br>
<p>The ID of the product. Example: <code>shirt</code></p>
            </div>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>product</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="product"                data-endpoint="GETapi-products--id--colors-sizes"
               value="1"
               data-component="url">
    <br>
<p>The ID of the product. Example: <code>1</code></p>
            </div>
                    </form>

                    <h2 id="products-POSTapi-compare">Compare Products</h2>

<p>
</p>

<p>Compares multiple products side-by-side by their IDs.</p>

<span id="example-requests-POSTapi-compare">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/compare" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"product_ids\": [
        1,
        2,
        3
    ]
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/compare"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "product_ids": [
        1,
        2,
        3
    ]
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-compare">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;products&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;name&quot;: &quot;Premium Headphones&quot;,
            &quot;description&quot;: &quot;Noise-cancelling wireless headphones&quot;,
            &quot;meta_title&quot;: &quot;Best Wireless Headphones&quot;,
            &quot;meta_description&quot;: &quot;Top-quality wireless headphones with noise cancellation&quot;,
            &quot;price&quot;: 199,
            &quot;after_discount_price&quot;: 179,
            &quot;slug&quot;: &quot;premium-headphones&quot;,
            &quot;quantity&quot;: 25,
            &quot;sku&quot;: &quot;HD2024&quot;,
            &quot;cost&quot;: 120,
            &quot;shipping_estimate_time&quot;: &quot;2-4 days&quot;,
            &quot;discount_start&quot;: &quot;2025-04-01 00:00:00&quot;,
            &quot;discount_end&quot;: &quot;2025-04-30 23:59:59&quot;,
            &quot;views&quot;: 1280,
            &quot;sales&quot;: 430,
            &quot;fake_average_rating&quot;: 4,
            &quot;summary&quot;: &quot;Great sound quality in a compact design&quot;,
            &quot;custom_attributes&quot;: {
                &quot;color&quot;: &quot;black&quot;,
                &quot;battery_life&quot;: &quot;30 hours&quot;
            },
            &quot;is_published&quot;: true,
            &quot;is_featured&quot;: false,
            &quot;is_free_shipping&quot;: true
        }
    ]
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;The given data was invalid.&quot;,
    &quot;errors&quot;: {
        &quot;product_ids&quot;: [
            &quot;The product ids field is required.&quot;,
            &quot;The product ids must be an array.&quot;,
            &quot;The product ids must have at least 2 items.&quot;
        ],
        &quot;product_ids.0&quot;: [
            &quot;The selected product_ids.0 is invalid.&quot;
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-compare" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-compare"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-compare"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-compare" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-compare">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-compare" data-method="POST"
      data-path="api/compare"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-compare', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-compare"
                    onclick="tryItOut('POSTapi-compare');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-compare"
                    onclick="cancelTryOut('POSTapi-compare');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-compare"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/compare</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-compare"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-compare"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>product_ids</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>Array of product IDs to compare (minimum 2 products).</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>*</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="product_ids.*"                data-endpoint="POSTapi-compare"
               value="16"
               data-component="body">
    <br>
<p>Each ID must exist in the products table. Example: <code>16</code></p>
                    </div>
                                    </details>
        </div>
        </form>

                    <h2 id="products-GETapi-products-featured">Get up to 3 featured published products.</h2>

<p>
</p>

<p>This endpoint retrieves a limited list of featured and published products.
It returns localized fields based on the <code>Accept-Language</code> header and includes:</p>
<ul>
<li>Localized fields (<code>name</code>, <code>description</code>, <code>summary</code>, <code>meta_title</code>, etc.)</li>
<li>Related user and category names</li>
<li>Product media (feature image, secondary image, and more)</li>
<li>Frontend action URLs (e.g., add to cart, wishlist)</li>
</ul>

<span id="example-requests-GETapi-products-featured">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/featured" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/featured"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products-featured">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
  &quot;products&quot;: [
    {
      &quot;id&quot;: 1,
      &quot;name&quot;: &quot;Localized name&quot;,
      &quot;description&quot;: &quot;Localized description...&quot;,
      ...
      &quot;media&quot;: {
        &quot;feature_product_image&quot;: &quot;https://example.com/storage/feature.jpg&quot;,
        &quot;second_feature_product_image&quot;: &quot;https://example.com/storage/feature2.jpg&quot;,
        &quot;more_product_images_and_videos&quot;: [
          &quot;https://example.com/storage/image1.jpg&quot;,
          &quot;https://example.com/storage/video1.mp4&quot;
        ]
      },
      &quot;actions&quot;: {
        &quot;add_to_cart&quot;: {
          &quot;method&quot;: &quot;POST&quot;,
          &quot;url&quot;: &quot;https://example.com/api/cart&quot;
        },
        ...
      }
    }
  ]
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products-featured" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products-featured"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products-featured"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products-featured" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products-featured">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products-featured" data-method="GET"
      data-path="api/products/featured"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products-featured', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products-featured"
                    onclick="tryItOut('GETapi-products-featured');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products-featured"
                    onclick="cancelTryOut('GETapi-products-featured');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products-featured"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/featured</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products-featured"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products-featured"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        </form>

                    <h2 id="products-GETapi-products--slug-">Get a single published product by its slug.</h2>

<p>
</p>

<p>This endpoint retrieves a product using its unique slug. The response includes:</p>
<ul>
<li>Localized fields (<code>name</code>, <code>description</code>, <code>summary</code>, <code>meta_title</code>, etc.) based on the <code>Accept-Language</code> header.</li>
<li>Associated user and category names.</li>
<li>Media including feature image, secondary image, sizes image, and more images/videos.</li>
<li>Size guide (if any) with title, description, and image URL.</li>
<li>Variants (product colors) with nested sizes and quantity.</li>
<li>Labels (with localized titles and color codes).</li>
<li>Bundles the product is part of (optional).</li>
<li>Real average rating based on reviews.</li>
<li>Action endpoints with methods for cart, wishlist, and comparison.</li>
</ul>

<span id="example-requests-GETapi-products--slug-">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/products/&amp;quot;smartphone-2025&amp;quot;" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/products/&amp;quot;smartphone-2025&amp;quot;"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-products--slug-">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
  &quot;product&quot;: {
    &quot;id&quot;: 1,
    &quot;user_name&quot;: &quot;Admin&quot;,
    &quot;category_name&quot;: &quot;Accessories&quot;,
    &quot;name&quot;: &quot;Localized Product Name&quot;,
    ...
    &quot;media&quot;: {
      &quot;feature_product_image&quot;: &quot;https://example.com/storage/feature.jpg&quot;,
      &quot;second_feature_product_image&quot;: &quot;https://example.com/storage/feature2.jpg&quot;,
      &quot;sizes_image&quot;: &quot;https://example.com/storage/sizes.jpg&quot;,
      &quot;more_product_images_and_videos&quot;: [...]
    },
    &quot;size_guide&quot;: {
      &quot;title&quot;: &quot;Size Chart&quot;,
      &quot;description&quot;: &quot;Details...&quot;,
      &quot;image_url&quot;: &quot;https://example.com/storage/size_guide.jpg&quot;
    },
    &quot;variants&quot;: [...],
    &quot;labels&quot;: [...],
    &quot;bundles&quot;: [...],
    &quot;average_rating&quot;: 4.3,
    &quot;actions&quot;: {
      &quot;add_to_cart&quot;: { &quot;method&quot;: &quot;POST&quot;, &quot;url&quot;: &quot;/api/cart&quot; },
      ...
    }
  }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-products--slug-" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-products--slug-"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-products--slug-"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-products--slug-" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-products--slug-">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-products--slug-" data-method="GET"
      data-path="api/products/{slug}"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-products--slug-', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-products--slug-"
                    onclick="tryItOut('GETapi-products--slug-');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-products--slug-"
                    onclick="cancelTryOut('GETapi-products--slug-');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-products--slug-"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/products/{slug}</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-products--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-products--slug-"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                        <h4 class="fancy-heading-panel"><b>URL Parameters</b></h4>
                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>slug</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="slug"                data-endpoint="GETapi-products--slug-"
               value=""smartphone-2025""
               data-component="url">
    <br>
<p>The unique slug of the product. Example: <code>"smartphone-2025"</code></p>
            </div>
                    </form>

                    <h2 id="products-GETapi-all-products">Get all active products with filtering and pagination

This endpoint returns a paginated list of all published products with their details, variants, and available filters.
Products can be filtered by various criteria like color, size, category, and rating.</h2>

<p>
</p>



<span id="example-requests-GETapi-all-products">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/all-products?color_id=1&amp;size_id=2&amp;category_id=3&amp;min_rating=4.5&amp;sort_by=latest" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/all-products"
);

const params = {
    "color_id": "1",
    "size_id": "2",
    "category_id": "3",
    "min_rating": "4.5",
    "sort_by": "latest",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-all-products">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;products&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;category_id&quot;: 3,
            &quot;category_name&quot;: &quot;T-Shirts&quot;,
            &quot;name&quot;: &quot;Premium Cotton T-Shirt&quot;,
            &quot;price&quot;: 29.989999999999998436805981327779591083526611328125,
            &quot;after_discount_price&quot;: 24.989999999999998436805981327779591083526611328125,
            &quot;description&quot;: &quot;High quality cotton t-shirt...&quot;,
            &quot;slug&quot;: &quot;premium-cotton-t-shirt&quot;,
            &quot;views&quot;: 150,
            &quot;sales&quot;: 30,
            &quot;fake_average_rating&quot;: 4.5,
            &quot;label_id&quot;: null,
            &quot;summary&quot;: &quot;Comfortable and stylish...&quot;,
            &quot;quantity&quot;: 100,
            &quot;created_at&quot;: &quot;2023-01-15T10:00:00.000000Z&quot;,
            &quot;updated_at&quot;: &quot;2023-01-20T12:30:00.000000Z&quot;,
            &quot;media&quot;: {
                &quot;feature_product_image&quot;: &quot;https://example.com/storage/products/image1.jpg&quot;,
                &quot;second_feature_product_image&quot;: &quot;https://example.com/storage/products/image2.jpg&quot;
            },
            &quot;variants&quot;: [
                {
                    &quot;id&quot;: 5,
                    &quot;color_id&quot;: 1,
                    &quot;color_name&quot;: &quot;Red&quot;,
                    &quot;image_url&quot;: &quot;https://example.com/storage/variants/red.jpg&quot;,
                    &quot;sizes&quot;: [
                        {
                            &quot;id&quot;: 10,
                            &quot;size_id&quot;: 2,
                            &quot;size_name&quot;: &quot;M&quot;,
                            &quot;quantity&quot;: 25
                        },
                        {
                            &quot;id&quot;: 11,
                            &quot;size_id&quot;: 3,
                            &quot;size_name&quot;: &quot;L&quot;,
                            &quot;quantity&quot;: 30
                        }
                    ]
                }
            ],
            &quot;real_average_rating&quot;: 4.29999999999999982236431605997495353221893310546875,
            &quot;actions&quot;: {
                &quot;view&quot;: &quot;https://example.com/api/products/1&quot;,
                &quot;edit&quot;: &quot;https://example.com/api/products/1/edit&quot;,
                &quot;delete&quot;: &quot;https://example.com/api/products/1&quot;
            }
        }
    ],
    &quot;pagination&quot;: {
        &quot;current_page&quot;: 1,
        &quot;last_page&quot;: 5,
        &quot;per_page&quot;: 15,
        &quot;total&quot;: 75
    },
    &quot;filters&quot;: {
        &quot;colors&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Red&quot;
            },
            {
                &quot;id&quot;: 2,
                &quot;name&quot;: &quot;Blue&quot;
            }
        ],
        &quot;sizes&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;S&quot;
            },
            {
                &quot;id&quot;: 2,
                &quot;name&quot;: &quot;M&quot;
            }
        ],
        &quot;categories&quot;: [
            {
                &quot;id&quot;: 1,
                &quot;name&quot;: &quot;Men&quot;,
                &quot;parent_id&quot;: null
            },
            {
                &quot;id&quot;: 3,
                &quot;name&quot;: &quot;T-Shirts&quot;,
                &quot;parent_id&quot;: 1
            }
        ]
    }
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-all-products" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-all-products"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-all-products"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-all-products" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-all-products">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-all-products" data-method="GET"
      data-path="api/all-products"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-all-products', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-all-products"
                    onclick="tryItOut('GETapi-all-products');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-all-products"
                    onclick="cancelTryOut('GETapi-all-products');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-all-products"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/all-products</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-all-products"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-all-products"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>color_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="color_id"                data-endpoint="GETapi-all-products"
               value="1"
               data-component="query">
    <br>
<p>optional Filter products by color ID. Example: <code>1</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>size_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="size_id"                data-endpoint="GETapi-all-products"
               value="2"
               data-component="query">
    <br>
<p>optional Filter products by size ID. Example: <code>2</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>category_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="category_id"                data-endpoint="GETapi-all-products"
               value="3"
               data-component="query">
    <br>
<p>optional Filter products by category ID (includes subcategories). Example: <code>3</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>min_rating</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="number" style="display: none"
               step="any"               name="min_rating"                data-endpoint="GETapi-all-products"
               value="4.5"
               data-component="query">
    <br>
<p>optional Filter products by minimum fake rating (1-5). Example: <code>4.5</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>sort_by</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="sort_by"                data-endpoint="GETapi-all-products"
               value="latest"
               data-component="query">
    <br>
<p>optional Sort products by creation date. Possible values: 'latest', 'oldest'. Default: 'latest'. Example: <code>latest</code></p>
            </div>
                </form>

    <h3>Response</h3>
    <h4 class="fancy-heading-panel"><b>Response Fields</b></h4>
    <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>products</code></b>&nbsp;&nbsp;
<small>string[][]</small>&nbsp;
 &nbsp;
<br>
<p>The list of active products with their details.</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>id</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The product ID.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The product name (translated to current locale).</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>price</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The original product price.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>after_discount_price</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The discounted price if available.</p>
                    </div>
                                                                <div style=" margin-left: 14px; clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>media</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>

            </summary>
                                                <div style="margin-left: 28px; clear: unset;">
                        <b style="line-height: 2;"><code>feature_product_image</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The URL of the main product image.</p>
                    </div>
                                    </details>
        </div>
                                                                    <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>variants</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>of product variants with color and size information.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>real_average_rating</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
<br>
<p>The actual average rating from customer reviews.</p>
                    </div>
                                    </details>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>pagination</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Pagination</p>
        </div>
                <div style=" padding-left: 28px;  clear: unset;">
        <details>
            <summary style="padding-bottom: 10px;">
                <b style="line-height: 2;"><code>filters</code></b>&nbsp;&nbsp;
<small>object</small>&nbsp;
 &nbsp;
<br>
<p>Available filters for products (colors, sizes, categories).</p>
            </summary>
                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>colors</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>of available colors for filtering.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>sizes</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>of available sizes for filtering.</p>
                    </div>
                                                                <div style="margin-left: 14px; clear: unset;">
                        <b style="line-height: 2;"><code>categories</code></b>&nbsp;&nbsp;
<small>string[]</small>&nbsp;
 &nbsp;
<br>
<p>of available categories for filtering.</p>
                    </div>
                                    </details>
        </div>
                    <h1 id="wheel-of-fortune">Wheel of Fortune</h1>

    

                                <h2 id="wheel-of-fortune-GETapi-wheel">Get the active wheel and its prizes</h2>

<p>
</p>

<p>Retrieves the currently active wheel of fortune, including its configuration and available prizes.
The wheel must be active (is_active=true), within its valid date range, and have at least one available prize.
All text fields are returned in the requested language (English or Arabic).</p>

<span id="example-requests-GETapi-wheel">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "https://backend.sopdakt.com/api/wheel?lang=ar" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/wheel"
);

const params = {
    "lang": "ar",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-wheel">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;id&quot;: integer,
        &quot;name&quot;: string,
        &quot;is_active&quot;: boolean,
        &quot;start_date&quot;: string|null,
        &quot;end_date&quot;: string|null,
        &quot;spins_per_user&quot;: integer,
        &quot;spins_duration&quot;: integer,
        &quot;display_rules&quot;: string,
        &quot;specific_pages&quot;: array|null,
        &quot;prizes&quot;: array [
            {
                &quot;id&quot;: integer,
                &quot;name&quot;: string,
                &quot;type&quot;: string,
                &quot;value&quot;: integer|null,
                &quot;coupon_id&quot;: integer|null,
                &quot;discount_id&quot;: integer|null,
                &quot;probability&quot;: integer,
                &quot;is_available&quot;: boolean
            }
        ],
        &quot;language&quot;: string
    },
    &quot;message&quot;: string
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: string
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-wheel" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-wheel"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-wheel"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-wheel" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-wheel">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-wheel" data-method="GET"
      data-path="api/wheel"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-wheel', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-wheel"
                    onclick="tryItOut('GETapi-wheel');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-wheel"
                    onclick="cancelTryOut('GETapi-wheel');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-wheel"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/wheel</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-wheel"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-wheel"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>lang</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
                <input type="text" style="display: none"
                              name="lang"                data-endpoint="GETapi-wheel"
               value="ar"
               data-component="query">
    <br>
<p>The language for translations (en or ar). Defaults to application's default locale. Example: <code>ar</code></p>
            </div>
                </form>

                    <h2 id="wheel-of-fortune-POSTapi-wheel-spin">Spin the wheel of fortune</h2>

<p>
<small class="badge badge-darkred">requires authentication</small>
</p>

<p>Processes a wheel spin attempt for either authenticated users or guests. Validates:</p>
<ul>
<li>Wheel is active and available</li>
<li>User hasn't exceeded spin limits (spins_per_user)</li>
<li>Cooldown period has expired (spins_duration)</li>
</ul>
<p>Every spin guarantees a prize (is_winner = true). If the spin limit is reached, returns the latest spin record.
If in cooldown, informs the user to wait with the next allowed spin time.</p>

<span id="example-requests-POSTapi-wheel-spin">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request POST \
    "https://backend.sopdakt.com/api/wheel/spin" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json" \
    --data "{
    \"wheel_id\": 1
}"
</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "https://backend.sopdakt.com/api/wheel/spin"
);

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};

let body = {
    "wheel_id": 1
};

fetch(url, {
    method: "POST",
    headers,
    body: JSON.stringify(body),
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-POSTapi-wheel-spin">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;spin_id&quot;: integer,
        &quot;wheel_id&quot;: integer,
        &quot;is_winner&quot;: true,
        &quot;prize&quot;: {
            &quot;id&quot;: integer,
            &quot;name&quot;: string,
            &quot;type&quot;: string,
            &quot;value&quot;: integer|null,
            &quot;coupon_id&quot;: integer|null,
            &quot;discount_id&quot;: integer|null,
            &quot;probability&quot;: integer,
            &quot;is_available&quot;: boolean
        },
        &quot;language&quot;: string
    },
    &quot;message&quot;: string
}</code>
 </pre>
            <blockquote>
            <p>Example response (403):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: string,
    &quot;remaining_spins&quot;: integer,
    &quot;next_spin_at&quot;: string|null,
    &quot;latest_spin&quot;: {
        &quot;spin_id&quot;: integer,
        &quot;wheel_id&quot;: integer,
        &quot;is_winner&quot;: true,
        &quot;prize&quot;: {
            &quot;id&quot;: integer,
            &quot;name&quot;: string,
            &quot;type&quot;: string,
            &quot;value&quot;: integer|null,
            &quot;coupon_id&quot;: integer|null,
            &quot;discount_id&quot;: integer|null,
            &quot;probability&quot;: integer,
            &quot;is_available&quot;: boolean
        }
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (404):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: string
}</code>
 </pre>
            <blockquote>
            <p>Example response (422):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: string,
    &quot;errors&quot;: {
        &quot;wheel_id&quot;: array
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;error&quot;: string
}</code>
 </pre>
    </span>
<span id="execution-results-POSTapi-wheel-spin" hidden>
    <blockquote>Received response<span
                id="execution-response-status-POSTapi-wheel-spin"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-POSTapi-wheel-spin"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-POSTapi-wheel-spin" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-POSTapi-wheel-spin">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-POSTapi-wheel-spin" data-method="POST"
      data-path="api/wheel/spin"
      data-authed="1"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('POSTapi-wheel-spin', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-POSTapi-wheel-spin"
                    onclick="tryItOut('POSTapi-wheel-spin');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-POSTapi-wheel-spin"
                    onclick="cancelTryOut('POSTapi-wheel-spin');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-POSTapi-wheel-spin"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-black">POST</small>
            <b><code>api/wheel/spin</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="POSTapi-wheel-spin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="POSTapi-wheel-spin"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <h4 class="fancy-heading-panel"><b>Body Parameters</b></h4>
        <div style=" padding-left: 28px;  clear: unset;">
            <b style="line-height: 2;"><code>wheel_id</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="wheel_id"                data-endpoint="POSTapi-wheel-spin"
               value="1"
               data-component="body">
    <br>
<p>The ID of an active wheel. Example: <code>1</code></p>
        </div>
        </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
