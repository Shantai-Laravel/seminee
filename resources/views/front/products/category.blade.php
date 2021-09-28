@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="oneCategoryContent">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'. $lang->lang) }}">Home</a></li>
                        <li><a href="#">{{ $category->translation->name }}</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <section class="similarProducts">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="row categoryGallery">
                            <div class="filterContainer">
                                <div class="btnFilter"></div>
                                <parameters-filter :category="{{ $category }}"></parameters-filter>
                            </div>
                            <div class="col-12">
                              <category :category="{{ $category }}" :product="0"></category>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        @include('front.partials.contact-form')
    </div>
</main>
@include('front.partials.footer')
@stop
