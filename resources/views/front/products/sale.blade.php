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
                  <li><a href="#">{{ trans('vars.PagesNames.pageNameOutletTitle') }}</a></li>
                </ul>
              </div>
            </div>
          </div>

          <section class="similarProducts">
            <div class="container">

                <sale></sale>

            </div>
          </section>

          @include('front.partials.contact-form')

        </div>
      </main>

    @include('front.partials.footer')

@stop
