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
                        <li><a href="#">{{ trans('vars.PagesNames.pageNews') }}</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h3>{{ trans('vars.PagesNames.pageNews') }}</h3>
                </div>
            </div>
            <div class="row galleryNews">
                @if ($news->count() > 0)
                    @foreach ($news as $key => $new)
                    <a href="{{ url('/'.$lang->lang .'/news/'.$new->id) }}" class="col-md-6">
                        <div class="newsItem">
                            <img src="/images/blogs/{{ $new->image }}" alt="" />
                            <div class="date">
                                <div>{{ date('d', strtotime($new->created_at)) }}</div>
                                <div>
                                    @php
                                        setlocale(LC_TIME, $lang->description);
                                        echo $new->created_at->formatLocalized('%B');
                                    @endphp
                                </div>
                            </div>
                            <div class="title">
                                {{ $new->translation->name }}
                            </div>
                            <p>
                                {{ $new->translation->description }}
                            </p>
                        </div>
                    </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</main>
@include('front.partials.footer')
@stop
