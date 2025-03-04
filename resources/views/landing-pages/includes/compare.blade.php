<section class="compare-section" id="compares">
    @if($landingPage->is_compares_section_top_image)
        <img class="shape2" src="{{asset('storage/'.$landingPage->compares_section_top_image??'assets/images/bg-shape2.png')}}" alt="">
    @endif

    @if($landingPage->is_compares_section_bottom_image)
        <img class="shape" src="{{asset('storage/'.$landingPage->compares_section_bottom_image??'assets/images/bg-shape.png')}}" alt="">
    @endif

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="section-title extra">
                    <h2 class="title">{{$landingPage->compare_title}}</h2>
                    <p class="subtitle">
                        {{$landingPage->compare_subtitle}}
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <table class="compare-table table table-responsive-lg table-bordered table-striped">
                    <thead>
                        <tr>
                            <th></th>
                            @php
                                $hasPrices=false;
                                $hasBrands=false;
                                $hasColors=false;
                                $hasDimentions=false;
                                $hasWeights=false;
                                $hasAttributes=false;
                            @endphp

                            @foreach($landingPage->comparesItems as $compareItem )
                                @php
                                    $hasPrices=$compareItem->price?true:$hasPrices;
                                    $hasBrands=$compareItem->brand?true:$hasBrands;
                                    $hasColors=$compareItem->color?true:$hasColors;
                                    $hasDimentions=$compareItem->dimentions?true:$hasDimentions;
                                    $hasWeights=$compareItem->weight?true:$hasWeights;
                                    $hasAttributes=$compareItem->attributes?true:$hasAttributes;
                                @endphp

                                <th class="text-center">
                                    <div class="product-image">
                                        <img src="{{asset('storage/'.$compareItem->image)}}" alt="">
                                    </div>
                                    <h3 class="product-title">{{$compareItem->title}}</h3>
                                    <p class="product-subtitle">{{$compareItem->subtitle}}</p>
                                </th>
                            @endforeach
                        </tr>

                        @if($hasPrices)
                            <tr>
                                <th class="property-title">Price</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$settingData['currency_code']}} {{$compareItem->price}}</th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>

                    <tbody>
                        @if($hasBrands)
                            <tr>
                                <th class="property-title">Brand</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$compareItem->brand}}</th>
                                @endforeach
                            </tr>
                        @endif

                        @if($hasColors)
                            <tr>
                                <th class="property-title">Color</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$compareItem->color}}</th>
                                @endforeach
                            </tr>
                        @endif

                        @if($hasDimentions)
                            <tr>
                                <th class="property-title">Item Dimensions</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$compareItem->dimentions}}</th>
                                @endforeach

                            </tr>
                        @endif

                        @if($hasWeights)
                            <tr>
                                <th class="property-title">Item Weight</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$compareItem->weight}}</th>
                                @endforeach

                            </tr>
                        @endif

                        @if($hasAttributes)
                            <tr>
                                <th class="property-title">Attributes</th>
                                @foreach($landingPage->comparesItems as $compareItem)
                                    <th class="price text-center">{{$compareItem->attributes}}</th>
                                @endforeach

                            </tr>
                        @endif
                    </tbody>

                    <tfoot>
                        <tr>
                            <th></th>
                            @foreach($landingPage->comparesItems as $compareItem)
                                @if($compareItem->cta_button)
                                    <td class="text-center"><a
                                            @if($compareItem->cta_button_link)  href="{{$compareItem->cta_button_link}}"
                                            @else data-toggle="modal" data-target="#purchaseModal" @endif

                                            class="mybtn3 mybtn-bg"><span>{{$compareItem->cta_button_text}}</span></a>
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>
