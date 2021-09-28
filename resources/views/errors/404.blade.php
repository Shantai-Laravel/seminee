@extends('front.app')

@section('content')

<div id="cover">

    <div class="fullWidthHeader">
        @include('front.partials.header')
    </div>

    <main>
      <div class="fourError blogContent">
        <ul class="crumbs">
            <li>
                <a href="https://juliaallert.com">Home</a>
            </li>
            <li>
                <a href="#">404</a>
            </li>
        </ul>
          <div class="container">
            <div class="row justify-content-center">
              <div class="col-md-6 col-sm-8 four">
                <img src="https://juliaallert.com/images/404.png">
                <p>The link you clicked may be broken or the page may have been removed.</p>
                <a href="https://juliaallert.com" class="btnError">back to home page</a>
              </div>
            </div>
          </div>
      </div>
    </main>

    @include('front.partials.footer')

</div>

@stop
