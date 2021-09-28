@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="oneCategoryContent searchContent">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'. $lang->lang) }}">Home</a></li>
                        <li><a href="#">{{ trans('vars.Search.searchTitle') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <section class="similarProducts">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>{{ trans('vars.Search.searchResult') }} <b>&#171;</b> {{ Request::get('search') }} <b>&#187;</b></h3>
                    </div>
                    <div class="col-12">
                        <div class="row search">
                            <div class="col-md-8">
                                <form class="" action="" method="get">
                                    <input type="text" placeholder="{{ trans('vars.Search.searchTitle') }}..." name="search" value="{{ Request::get('search') }}"/>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="row">
                            @if ($products->count() > 0)
                                @foreach ($products as $key => $product)
                                    <a href="oneProduct.html" class="col-md-4 oneProduct">
                                        @if ($product->mainImage)
                                            <img src="/images/products/og/{{ $product->mainImage->src }}" alt="" />
                                        @endif
                                        <div class="itemDescr">
                                            <div class="name">
                                                <div>{{ $product->translation->name }}</div>
                                                <div></div>
                                            </div>
                                            @if ($product->mainPrice->price > 0)
                                                <div class="price">
                                                    <div>{{ $product->mainPrice->price }} {{ $currency->abbr }}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                                @else
                                    <p>{{ trans('vars.Search.searchNoResult') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>
@include('front.partials.footer')
@stop
