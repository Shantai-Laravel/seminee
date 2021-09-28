@extends('front.app')
@section('content')
@include('front.partials.header')
<main>
    <div class="newsPageContent">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <ul class="crumbs">
                        <li><a href="{{ url('/'.$lang->lang) }}">Home</a></li>
                        <li><a href="{{ url('/'.$lang->lang.'/news') }}">{{ trans('vars.PagesNames.pageNews') }}</a></li>
                        <li><a href="#">{{ $new->translation->name }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="row newOne">
                <div class="col-12">
                    <div class="title">
                        {{ $new->translation->name }}
                    </div>
                </div>
                <div class="col-12 text-right">
                    {{ date('d m Y', strtotime($new->created_at)) }}
                </div>
                <div class="col-12">
                    <img src="/images/blogs/{{ $new->image }}" alt="" />
                </div>
                {!! $new->translation->body !!}
            </div>
        </div>
    </div>
</main>
@include('front.partials.footer')
@stop
