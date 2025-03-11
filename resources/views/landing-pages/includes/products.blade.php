<section class="pricing" id="pricing">
    @if($landingPage->is_products_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->products_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_products_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->products_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title">
                    <h2 class="title">{{$landingPage->product_title}}</h2>
                    <p>
                        {{$landingPage->product_subtitle}}
                    </p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="product-slider">
                    @foreach($landingPage->productsItems as $productItem)
                        <div class="item">
                            <div class="single-product">
                                <div class="img">
                                    <img src="{{asset('storage/'.$productItem->image)}}" alt="">
                                    @if($productItem->cta_button)
                                        <div class="links">
                                            <a target="_blank"
                                            @if($productItem->cta_button_link)  href="{{$productItem->cta_button_link}}"
                                            @else data-toggle="modal" data-target="#purchaseModal" @endif
                                            class="mybtn3 mybtn-bg"><span>{{$productItem->cta_button_text}}</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="content">
                                    <ul class="stars">
                                        @for($i=0;$i<$productItem->rate;$i++)
                                            <li>
                                                <i class="fas fa-star"></i>
                                            </li>
                                        @endfor
                                    </ul>
                                    <div class="price">
                                        <p class="new-price">
                                            {{$landingPageSettings?->currency_code}} {{$productItem->price}}
                                        </p>
                                        @if($productItem->after_discount_price)
                                            <p class="old-price">
                                                <del>
                                                    {{$landingPageSettings?->currency_code}} {{$productItem->after_discount_price}}
                                                </del>
                                            </p>

                                        @endif

                                    </div>

                                    <h4 class="title">
                                        {{$productItem->title}}
                                    </h4>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
