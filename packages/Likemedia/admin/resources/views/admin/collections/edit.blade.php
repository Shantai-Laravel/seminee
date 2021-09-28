@extends('admin::admin.app')
@include('admin::admin.nav-bar')
@include('admin::admin.left-menu')
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/back') }}">Control Panel</a></li>
        <li class="breadcrumb-item"><a href="{{ url('back/product-collections') }}">Collections</a></li>
        <li class="breadcrumb-item active" aria-current="collection">Edit collection</li>
    </ol>
</nav>
<div class="title-block">
    <h3 class="title"> Edit collection </h3>
</div>
@include('admin::admin.alerts')
<div class="list-content">
    <div class="card">
        <div class="card-block">
            <form class="form-reg" role="form" method="POST" action="{{ route('product-collections.update', $collection->id) }}" id="add-form" enctype="multipart/form-data">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}
                <div class="row">
                    <div class="col-md-8">
                        <div class="tab-area">
                            <ul class="nav nav-tabs nav-tabs-bordered">
                                @if (!empty($langs))
                                @foreach ($langs as $lang)
                                <li class="nav-item">
                                    <a href="#{{ $lang->lang }}" class="nav-link  {{ $loop->first ? ' open active' : '' }}"
                                        data-target="#{{ $lang->lang }}">{{ $lang->lang }}</a>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                        @if (!empty($langs))
                        @foreach ($langs as $lang)
                        <div class="tab-content {{ $loop->first ? ' active-content' : '' }}" id={{ $lang->
                            lang }}>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="name-{{ $lang->lang }}">Title [{{ $lang->lang }}]</label>
                                <input type="text" name="title_{{ $lang->lang }}" value="{{ $translation->name }}" class="form-control">
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="description-{{ $lang->lang }}">Description [{{ $lang->lang }}]</label>
                                <textarea name="description_{{ $lang->lang }}" class="form-control">{{ $translation->description }}</textarea>
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="description-{{ $lang->lang }}">Body [{{ $lang->lang }}]</label>
                                <textarea name="body_{{ $lang->lang }}" id="body-{{ $lang->lang }}" class="form-control">{{ $translation->body }}</textarea>
                                <script>
                                    CKEDITOR.replace('body-{{ $lang->lang }}', {
                                        language: '{{$lang}}',
                                        height: '200px'
                                    });
                                </script>
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="meta_title_{{ $lang->lang }}">Seo Title [{{ $lang->lang }}]</label>
                                <input type="text" name="seo_title_{{ $lang->lang }}" class="form-control" value="{{ $translation->seo_title }}">
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="seo_descr_{{ $lang->lang }}">Seo Description [{{ $lang->lang }}]</label>
                                <input type="text" name="seo_descr_{{ $lang->lang }}" class="form-control" value="{{ $translation->seo_description }}" >
                                @endif
                                @endforeach
                            </div>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id && !is_null($translation->lang_id))
                                <label for="seo_keywords_{{ $lang->lang }}">Seo Keywords [{{ $lang->lang }}]</label>
                                <input type="text" name="seo_keywords_{{ $lang->lang }}" class="form-control" value="{{ $translation->seo_keywords }}">
                                @endif
                                @endforeach
                            </div>
                            <hr>
                            <div class="form-group">
                                @foreach($collection->translations as $translation)
                                @if($translation->lang_id == $lang->id)
                                @if ($translation->image)
                                <img src="{{ asset('/images/collections/'. $translation->image ) }}" height="100px">
                                <input type="hidden" name="old_image_{{ $lang->lang }}" value="{{ $translation->image }}"/>
                                @else
                                <img src="{{ asset('/admin/img/noimage.jpg') }}" style="height: 100px;">
                                @endif
                                @endif
                                @endforeach
                                <label for="img-{{ $lang->lang }}">Image [{{ $lang->lang }}]</label>
                                <input type="file" name="image_{{ $lang->lang }}" id="img-{{ $lang->lang }}"/>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <div class="col-md-4">
                        <hr>
                        <div class="form-group">
                            <label>
                            <input type="checkbox" name="active" {{ $collection->active ? 'checked' : '' }}>
                            <span> Active</span>
                            </label>
                        </div>
                        <div class="form-group">
                            <label for="img">Banner</label>
                            <input type="file" name="banner" id="img"/>
                            <br>
                            @if ($collection->banner)
                            <img src="{{ asset('/images/collections/'. $collection->banner ) }}" style="width: 70%;">
                            <input type="hidden" name="old_banner" value="{{ $collection->banner }}"/>
                            @else
                            <img src="{{ asset('/admin/img/noimage.jpg') }}" style="width: 70%;">
                            @endif
                            <hr>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <input type="submit" value="Save" class="btn btn-primary">
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@section('footer')
<footer>
    @include('admin::admin.footer')
</footer>
@stop
