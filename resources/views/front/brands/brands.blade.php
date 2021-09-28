@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="brandsPage">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'.$lang->lang) }}">Home</a></li>
                        <li><a href="#">{{ trans('vars.PagesNames.pageBrands') }}</a></li>
                    </ul>
                </div>
            </div>
            <section class="brands">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h3>{{ trans('vars.PagesNames.pageBrands') }}</h3>
                        </div>
                    </div>
                    <div class="brandsGalery">
                        @if ($brands->count() > 0)
                        @foreach ($brands as $key => $brand)
                        @if ($key % 1 == 0)
                        <article class="brandsItem">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="name">{{ $brand->translation->name }}</div>
                                    <p>
                                        {!! $brand->translation->description !!}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ $brand->link }}" target="_blank">
                                        <img src="/images/brands/{{ $brand->logo }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        </article>
                        @else
                        <article class="brandsItem">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="name">{{ $brand->translation->name }}</div>
                                    <div class="sub">{{ trans('vars.General.production') }} {{ $brand->translation->seo_text }}</div>
                                    <p>
                                        {!! $brand->translation->description !!}
                                    </p>
                                </div>
                                <div class="col-md-4">
                                    <a href="{{ $brand->link }}" target="_blank">
                                        <img src="/images/brands/{{ $brand->logo }}" alt="" />
                                    </a>
                                </div>
                            </div>
                        </article>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>
@include('front.partials.footer')
@stop
