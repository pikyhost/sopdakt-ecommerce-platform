<div class="product-criteria page-wrapper pt-5 pb-3">
    <main class="main">
        <div class="container-fluid">
            <div class="container-fluid">
                <div class="row justify-content-center gap-3">
                    @if($media)
                        <div style="overflow: hidden;" class="overflow-hidden col-lg-6 col-md-6 product-single-gallery  " data-aos="fade-right" data-aos-duration="1500">
                            <div class="product-slider-container">
                                <div class="label-group">
                                    {{--                    <div class="product-label label-hot">HOT</div>--}}

                                    {{--                    <div class="product-label label-sale">--}}
                                    {{--                        -16%--}}
                                    {{--                    </div>--}}
                                </div>

                                <div class="product-single-carousel owl-carousel owl-theme show-nav-hover">
                                    @foreach($media as $mediaItem)
                                        @if($mediaItem->type == 'image')
                                            <div class="product-item text-center" >
                                                <img class="product-single-image rounded-1"
                                                    src="{{asset($mediaItem->url)}}"
                                                    width="468"
                                                    height="468" alt="product"/>
                                            </div>
                                        @elseif($mediaItem->type == 'video')
                                            <div class="product-item">
                                                <video class="product-single-image" controls width="468"
                                                    height="468">
                                                    <source src="{{asset($mediaItem->url)}}" type="video/mp4">
                                                </video>
                                            </div>
                                        @endif
                                    @endforeach

                                </div>
                                <!-- End .product-single-carousel -->
                                <span class="prod-full-screen">
                                <i class="icon-plus"></i>
                            </span>
                            </div>

                            <div class="prod-thumbnail owl-dots">
                                @foreach($media as $mediaItem)
                                    @if($mediaItem->type == 'image')
                                        <div class="owl-dot">
                                            <img src="{{asset($mediaItem->url)}}" width="110" height="110" class="rounded-1"
                                                alt="product-thumbnail"/>
                                        </div>
                                    @elseif($mediaItem->type == 'video')
                                        <div class="owl-dot">
                                            <video controls width="110" height="110">
                                                <source src="{{asset($mediaItem->url)}}" type="video/mp4">
                                            </video>
                                        </div>
                                    @endif
                                @endforeach

                            </div>
                        </div><!-- End .product-single-gallery -->
                    @endif
                    <div class="col-md-6 pb-1  " data-aos="fade-left" data-aos-duration="1500">
                        <div class="single-product-custom-block">
                            @if($landingPage->features)
                                <div class="porto-block">
                                    @foreach($landingPage->features as $feature)
                                        <h5 class="porto-heading d-inline-block product-details-feature"
                                            style=" direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
                                        >{{$feature->title}}</h5>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="product-single-details mb-1   w-100">
                            <h1 class="product-title fs-4">{{$landingPage->title}}</h1>
                            @if($landingPage->rating)
                                <div class="ratings-container">
                                    <div class="product-ratings">
                            <span class="ratings" style="
                            width: {{$landingPage->rating*10*2}}%;
                            "></span><!-- End .ratings -->
                                        <span class="tooltiptext tooltip-top"></span>
                                    </div><!-- End .product-ratings -->

                                    {{--                            <a href="#" class="rating-link">( 6 Reviews )</a>--}}
                                </div><!-- End .ratings-container -->
                            @endif
                            <hr class="short-divider">

                            <div class="price-box">
                                @if($landingPage->after_discount_price)
                                    <span class="old-price">{{$landingPage->price}}</span>
                                @endif
                                <span class="new-price">{{$landingPage->after_discount_price}}</span>
                            </div><!-- End .price-box -->

                            <div class="product-desc">
                                <p>
                                    {{$landingPage->description}}
                                </p>
                            </div><!-- End .product-desc -->

                            <ul class="single-info-list">

                                <li class="fs-5">
                                    <div class="product-sku">
                                        {{__('SKU')}}: <strong class="sku_text">{{$landingPage->sku}}</strong>
                                    </div>
                                </li>

                            </ul>

                            <div id="accordion" class="col-12 p-0 "
                                style="direction:  {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                                @foreach($landingPage->bundles as $bundle)
                                    <div class="col-12 my-2">
                                        <h5>
                                            <label
                                                for="bundle-{{$bundle->id}}"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#collapse-{{$bundle->id}}"
                                                aria-expanded="true"
                                                aria-controls="collapse-{{$bundle->id}}"
                                                class="btn rounded-1 btn-toggle btn-link border w-100 m-0"
                                            >
                                                {{$bundle->name}}
                                            </label>
                                        </h5>

                                        <div id="collapse-{{$bundle->id}}"
                                            class="collapse  p-0"
                                            data-parent="#accordion">
                                            <div class="feature-box">
                                                <div class="feature-box-content ">
                                                    <div class="row">
                                                        <input class="d-none" id="bundle-{{$bundle->id}}"
                                                            type="radio" name="landing_page_bundle_id"
                                                            value="{{$bundle->id}}"/>
                                                    </div>
                                                    @for($i = 0; $i < $bundle->quantity; $i++)
                                                        <div class=" card mb-1 px-3 py-1 rounded-0 shadow">
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <h5>{{__('Piece number')}} {{$i + 1}}</h5>
                                                                </div>
                                                                <div class="row my-2">
                                                                    <div class="col-6"
                                                                        style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                                                        <x-select
                                                                            class="rounded-0 form-control"
                                                                            id="color"
                                                                            name="varieties[{{$bundle->id}}][{{$i}}][color_id]"
                                                                            label-name="Color"
                                                                            required>
                                                                            @foreach( $landingPage->colors->unique() as $color)
                                                                                <option
                                                                                    value="{{$color->id}}">{{__($color->name)}}</option>
                                                                            @endforeach
                                                                        </x-select>
                                                                    </div>
                                                                    <div class="col-6"
                                                                        style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                                                        <x-select
                                                                            class="rounded-0 form-control"
                                                                            id="size"
                                                                            name="varieties[{{$bundle->id}}][{{$i}}][size_id]"
                                                                            label-name="Size"
                                                                            required>
                                                                            @foreach( $landingPage->sizes->unique() as $size)
                                                                                <option
                                                                                    value="{{$size->id}}">{{$size->name}}</option>
                                                                            @endforeach
                                                                        </x-select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="product-action">
                            {{--                            <div class="product-single-qty " style='border-radius: 4px;overflow: hidden;'>--}}
                            {{--                                <input class="horizontal-quantity form-control" type="text">--}}
                            {{--                            </div><!-- End .product-single-qty -->--}}
                            <div class="product-single-qty">
                                <input class="horizontal-quantity form-control" type="number" min="1">
                            </div><!-- End .product-single-qty -->
                            <button onclick="handleCheckout()" style="border-radius: 5px !important;"
                                    class="mybtn3 mybtn-bg btn btn-dark rounded-0 px-3 py-2 my-2"
                                    title="Add to Cart">
                                <span class="buy_text ">{{__('Buy Now')}}</span>
                            </button>


                            {{--                            <a href="cart.html" class="btn btn-gray view-cart d-none">View cart</a>--}}
                        </div><!-- End .product-action -->

                        <hr class="divider mb-0 mt-0">

                        {{--                        <div class="product-single-share mb-3">--}}
                        {{--                            <label class="sr-only">Share:</label>--}}

                        {{--                            <div class="social-icons mr-2">--}}
                        {{--                                <a href="#" class="social-icon social-facebook icon-facebook" target="_blank"--}}
                        {{--                                   title="Facebook"></a>--}}
                        {{--                                <a href="#" class="social-icon social-twitter icon-twitter" target="_blank"--}}
                        {{--                                   title="Twitter"></a>--}}
                        {{--                                <a href="#" class="social-icon social-linkedin fab fa-linkedin-in" target="_blank"--}}
                        {{--                                   title="Linkedin"></a>--}}
                        {{--                                <a href="#" class="social-icon social-gplus fab fa-google-plus-g" target="_blank"--}}
                        {{--                                   title="Google +"></a>--}}
                        {{--                                <a href="#" class="social-icon social-mail icon-mail-alt" target="_blank"--}}
                        {{--                                   title="Mail"></a>--}}
                        {{--                            </div><!-- End .social-icons -->--}}

                        {{--                            <a href="wishlist.html" class="btn-icon-wish add-wishlist" title="Add to Wishlist"><i--}}
                        {{--                                    class="icon-wishlist-2"></i><span>Add to--}}
                        {{--										Wishlist</span></a>--}}
                        {{--                        </div><!-- End .product single-share -->--}}
                    </div><!-- End .product-single-details -->
                </div>
            </div><!-- End .row -->
        </div><!-- End .product-single-container -->
        {{--    </div>--}}

    </main>
</div>
