<script src="{{asset('assets/js/jquery.js')}}"></script>
<script src="{{asset('assets/js/popper.min.js')}}"></script>
<script src="{{asset('assets/js/bootstrap.min.js')}}"></script>
<script src="{{asset('assets/js/waypoints.min.js')}}"></script>
<script src="{{asset('assets/js/owl.carousel.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.magnific-popup.js')}}"></script>
<script src="{{asset('assets/js/aos.js')}}"></script>
<script src="{{asset('assets/js/jquery.countdown.min.js')}}"></script>
<script src="{{asset('assets/js/jquery.easing.1.3.js')}}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC7eALQrRUekFNQX71IBNkxUXcz-ALS-MY&sensor=false"></script>
<script src="{{asset('assets/js/gmap.js')}}"></script>
<script src="{{asset('assets/js/contact.js')}}"></script>
<script src="{{asset('assets/js/porto_plugins.min.js')}}"></script>
<script src="{{asset('assets/js/porto_main.js')}}"></script>
<script src="{{asset('assets/js/main_landing.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@4.1.4/dist/js/splide.min.js"></script>
<script src="{{asset('plugins/sweetalerts2/sweetalerts2.min.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

<script>
    getRegions('governorate', 'region');
    getShippingCost();

    function getRegions($elementId = 'address_governorate', $regionsElementId = 'address_region') {
        var governorate_id = $('#' + $elementId).val();
        console.log(governorate_id, $elementId)
        $.ajax({
            url: "{{route('api.regions.index')}}",
            type: 'GET',
            data: {
                governorate_id: governorate_id
            },
            success: function (data) {
                let addressRegions = $('#' + $regionsElementId);
                addressRegions.html('');
                if (data.length === 0) {
                    addressRegions.append('<option value="">No regions found</option>');
                } else {
                    data.forEach(function (region) {
                        console.log(region);
                        addressRegions.append('<option value="' + region.id + '">' + region.name + '</option>');
                    });
                    getShippingCost();
                }
            }
        });
    }

    let subtotal = {{$totalPrice}};
    let shippingCost = 0;
    let total = {{$totalPrice}};

    function getShippingCost() {
        let regionId = $('#region').val();
        let shippingTypeId = $('#shipping_type_id').val();

        if (regionId) {

            fetch('{{ route('api.shipping.calculate') }}', {
                method: 'POST',
                body: JSON.stringify({
                    landing_page_id: {{$landingPage->id}},
                    region_id: regionId,
                    shipping_type_id: shippingTypeId
                }),
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            })
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        if (data.shipping_cost) {
                            let shippingCost = data.shipping_cost;
                            let total = subtotal + shippingCost;

                            document.getElementById('shipping_cost').innerText =
                                shippingCost + ' {{$landingPageSettings?->currency_code}}';
                            document.getElementById('total').innerText =
                                total + ' {{$landingPageSettings?->currency_code}}';
                        } else {
                            document.getElementById('shipping_cost').innerText =
                                '0 {{$landingPageSettings?->currency_code}}';
                        }
                    } else {
                        Swal.fire({
                            title: '{{__('Error')}}',
                            text: 'An error occurred, please try again later',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                })
                .catch((error) => {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: 'An error occurred, please try again later',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                });
        } else {
            document.getElementById('shipping_cost').innerText = '';
        }
    }

    let product_size = document.getElementById('Size');
    let product_color = document.getElementById('Color');
    let quantityInput = document.getElementById('quantity');

    product_size.addEventListener('change', function () {
        let selectedSize = product_size.value;
        let selectedColor = product_color.value;
        let quantity = quantityInput.value || 1;
        setPriceByColorAndSizeId(selectedColor, selectedSize, quantity);
    });

    product_color.addEventListener('change', function () {
        let selectedSize = product_size.value;
        let selectedColor = product_color.value;
        let quantity = quantityInput.value || 1;
        setPriceByColorAndSizeId(selectedColor, selectedSize, quantity);
    });
    quantityInput.addEventListener('input', function () {
        let selectedSize = product_size.value;
        let selectedColor = product_color.value;
        let quantity = quantityInput.value || 1;
        setPriceByColorAndSizeId(selectedColor, selectedSize, quantity);
    });

    function setPriceByColorAndSizeId(selectedColor, selectedSize, quantity) {
        fetch('{{ route('dashboard.landing-pages.get-combination-price',$landingPage->id) }}', {
            method: 'POST',
            body: JSON.stringify({
                size_id: selectedSize,
                color_id: selectedColor
            }),
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    if (data.price) {
                        let shippingCostElement = document.getElementById('shipping_cost');
                        let shippingCostText = shippingCostElement.innerText || '0';

                        // Parse the numeric value of shipping cost
                        let shippingCost = parseFloat(shippingCostText.replace(/[^\d.-]/g, '')) || 0;

                        // Calculate subtotal and total
                        subtotal = data.price * quantity;
                        total = subtotal + shippingCost;

                        document.getElementById('price').innerText = subtotal + ' {{$landingPageSettings?->currency_code}}';
                        document.getElementById('total').innerText = total + ' {{$landingPageSettings?->currency_code}}';
                    }
                } else {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: 'An error occurred, please try again later',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch((error) => {
                Swal.fire({
                    title: '{{__('Error')}}',
                    text: 'An error occurred, please try again later',
                    icon: 'error',
                    confirmButtonText: 'Ok'
                });

            });
    }
</script>

<script>
    // Initialize Swiper for each section
    const leftSwiper = new Swiper('.left-swiper', {
        direction: 'vertical',
        loop: true,
        autoplay: {
            delay: 4000,
            disableOnInteraction: false,
        },
    });

    const centerSwiper = new Swiper('.center-swiper', {
        direction: 'vertical',
        loop: true,
        autoplay: {
            delay: 6000,
            disableOnInteraction: false,
        },
    });

    const rightSwiper = new Swiper('.right-swiper', {
        direction: 'vertical',
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });
</script>

<script>
    let mainSliders = document.querySelectorAll('.main-slider');
    let thumbnailSliders = document.querySelectorAll('.thumbnail-slider');

    mainSliders.forEach((mainSlider, index) => {
        let main = new Splide(mainSlider, {
            type: 'fade',
            heightRatio: 0.5,
            pagination: false,
            arrows: false,
            cover: true,
            speed: 2400,  // Default is 400
            easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
        });

        let thumbnails = new Splide(thumbnailSliders[index], {
            rewind: true,
            fixedWidth: 104,
            fixedHeight: 58,
            isNavigation: true,
            gap: 10,
            focus: 'center',
            pagination: false,
            cover: true,
            speed: 2000,
            easing: 'cubic-bezier(0.4, 0, 0.2, 1)',
            dragMinThreshold: {
                mouse: 4,
                touch: 10,
            },
            breakpoints: {
                640: {
                    fixedWidth: 66,
                    fixedHeight: 38,
                },
            },
        });

        main.sync(thumbnails);
        main.mount();
        thumbnails.mount();
    });
    let horizontal_quantity_input = document.querySelector('.horizontal-quantity');

    function handleCheckout() {
        // Check if a bundle is selected
        let landing_page_bundle_id = $('input[name="landing_page_bundle_id"]:checked').val();

        // Check if the bundle was expanded and then closed
        let isBundleExpanded = $('.collapse.show').length === 0; // Check if no bundles are open

        if (landing_page_bundle_id) {
            if (isBundleExpanded) {
                // If a bundle is selected and no bundles are open, show normal checkout
                updateURLWithParameter('checkout', 'true');
                showCheckout();
            } else {
                // If a bundle is selected and left open, validate bundle data
                updateURLWithParameter('bundleId', landing_page_bundle_id);
                showCheckoutPage();
            }
        } else {
            // If no bundle is selected, proceed with normal checkout
            updateURLWithParameter('checkout', 'true');
            showCheckout();
        }
    }

    // function handleCheckout() {
    //     // Check if a bundle is selected
    //     let landing_page_bundle_id = $('input[name="landing_page_bundle_id"]:checked').val();
    //     if (landing_page_bundle_id) {
    //         // Add a parameter to the URL
    //         updateURLWithParameter('bundleId', landing_page_bundle_id);
    //         showCheckoutPage();
    //     } else {
    //         // Add a parameter to the URL
    //         updateURLWithParameter('checkout', 'true');
    //         showCheckout();
    //     }
    // }


    function updateURLWithParameter(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value); // Add or update parameter
        window.history.pushState({}, '', url); // Update the URL without reloading
    }

    function removeURLParameter(key) {
        const url = new URL(window.location.href);
        url.searchParams.delete(key); // Remove the parameter
        window.history.pushState({}, '', url); // Update the URL without reloading
    }

    function showCheckout() {
        try {
            let quantity = horizontal_quantity_input.value;
            let modalQuantityInput = document.getElementById('quantity');
            modalQuantityInput.value = quantity;

            let activeSizeItem = document.querySelector('.config-size-list.sizes .active');
            let activeSizeId = activeSizeItem.getAttribute('data-value');

            let modalSizeSelect = document.getElementById('size');
            modalSizeSelect.value = activeSizeId;

            let activeColorItem = document.querySelector('.config-size-list.colors .active');
            let activeColorId = activeColorItem.getAttribute('data-value');

            let modalColorSelect = document.getElementById('color');
            modalColorSelect.value = activeColorId;

            setPriceByColorAndSizeId(activeColorId, activeSizeId, quantity);

            $('#purchaseModal').modal('show');
        } catch (e) {
            $('#purchaseModal').modal('show');
        }

        // Remove parameter when modal is closed
        $('#purchaseModal').on('hidden.bs.modal', () => {
            removeURLParameter('checkout');
        });
    }

    function showCheckoutPage() {
        let landing_page_bundle_id = $('input[name="landing_page_bundle_id"]:checked').val();
        let quantityElemnt = document.querySelector('.horizontal-quantity.form-control');
        let quantityValue = quantityElemnt.value;

        if (landing_page_bundle_id) {
            // If a bundle is selected, validate its varieties
            const varietiesData = getVarieties();
            const bundleVarieties = varietiesData[landing_page_bundle_id];
            if (bundleVarieties) {
                let errorMessageHtml = `<ul>`;
                let counter = 1;
                bundleVarieties.forEach(variety => {
                    let sizeId = variety.size_id;
                    let colorId = variety.color_id;

                    if (!sizeId || !colorId) {
                        errorMessageHtml += `<li class='text-danger'>{{__('Please select size and color for item')}} ${counter}</li>`;
                    }
                    counter++;
                });
                errorMessageHtml += `</ul>`;

                if (errorMessageHtml !== `<ul></ul>`) {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        html: errorMessageHtml,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                // Submit the bundle purchase data
                processCheckout({
                    landing_page_bundle_id: landing_page_bundle_id,
                    quantity: quantityValue,
                    varieties: bundleVarieties
                });
            }
        } else {
            // If no bundle is selected, proceed with the normal product checkout
            processCheckout({
                quantity: quantityValue
            });
        }
    }

    function processCheckout(data) {
        fetch('{{ route('landing-pages.purchase-form.save-bundle-data', $landingPage->id) }}', {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    if (data.success) {
                        window.location.href = '{{ route('landing-pages.purchase-form.show', $landingPage->slug) }}';
                    } else {
                        Swal.fire({
                            title: '{{__('Error')}}',
                            text: 'An error',
                            icon: 'error',
                            confirmButtonText: 'Ok'
                        });
                    }
                } else {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: 'An error occurred',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            })
            .catch((error) => {
                if (error.response) {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: error.response.data.message,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                } else if (error.request) {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: 'An error occurred, please try again later',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                } else if (error.errors) {
                    Swal.fire({
                        title: '{{__('Error')}}',
                        text: error.errors,
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
    }


    function getVarieties() {
        const varieties = {};

        // Select all color and size dropdowns
        const colorElements = document.querySelectorAll('select[name^="varieties"]');

        colorElements.forEach(element => {
            // Extract bundle_id and index from the name attribute
            const matches = element.name.match(/varieties\[(\d+)]\[(\d+)]\[(color_id|size_id)]/);
            if (matches) {
                const bundleId = matches[1];
                const index = matches[2];
                const key = matches[3]; // "color_id" or "size_id"

                // Initialize structure if not exists
                if (!varieties[bundleId]) {
                    varieties[bundleId] = [];
                }
                if (!varieties[bundleId][index]) {
                    varieties[bundleId][index] = {};
                }

                // Assign the value
                varieties[bundleId][index][key] = element.value;
            }
        });

        return varieties;
    }

</script>

<script>
    function enforceMaxLength(input) {
        if (input.value.length > 11) {
            input.value = input.value.slice(0, 11);
        }
    }

    document.getElementById('phone').addEventListener('input', function() {
        enforceMaxLength(this);
    });

    document.getElementById('another_phone').addEventListener('input', function() {
        enforceMaxLength(this);
    });
</script>

{!! displayAlert() !!}
