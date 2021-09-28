@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="oneCategoryContent salesContent">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'.$lang->lang) }}">Home</a></li>
                        <li><a href="#">{{ $promotion->translation->name }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <section class="similarProducts">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>{{ $promotion->translation->name }}</h3>
                    </div>
                    <div class="slideItem col-12">
                        <img src="/images/promotions/{{ $promotion->img }}" alt="" />
                        <div class="slideInner">
                            <div class="contentInner">
                                <div class="miniBanner">
                                    <img src="/fronts/img/prod/bcgSlide1.png" alt="" />
                                    <div class="text">
                                        <div class="name">
                                            {{ $promotion->translation->name }}
                                        </div>
                                        <p>
                                            {{ $promotion->translation->description }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <promotion-products :promotion="{{ $promotion }}"></promotion-products>

                </div>
            </div>
        </section>
    </div>
</main>
@include('front.partials.footer')
@stop
