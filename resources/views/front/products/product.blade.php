@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="oneProductContent">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'.$lang->lang) }}">Home</a></li>
                        <li><a href="{{ url('/'.$lang->lang.'/catalog/'.$product->category->alias) }}">{{ $product->category->translation->name }}</a></li>
                        <li><a href="#">{{ $product->translation->name }}</a></li>
                    </ul>
                </div>
            </div>
            <section class="row blocOne">
                <div class="col-lg-7 col-md-8">
                    <div class="slideOneProduct">
                        @if ($product->images()->count())
                            @foreach ($product->images as $key => $image)
                                <img src="/images/products/og/{{ $image->src }}" alt="" />
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-lg-5 col-md-8 itemDescr">
                    <div>
                        <div class="name">{{ $product->translation->name }}</div>
                        <div class="artNumber"><span>{{ trans('vars.DetailsProductSet.ProductCod') }}</span><span>{{ $product->code }}</span></div>
                    </div>
                    <div class="moreInformation">
                        @php
                            $paramImages = getParamImages($product->id, 21);
                        @endphp
                        @if (count($paramImages) > 0)
                            <div class="colorOptions">
                                <div class="titleTab">{{ trans('vars.DetailsProductSet.colors') }}:</div>
                                <div class="colorContainer">
                                    @foreach ($paramImages as $key => $paramImage)
                                        <img src="/images/parameters/{{ $paramImage->image }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @php
                            $paramImages = getParamImages($product->id, 23);
                        @endphp
                        @if (count($paramImages) > 0)
                            <div class="colorOptions">
                                <div class="titleTab">{{ trans('vars.DetailsProductSet.options') }}:</div>
                                <div class="colorContainer">
                                    @foreach ($paramImages as $key => $paramImage)
                                        <img src="/images/parameters/{{ $paramImage->image }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div>
                        @if ($product->mainPrice->price > 0)
                            <div class="priceProduct">
                                <span>{{ $product->mainPrice->price }} {{ $currency->abbr }}</span>
                                <span class="reducePrice">
                                    @if ($product->mainPrice->old_price)
                                        {{ $product->mainPrice->old_price }} {{ $currency->abbr }}
                                    @endif
                                </span>
                            </div>
                        @endif
                        <a href="#" class="butt" data-toggle="modal" data-target="#request">{{ trans('vars.TehButtons.btnOrder') }}</a>
                    </div>
                </div>
                <div class="row pdescr">
                  <p class="col-12">
                      {{ $product->translation->body }}
                  </p>
                </div>
            </section>
        </div>

        <section class="param">
            <div class="bcgParam"></div>
            <div class="container">
                @if ($product->propertyValues->count() > 0)
                <div class="row">
                    <div class="col-md-5 nameParam">
                        {{ trans('vars.DetailsProductSet.parameters') }}
                    </div>
                    <div class="col-md-7">
                        <div class="row paramBloc">
                            @php
                                $checkId = [];
                            @endphp
                            @foreach ($product->propertyValues as $key => $productValue)
                            @if (count($productValue->parametersAdditional) > 0)
                            @if ($productValue->parameter->type == 'select')
                                @if (!is_null($productValue->value) && !is_null($productValue->value->translation))
                                    <div class="col-6">
                                        <div class="item">
                                            <div>{{ $productValue->parameter->translation->name }}</div>
                                            <div>
                                                {{ @$productValue->value->translation->name }}
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @elseif ($productValue->parameter->type == 'checkbox')
                            @if (!in_array($productValue->parameter->id, $checkId))

                            @php
                                $values = getCheckboxValues($product->id, $productValue->parameter_id);
                                $checkId[] = $productValue->parameter->id;
                            @endphp

                            @if (count($values) > 0)
                            <div class="col-6">
                                <div class="item">
                                    <div>{{ $productValue->parameter->translation->name }}</div>
                                    <div>
                                        @foreach ($values as $key => $val)
                                            @if ($key !== 0) , @endif
                                                @if ($val->value)
                                                    {{ $val->value->translation->name }}
                                                @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif

                            @endif
                            @else
                                @if (!is_null($productValue->translation))
                                    @if ($productValue->translation->value !== null)
                                    <div class="col-6">
                                        <div class="item">
                                            <div>{{ $productValue->parameter->translation->name }}</div>
                                            <div>{{ $productValue->translation->value }} {{ $productValue->parameter->translation->unit }}</div>
                                        </div>
                                    </div>
                                    @endif
                                @endif
                            @endif
                            @endif
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </section>

        @if ($product->materials->count() > 0)
        <section class="similarProducts">
            <div class="bcgParam">
                {{ trans('vars.DetailsProductSet.products') }} <br /> {{ trans('vars.DetailsProductSet.recomanded') }}
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class=" slideSimilar">
                            <div class="slideItem">
                                @foreach ($product->materials as $key => $material)
                                <a href="{{ url('/'. $lang->lang . '/catalog/'.$material->product->category->alias. '/'. $material->product->alias) }}" class="oneProduct">
                                    @if ($material->product->mainImage)
                                        <img src="/images/products/og/{{ $material->product->mainImage->src }}" alt=""/>
                                    @else
                                        <img src="/images/noimage.jpg" alt=""/>
                                    @endif
                                    <div class="itemDescr">
                                        <div class="name">
                                            <div>{{ $material->product->translation->name }}</div>
                                            <div></div>
                                        </div>
                                        @if ($material->product->mainPrice->price > 0)
                                            <div class="price">
                                                <div>{{ $material->product->mainPrice->price }} {{ $currency->abbr }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="slideSimilar slideSimilarMobile">
                            @foreach ($product->materials as $key => $material)
                            <a href="{{ url('/'. $lang->lang . '/catalog/'.$material->product->category->alias. '/'. $material->product->alias) }}" class="oneProduct">
                                @if ($material->product->mainImage)
                                    <img src="/images/products/og/{{ $material->product->mainImage->src }}" alt=""/>
                                @else
                                    <img src="/images/noimage.jpg" alt=""/>
                                @endif
                                <div class="itemDescr">
                                    <div class="name">
                                        <div>{{ $material->product->translation->name }}</div>
                                        <div></div>
                                    </div>
                                    @if ($material->product->mainPrice->price > 0)
                                        <div class="price">
                                            <div>{{ $material->product->mainPrice->price }} {{ $currency->abbr }}</div>
                                        </div>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @else
        @if ($similarProducts->count() > 0)
        <section class="similarProducts">
            <div class="bcgParam">
                {{ trans('vars.DetailsProductSet.products') }} <br /> {{ trans('vars.DetailsProductSet.recomanded') }}
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-12">
                        <div class=" slideSimilar">
                            <div class="slideItem">
                                @foreach ($similarProducts as $key => $prod)
                                <a href="{{ url('/'. $lang->lang . '/catalog/'.$prod->category->alias. '/'. $prod->alias) }}" class="oneProduct">
                                    @if ($prod->mainImage)
                                    <img src="/images/products/og/{{ $prod->mainImage->src }}" alt="" />
                                    @else
                                    <img src="/images/noimage.jpg" alt=""/>
                                    @endif
                                    <div class="itemDescr">
                                        <div class="name">
                                            <div>{{ $prod->translation->name }}</div>
                                            <div></div>
                                        </div>
                                        @if ($prod->mainPrice->price > 0)
                                        <div class="price">
                                            <div>{{ $prod->mainPrice->price }} {{ $currency->abbr }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="slideSimilar slideSimilarMobile">
                            @foreach ($similarProducts as $key => $prod)
                            <a href="{{ url('/'. $lang->lang . '/catalog/'.$prod->category->alias. '/'. $prod->alias) }}" class="oneProduct">
                                @if ($prod->mainImage)
                                <img src="/images/products/og/{{ $prod->mainImage->src }}" alt="" />
                                @endif
                                <div class="itemDescr">
                                    <div class="name">
                                        <div>{{ $prod->translation->name }}</div>
                                        <div></div>
                                    </div>
                                    @if ($prod->mainPrice->price > 0)
                                    <div class="price">
                                        <div>{{ $prod->mainPrice->price }} {{ $currency->abbr }}</div>
                                    </div>
                                    @endif
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @endif
        @endif
    </div>
</main>
<div class="modals">
    <div class="modal" id="request">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ url('/'.$lang->lang. '/product/pre-order') }}" method="post">
                    <div class="close" data-dismiss="modal"></div>
                    <h4>{{ trans('vars.Orders.makeRequestTitle') }}</h4>
                    <p>
                        {{ trans('vars.Orders.requestBody') }}
                    </p>
                    <div class="row">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="col-12">
                            <label for="name">{{ trans('vars.FormFields.fieldFullName') }}</label>
                            <input type="text" id="name" name="name" required/>
                        </div>
                        <div class="col-12">
                            <label for="telefon">{{ trans('vars.FormFields.fieldphone') }}</label>
                            <input type="text" id="telefon" name="phone" required/>
                        </div>
                        <div class="col-12">
                            <label for="email">{{ trans('vars.FormFields.fieldEmail') }}</label>
                            <input type="email" id="email" name="email" required/>
                        </div>
                        <div class="col-12">
                            <label for="msg">{{ trans('vars.FormFields.fieldMessage') }}</label>
                            <textarea id="" cols="30" rows="10" name="message" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="checkContainer">
                                <input type="checkbox" required="required">
                                {{ trans('vars.FormFields.termsUserAgreementPersonalData3') }}
                                <a href="{{ url($lang->lang.'/terms') }}" target="_blank"> {{ trans('vars.PagesNames.pageNameTermsConditions') }}</a>
                            </label>
                        </div>
                        <div class="col-12">
                            <input type="submit" class="butt" value="{{ trans('vars.FormFields.send') }}" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@include('front.partials.footer')
@stop
