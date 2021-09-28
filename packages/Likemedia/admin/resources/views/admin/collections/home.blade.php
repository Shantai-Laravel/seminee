@extends('admin::admin.app')
@include('admin::admin.nav-bar')
@include('admin::admin.left-menu')
@section('content')

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/back') }}">Control Panel</a></li>
        <li class="breadcrumb-item active" aria-current="collection">Collections </li>
    </ol>
</nav>

<div class="title-block">
    <h3 class="title"> Product Collections </h3>
</div>


<div id="cover">
    <div class="items">

        <collections :collections_prop="{{ $collections }}" :langs="{{ $langs }}"></collections>

    </div>
</div>


<script src="{{asset('fronts/js/app.js')}}"></script>
<link rel="stylesheet" href="{{ asset('admin/css/nestable.css') }}">

@stop
@section('footer')
<footer>
    @include('admin::admin.footer')
</footer>
@stop
