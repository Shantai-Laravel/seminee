<!DOCTYPE html>
<html lang="{{ @$lang->lang }}" currency="{{ @$currency->abbr }}" currency-rate="{{ @$currency->rate }}" main-currency={{ @$mainCurrency->abbr }} device="{{ isMobile() ? 'sm' : 'md' }}">
    <head>
        <meta charset="utf-8" />
        {{-- <meta name="robots" content="nofollow,noindex" />
        <meta name="googlebot" content="noindex, nofollow" /> --}}
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>{{ @$seoData['title'] }}</title>

        <meta name="description" content="{{ @$seoData['description'] }}">
        <meta name="keywords" content="{{ @$seoData['keywords'] }}">

        <meta name="_token" content="{{ csrf_token() }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
        <link
            rel="stylesheet"
            href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
            integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T"
            crossorigin="anonymous"
            />
        <link rel="stylesheet" type="text/css" href="{{ asset('fronts/css/style.bundle.css?'.uniqid())}}" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/css/select2.min.css">
    </head>
    <body>
        <div class="noClick">

        </div>
        <div id="cover">
            @yield('content')
        </div>
        <script
          src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
          integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
          crossorigin="anonymous"
        ></script>
        <script
          src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
          integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
          crossorigin="anonymous"
        ></script>
        <script
          src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
          integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
          crossorigin="anonymous"
        ></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="/{{ $lang->lang }}/js/lang.js?{{ uniqid('', true) }}"></script>
        <script src="{{ asset('fronts/js/app.js?'.uniqid()) }}"></script>
        <script src="{{ asset('fronts/js/bundle.js?'.uniqid()) }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.10/js/select2.min.js"></script>
        <script>
        $(document).ready(function() {
            function t(t) {
                if (!t.id) return t.text.toUpperCase();
                var e = $(t.element).attr("data-image");
                return e ? $('<span><img src="' + e + '" height="16px" width="16px" /> ' + t.text.toUpperCase() + "</span>") : t.text.toUpperCase()
            }
            $(".js-example-basic-single").select2({
                templateResult: t,
                templateSelection: t
            })
        });
        </script>
        @include('front.partials.modals')

    </body>
</html>
