<header>
    <div class="relativeHeader">
        <div class="container headerBlock">
            <div class="row menuUp">
                <ul class="col-auto menuUpLeft">
                    <li><a href="{{ url('/'. $lang->lang .'/about') }}">{{ trans('vars.PagesNames.pageAboutUs') }}</a></li>
                    <li><a href="{{ url('/'. $lang->lang .'/info') }}">{{ trans('vars.PagesNames.pageInfo') }}</a></li>
                </ul>
                <a class="logo" href="{{ url('/'. $lang->lang) }}"><img src="/fronts/img/icons/logo.png" alt=""/></a>
                <ul class="col-auto menuCabinet">
                  <li class="buttMenu">
                      <a href="{{ url('/'. $lang->lang .'/news') }}"><span>{{ trans('vars.PagesNames.pageNews') }}</span></a>
                  </li>
                    <li class="buttMenu buttSearch">
                        <span></span>
                        <div class="menuOpen">
                            <div class="headMenuOpen">
                                {{ trans('vars.Search.searchTitle') }}
                                <div class="closeMenu"></div>
                            </div>
                            <search-box></search-box>
                        </div>
                    </li>
                    <li class="buttMenu buttLang">
                        @if (Request::segment(1))
                            @if ($lang->lang == 'ro')
                                <a href="{{ url(str_replace('/ro', '/ru', Request::url())) }}"><span>RU</span></a>
                            @else
                                <a href="{{ url(str_replace('/ru', '/ro', Request::url())) }}"><span>RO</span></a>
                            @endif
                        @else
                            <a href="{{ url(Request::url().'/ru') }}"><span>RU</span></a>
                        @endif
                    </li>
                </ul>
            </div>
            <div class="row menuCenter">
                <input type="text" class="mobileSearch" placeholder="Search" />
                <ul class="col-auto">
                    <li>
                        <a href="{{ url('/'. $lang->lang .'/brands') }}"><span>{{ trans('vars.PagesNames.pageBrands') }}</span></a>
                    </li>
                    <li class="menuItem">
                        <span>{{ trans('vars.PagesNames.pageProducts') }}</span>
                        <ul class="submenu">
                            <li class="titleSubmenu"></li>
                            @if (count($categoriesMenu))
                                @foreach ($categoriesMenu as $key => $categoryItem)
                                    @if (count($categoryItem->children()->get()) > 0)
                                        <li class="submenuItem">
                                            <span><a href="{{ url($lang->lang.'/catalog/'.$categoryItem->alias) }}">{{ $categoryItem->translation($lang->id)->first()->name }}</a></span>
                                            <ul class="submenuItemBloc">
                                                <li class="titleSubmenu"></li>
                                                @foreach ($categoryItem->children()->get() as $key => $categoryItem2)
                                                    <li>
                                                        <a href="{{ url($lang->lang.'/catalog/'.$categoryItem2->alias) }}">
                                                            {{ $categoryItem2->translation($lang->id)->first()->name }}
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @else
                                        <li>
                                            <a href="{{ url($lang->lang.'/catalog/'.$categoryItem->alias) }}"> {{ $categoryItem->translation($lang->id)->first()->name }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </li>
                </ul>
                <ul class="col-auto">
                    <li>
                        <a href="{{ url('/'. $lang->lang .'/sale') }}"><span>{{ trans('vars.PagesNames.pageNameOutletTitle') }}</span></a>
                    </li>
                    <li>
                        <a href="{{ url('/'. $lang->lang .'/contacts') }}"><span>{{ trans('vars.PagesNames.pageNameContacts') }}</span></a>
                    </li>
                    <li>
                        <a href="{{ url('/'. $lang->lang .'/news') }}"><span>{{ trans('vars.PagesNames.pageNews') }}</span></a>
                    </li>
                </ul>
            </div>
            <div id="burger" class="burger">
                <div class="iconBar"></div>
            </div>
        </div>
    </div>
</header>
