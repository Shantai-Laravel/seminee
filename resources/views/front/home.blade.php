@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="homeContent">
        <section class="bannerHome">
            <div class="container">
                <video src="/videos/bannerHome.mp4" poster="/videos/poster.jpg" muted autoplay loop>

                </video>
            </div>
        </section>
        <section class="similarProducts">
            <div class="container">
                <recomanded-products></recomanded-products>
            </div>
        </section>
        <section class="brands">
            <div class="container">
                @if ($brands->count() > 0)

                <div class="row">
                    <div class="col-12">
                        <h3>{{ trans('vars.PagesNames.pageBrands') }}</h3>
                    </div>
                </div>
                <div class="row brandsContent">
                    @foreach ($brands as $key => $brand)
                        <div class="col-md-3">
                            <a href="{{ $brand->link }}" target="_blank">
                                <img src="/images/brands/{{ $brand->logo }}" alt=""/>
                            </a>
                        </div>
                    @endforeach
                    <div class="col-12">
                        <a href="{{ url('/'.$lang->lang.'/brands') }}" class="butt"><span>{{ trans('vars.TehButtons.viewMore') }}</span></a>
                    </div>
                </div>
                @endif
            </div>
        </section>
        <section class="about">
            <div class="container">
                <div class="row aboutContent">
                    <div class="col-md-9">
                        <div class="aboutInner">
                            <h3>{{ trans('vars.General.aboutCompanyHeading') }}</h3>
                            <div class="row justify-content-between">
                                <div class="col-md-3 aboutItem">
                                    <div>{{ trans('vars.General.aboutCounter1') }}</div>
                                    <div>{{ trans('vars.General.aboutDescr1') }}</div>
                                </div>
                                <div class="col-md-3 aboutItem">
                                    <div>{{ trans('vars.General.aboutCounter2') }}</div>
                                    <div>{{ trans('vars.General.aboutDescr3') }}</div>
                                </div>
                                <div class="col-md-3 aboutItem">
                                    <div>{{ trans('vars.General.aboutCounter3') }}</div>
                                    <div>{{ trans('vars.General.aboutDescr3') }}</div>
                                </div>
                                <p>
                                    {{ trans('vars.General.aboutFooter') }}
                                </p>
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
