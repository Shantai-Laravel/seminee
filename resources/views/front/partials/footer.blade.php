<footer>
    <div class="container">
        <div class="row">
            <div class="col-md-5 col-sm-12 footerBloc">
                <a href="index.html" class="logo"><img src="/fronts/img/icons/logo.png" alt=""/></a>
                <p>
                    {{ trans('vars.HeaderFooter.footerText') }}
                </p>
            </div>
            <div class="col-md col-sm-12 footerBloc">
                <div class="footerTitle">{{ trans('vars.HeaderFooter.aboutTitle') }}</div>
                <div class="line"></div>
                <ul>
                    <li><a href="{{ url('/'. $lang->lang. '/brands') }}">{{ trans('vars.PagesNames.pageBrands') }}</a></li>
                    <li><a href="{{ url('/'. $lang->lang. '/info') }}">{{ trans('vars.PagesNames.pageInfo') }}</a></li>
                    <li><a href="{{ url('/'. $lang->lang. '/about') }}">{{ trans('vars.PagesNames.pageAboutUs') }}</a></li>
                    <li><a href="{{ url('/'. $lang->lang. '/contacts') }}">{{ trans('vars.Contacts.contactsTitle') }}</a></li>
                </ul>
            </div>
            <div class="col-md col-sm-12 footerBloc">
                <div class="footerTitle">{{ trans('vars.HeaderFooter.allProductsTitle') }}</div>
                <div class="line"></div>
                <ul>
                    @if ($categoriesMenu)
                        @foreach ($categoriesMenu as $key => $category)
                            <li><a href="{{ url('/'.$lang->lang.'/catalog/'.$category->alias) }}">{{ $category->translation->name }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <div class="col-md col-sm-12 footerBloc">
                <div class="footerTitle">{{ trans('vars.HeaderFooter.supportTitle') }}</div>
                <div class="line"></div>
                <ul>
                    <li><a href="tel:+37369568844">Tel: {{ trans('vars.Contacts.phoneNumber') }}</a></li>
                    <li><a href="mailto:info@inconarm.md">Mail: {{ trans('vars.Contacts.email') }}</a></li>
                    <li>
                        <p>{{ trans('vars.Contacts.addressSiteMain') }}</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="row justify-content-between align-items-center">
            <div class="col-sm-auto col-12 prefooter">
                ©{{ date('Y') }} {{ trans('vars.HeaderFooter.footerCopyright') }} Like-Media
            </div>
            <div class="col-sm-auto col-12 retFooter">
                <a href="{{ trans('vars.Contacts.facebook')  }}" class="f"></a>
                <a href="{{ trans('vars.Contacts.instagram') }}" class="i"></a>
            </div>
        </div>
    </div>
</footer>
