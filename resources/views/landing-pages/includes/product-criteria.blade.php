<div class="product-criteria page-wrapper pt-5 pb-3">
    <main class="main">
        <div class="container-fluid">
            <div class="container-fluid">
                <div class="row justify-content-center gap-3">
                    @if($media)
                        <div style="overflow: hidden;" class="overflow-hidden col-lg-6 col-md-6 product-single-gallery" data-aos="fade-right" data-aos-duration="1500">
                            <div class="product-slider-container">
                                <div class="product-single-carousel owl-carousel owl-theme show-nav-hover">
                                    @foreach($media as $mediaItem)
                                        @if($mediaItem->type == 'image')
                                            <div class="product-item text-center" >
                                                <img class="product-single-image rounded-1" src="{{asset('storage/'.$mediaItem->url)}}" width="468" height="468" alt="product"/>
                                            </div>
                                        @elseif($mediaItem->type == 'video')
                                            <div class="product-item">
                                                <video class="product-single-image" controls width="468" height="468">
                                                    <source src="{{asset('storage/'.$mediaItem->url)}}" type="video/mp4">
                                                </video>
                                            </div>
                                        @endif
                                    @endforeach

                                </div>

                                <span class="prod-full-screen">
                                    <i class="icon-plus"></i>
                                </span>
                            </div>

                            <div class="prod-thumbnail owl-dots">
                                @foreach($media as $mediaItem)
                                    @if($mediaItem->type == 'image')
                                        <div class="owl-dot">
                                            <img src="{{asset('storage/'.$mediaItem->url)}}" width="110" height="110" class="rounded-1" alt="product-thumbnail"/>
                                        </div>
                                    @elseif($mediaItem->type == 'video')
                                        <div class="owl-dot">
                                            <video controls width="110" height="110">
                                                <source src="{{asset('storage/'.$mediaItem->url)}}" type="video/mp4">
                                            </video>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="col-md-6 pb-1" data-aos="fade-left" data-aos-duration="1500">
                        <div class="single-product-custom-block">
                            @if($landingPage->features)
                                <div class="porto-block">
                                    @foreach($landingPage->features as $feature)
                                        <h5 class="porto-heading d-inline-block product-details-feature" style=" direction: {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">{{$feature->title}}</h5>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="product-single-details mb-1 w-100">
                            <h1 class="product-title fs-4">{{$landingPage->title}}</h1>

                            @if($landingPage->rating)
                                <div class="ratings-container">
                                    <div class="product-ratings">
                                        <span class="ratings" style="width: {{$landingPage->rating*10*2}}%;"></span>
                                        <span class="tooltiptext tooltip-top"></span>
                                    </div>
                                </div>
                            @endif

                            <hr class="short-divider">

                            <div class="price-box">
                                @if($landingPage->after_discount_price)
                                    <span class="old-price">{{$landingPage->price}}</span>
                                @endif
                                <span class="new-price">{{$landingPage->after_discount_price}}</span>
                            </div>

                            <div class="product-desc">
                                <p>{{$landingPage->description}}</p>
                            </div>

                            <ul class="single-info-list">
                                <li class="fs-5">
                                    <div class="product-sku">
                                        {{__('SKU')}}: <strong class="sku_text">{{$landingPage->sku}}</strong>
                                    </div>
                                </li>
                            </ul>

                            <div id="accordion" class="col-12 p-0" style="direction:  {{app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
                                @foreach($landingPage->bundles as $key => $bundle)
                                    <div class="col-12 my-2">
                                        <h5>
                                            <label for="bundle-{{$bundle->id}}" type="button" data-toggle="collapse" data-target="#collapse-{{$bundle->id}}" aria-expanded="true" aria-controls="collapse-{{$bundle->id}}" class="btn rounded-1 btn-toggle btn-link border w-100 m-0">
                                                {{$bundle->name}}
                                            </label>
                                        </h5>

                                        <div id="collapse-{{$bundle->id}}" class="collapse  p-0" data-parent="#accordion">
                                            <div class="feature-box">
                                                <div class="feature-box-content ">
                                                    <div class="row">
                                                        <input class="d-none" id="bundle-{{$bundle->id}}" type="radio" name="bundle_landing_page_id" value="{{$bundle->id}}"/>
                                                    </div>

                                                    <div class=" card mb-1 px-3 py-1 rounded-0 shadow">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <h5>{{__('Piece number')}} {{$key + 1}}</h5>
                                                            </div>

                                                            <div class="row my-2">
                                                                @foreach($bundle->products as $key => $product)
                                                                    <div class="col-6 my-2" style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                                                        <x-select class="rounded-0 form-control" id="color" name="varieties[{{$bundle->id}}][{{$key}}][color_id]" label-name="Color" required>
                                                                            @foreach ($product->colors->unique() as $color)
                                                                                <option value="{{$color->id}}">{{__($color->name)}}</option>
                                                                            @endforeach
                                                                        </x-select>
                                                                    </div>

                                                                    <div class="col-6 my-2" style="text-align: {{app()->getLocale() == 'ar' ? 'right' : 'left'}}">
                                                                        <x-select class="rounded-0 form-control" id="size" name="varieties[{{$bundle->id}}][{{$key}}][size_id]" label-name="Size" required>
                                                                            @foreach($product->sizes->unique() as $size)
                                                                                <option value="{{$size->id}}">{{$size->name}}</option>
                                                                            @endforeach
                                                                        </x-select>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <div class="row mt-2 mb-2">
                                                                <div class="col-12">
                                                                    <button onclick="handleCheckout(false)" style="border-radius: 5px !important;" class="mybtn3 mybtn-bg btn btn-dark rounded-0 px-3 py-2 my-2 w-100" title="{{__('Buy Now')}}">
                                                                        <span class="buy_text">{{__('Buy Now')}}</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="product-action">
                            <div class="product-single-qty">
                                <input class="horizontal-quantity form-control" type="number" min="1">
                            </div>

                            <button onclick="handleCheckout(true)" style="border-radius: 5px !important;" class="mybtn3 mybtn-bg btn btn-dark rounded-0 px-3 py-2 my-2" title="{{__('Buy Now')}}">
                                <span class="buy_text">{{__('Buy Now')}}</span>
                            </button>
                        </div>

                        <hr class="divider mb-0 mt-0">
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
