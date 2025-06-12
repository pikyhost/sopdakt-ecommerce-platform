$(function ($) {
    "use strict";

    var $window = $(window),
        $body = $("body");

    document.onreadystatechange = function () {
        var state = document.readyState;
        if (state == "interactive") {
            document.getElementById("preloader").style.display = "block";
        } else if (state == "complete") {
            setTimeout(function () {
                document.getElementById("preloader").style.display = "none";
                AOS.init({
                    easing: "ease-out-back",
                });
                AOS.refresh();
            }, 2000);
        }
    };

    $(".navigation .navbar-nav a").on("click", function (event) {
        var $anchor = $(this);
        $("html, body")
            .stop()
            .animate(
                {
                    scrollTop: $($anchor.attr("href")).offset().top - 80,
                },
                1000
            );
        event.preventDefault();
    });

    $(".nav-link").on("click", function () {
        $(".navbar-collapse").collapse("hide");
    });

    $("[data-countdown]").each(function () {
        var $this = $(this),
            finalDate = $(this).data("countdown");
        $this.countdown(finalDate, function (event) {
            $this.html(
                event.strftime(
                    "<span>%D <small>Days</small></span> <span>%H <small>Hrs</small></span>  <span>%M <small>Min</small></span> <span>%S <small>Sec</small></span>"
                )
            );
        });
    });

    var html_body = $("html, body");
    var amountScrolled = 300;
    var bootomclass = $(".bottomtotop");
    bootomclass.hide();
    $window.on("scroll", function () {
        if ($window.scrollTop() > amountScrolled) {
            bootomclass.fadeIn("slow");
        } else {
            bootomclass.fadeOut("slow");
        }

        if ($(".count-down-container").length > 0) {
            if ($(".count-down-container").offset().top > 500) {
                $(".count-down-container").addClass("d-flex");
                $(".count-down-container").removeClass("d-none");
            } else {
                $(".count-down-container").removeClass("d-flex");
                $(".count-down-container").addClass("d-none");
            }
        }
    });

    bootomclass.on("click", function () {
        html_body.animate(
            {
                scrollTop: 0,
            },
            600
        );
        return false;
    });

    $body.scrollspy({
        target: "#mainmenu",
        offset: 100,
    });

    $(".video-play-btn").magnificPopup({
        type: "video",
        iframe: {
            patterns: {
                youtube: {
                    index: "youtube.com/", // URL contains "youtube.com"
                    id: "v=",
                    src: "https://www.youtube.com/embed/%id%?autoplay=1",
                },
                vimeo: {
                    index: "vimeo.com/", // URL contains "vimeo.com"
                    id: "/",
                    src: "https://player.vimeo.com/video/%id%?autoplay=1",
                },
            },
        },
    });
    $(".img-popup").magnificPopup({
        type: "image",
    });

    var $deal_slider = $(".deal-slider");
    var dealChildCount = $deal_slider.children().length;
    if (dealChildCount > 1) {
        $deal_slider.owlCarousel({
            loop: true,
            navText: [
                '<i class="fa fa-angle-left"></i>',
                '<i class="fa fa-angle-right"></i>',
            ],
            nav: true,
            dots: false,
            animateOut: "fadeOut",
            animateIn: "fadeIn",
            autoplayTimeout: 6000,
            smartSpeed: 1200,
            responsive: {
                0: {
                    items: 1,
                },
                576: {
                    items: 1,
                },
                950: {
                    items: 1,
                },
                960: {
                    items: 1,
                },
                1200: {
                    items: 1,
                },
            },
        });
    }

    $(".testimonial-slider").owlCarousel({
        navigation: true, // Show next and prev buttons
        loop: true,
        slideSpeed: 300,
        scrollPerPage: false, // لتحريك عنصر واحد فقط عند كل ضغط
        nav: true, // تفعيل التنقل
        items: 1,
        itemsDesktop: [1200, 1],
        itemsTablet: [800, 1],
        itemsMobile: [700, 1],
        paginationSpeed: 400,
        navigationText: [
            '<i class="fa fa-angle-left"></i>',
            '<i class="fa fa-angle-right"></i>',
        ],
    });

    var $product_slider = $(".product-slider");
    $product_slider.owlCarousel({
        loop: true,
        nav: true,
        navText: [
            '<i class="fa fa-angle-left"></i>',
            '<i class="fa fa-angle-right"></i>',
        ],
        dots: false,
        margin: 30,
        autoplay: false,
        autoplayTimeout: 8000,
        smartSpeed: 1500,
        responsive: {
            0: {
                items: 1,
            },
            576: {
                items: 1,
            },
            768: {
                items: 2,
            },
            992: {
                items: 3,
            },
            1200: {
                items: 4,
            },
            1920: {
                items: 4,
            },
        },
    });
});
