@extends('front.app')
@section('content')
@include('front.partials.header')

<main>
    <div class="info">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li>
                            <a href="{{ url('/'.$lang->lang) }}">Home</a>
                        </li>
                        <li>
                            <a href="#">{{ $page->translation->title }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            {!! $page->translation->body !!}

        </div>
        @include('front.partials.contact-form')
    </div>
</main>

@include('front.partials.footer')
@stop
