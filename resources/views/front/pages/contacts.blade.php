@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="contactContent">
        <div class="videoBloc">
            <video loop autoplay>
                <source src="/fronts/img/video/camin.mp4" type="video/mp4" />
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="bcgBloc"></div>
        <audio loop autoplay>
            <source src="/fronts/img/video/burning.MP3" type="audio/mpeg" />
            Your browser does not support the audio element.
        </audio>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'.$lang->lang) }}">Home</a></li>
                        <li><a href="#">{{ $page->translation->name }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3>{{ trans('vars.PagesNames.pageNameContacts') }}</h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="contactBloc">
                        <p>{{ trans('vars.Contacts.address') }}</p>
                        <p>{{ trans('vars.Contacts.addressSiteMain') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contactBloc">
                        <p>{{ trans('vars.FormFields.fieldphone') }}</p>
                        <p><a href="tel:{{ trans('vars.Contacts.phoneNumber') }}"> {{ trans('vars.Contacts.phoneNumber') }} </a></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="contactBloc blocTwo">
                        <p>E-mail:</p>
                        <a href="mailto:{{ trans('vars.Contacts.email') }}">{{ trans('vars.Contacts.email') }}</a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="mapouter">
                        <div class="gmap_canvas">
                            {{-- <iframe
                            width="100%"
                            height="580"
                            id="gmap_canvas"
                            src="https://maps.google.com/maps?q=chisinau%20str%20petricani%2086&t=&z=13&ie=UTF8&iwloc=&output=embed"
                            frameborder="0"
                            scrolling="no"
                            marginheight="0"
                            marginwidth="0"
                            ></iframe
                            ><a href="https://www.vpnchief.com"></a> --}}
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2720.498758904801!2d28.84287101558453!3d47.01081413693655!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40c97c22192d44b7%3A0xab1a8f10fb08a5af!2zU3RyYWRhIE1pbGXFn3RpIDEyLCBDaGnImWluxIN1LCDQnNC-0LvQtNCw0LLQuNGP!5e0!3m2!1sru!2s!4v1582040447825!5m2!1sru!2s" width="100%" height="580" frameborder="0" style="border:0;" allowfullscreen=""></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <form action="{{ url('/'.$lang->lang.'/contact-feed-back') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="row">
                    <div class="col-md-12">
                        <h3>{{ trans('vars.Contacts.contactUs') }}</h3>
                    </div>
                </div>
                <div class="row contact-form">
                    <div class="col-md-6">
                        <input type="text" id="name" placeholder="{{ trans('vars.FormFields.fieldFullName') }}" name="name" required/>
                    </div>
                    <div class="col-md-6">
                        <input type="text" id="telefon" placeholder="{{ trans('vars.FormFields.fieldphone') }}" name="phone" required/>
                    </div>
                    <div class="col-md-6">
                        <input type="email" id="email" placeholder="{{ trans('vars.FormFields.fieldEmail') }}" name="email" required/>
                    </div>
                    <div class="col-md-6">
                        <textarea name="message" required id="" cols="30" rows="10" placeholder="{{ trans('vars.FormFields.needToKnow') }}"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="checkContainer">
                            <input type="checkbox" required="required">
                            {{ trans('vars.FormFields.termsUserAgreementPersonalData3') }}
                            <a href="{{ url($lang->lang.'/terms') }}" target="_blank"> {{ trans('vars.PagesNames.pageNameTermsConditions') }}</a>
                        </label>
                    </div>
                    <div class="col-md-12">
                        <input type="submit" class="butt" value="{{ trans('vars.FormFields.send') }}" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

@include('front.partials.footer')
@stop
