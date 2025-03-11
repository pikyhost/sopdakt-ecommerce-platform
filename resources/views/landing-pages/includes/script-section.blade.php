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
        $.ajax({
            url: "{{route('regions.index')}}",
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
            fetch('{{ route('shipping.calculate') }}', {
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

                        document.getElementById('shipping_cost').innerText = shippingCost + ' {{$landingPageSettings?->currency_code}}';
                        document.getElementById('total').innerText = total + ' {{$landingPageSettings?->currency_code}}';
                    } else {
                        document.getElementById('shipping_cost').innerText = '0 {{$landingPageSettings?->currency_code}}';
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
</script>

<script>
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

<script>
    let horizontal_quantity_input = document.querySelector('.horizontal-quantity');
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
        fetch('{{ route('landing-page.get-combination-price',$landingPage->id) }}', {
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
                    let shippingCost = parseFloat(shippingCostText.replace(/[^\d.-]/g, '')) || 0;

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

    function handleCheckout($dierctCheckout = false) {
        if($dierctCheckout) {
            updateURLWithParameter('checkout', 'true');
            showCheckout();
        } else {
            let bundle_landing_page_id = $('input[name="bundle_landing_page_id"]:checked').val();
            let isBundleExpanded = $('.collapse.show').length === 0;

            if (bundle_landing_page_id) {
                if (isBundleExpanded) {
                    updateURLWithParameter('checkout', 'true');
                    showCheckout();
                } else {
                    updateURLWithParameter('bundleId', bundle_landing_page_id);
                    showCheckoutPage();
                }
            }
        }
    }

    function updateURLWithParameter(key, value) {
        const url = new URL(window.location.href);
        url.searchParams.set(key, value);
        window.history.pushState({}, '', url);
    }

    function removeURLParameter(key) {
        const url = new URL(window.location.href);
        url.searchParams.delete(key);
        window.history.pushState({}, '', url);
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

        $('#purchaseModal').on('hidden.bs.modal', () => {
            removeURLParameter('checkout');
        });
    }

    function showCheckoutPage() {
        let bundle_landing_page_id = $('input[name="bundle_landing_page_id"]:checked').val();
        let quantityElemnt = document.querySelector('.horizontal-quantity.form-control');
        let quantityValue = quantityElemnt.value;

        if (bundle_landing_page_id) {
            const varietiesData = getVarieties();
            const bundleVarieties = varietiesData[bundle_landing_page_id];

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

                processCheckout({
                    bundle_landing_page_id: bundle_landing_page_id,
                    quantity: quantityValue,
                    varieties: bundleVarieties
                });
            }
        } else {
            processCheckout({quantity: quantityValue});
        }
    }

    function processCheckout(data) {
        fetch('{{ route('landing-page.purchase-form.save-bundle-data', $landingPage->id) }}', {
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
                    window.location.href = '{{ route('landing-page.purchase-form.show', $landingPage->slug) }}';
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
        const colorElements = document.querySelectorAll('select[name^="varieties"]');

        colorElements.forEach(element => {
            const matches = element.name.match(/varieties\[(\d+)]\[(\d+)]\[(color_id|size_id)]/);
            if (matches) {
                const bundleId = matches[1];
                const index = matches[2];
                const key = matches[3];

                if (!varieties[bundleId]) varieties[bundleId] = [];
                if (!varieties[bundleId][index]) varieties[bundleId][index] = {}
                varieties[bundleId][index][key] = element.value;
            }
        });

        return varieties;
    }
</script>

{!! displayAlert() !!}
