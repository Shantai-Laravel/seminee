@extends('admin::admin.app')
@include('admin::admin.nav-bar')
@include('admin::admin.left-menu')
@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('/back') }}">Control panel</a></li>
        <li class="breadcrumb-item"><a href="{{ route('frontusers.index') }}">Front Users</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Front User</li>
    </ol>
</nav>
<div class="title-block">
    <h3 class="title"> Edit Front User </h3>
    @include('admin::admin.list-elements', [
    'actions' => [
            'Add new user' => route('frontusers.create'),
        ]
    ])
</div>
<div class="list-content">
    <div class="tab-area">
        @include('admin::admin.alerts')
    </div>
    <form class="form-reg" role="form" method="POST" action="{{ route('frontusers.update', $user->id) }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}
        <div class="part left-part">
            <h5>User Information</h5>
            <ul>
                <li>
                    <label for="name">Name</label>
                    <input type="text" name="name" class="name" id="name" value="{{$user->name}}">
                </li>
                <li>
                    <label for="surname">Surname</label>
                    <input type="text" name="surname" class="name" id="surname" value="{{$user->surname}}">
                </li>
                <li>
                    <label for="email">Email</label>
                    <input type="email" name="email" class="name" id="email" value="{{$user->email}}">
                </li>
                <li>
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" class="name" id="phone" value="{{$user->phone}}">
                </li>
                <li>
                    <label for="date">Birthday</label>
                    <input type="date" name="birthday" class="name" id="date" value="{{$user->birthday}}">
                </li>
                <li>
                    <label>Countries</label>
                    <select class="" name="country_id">
                    @foreach ($countries as $key => $country)
                    <option value="{{ $country->id }}" {{ $user->country_id == $country->id  ? 'selected' : '' }}>{{ $country->translation->name }}</option>
                    @endforeach
                    </select>
                </li>
                <li>
                    <label>Currecy</label>
                    <select class="" name="currency_id">
                    @foreach ($currencies as $key => $currency)
                    <option value="{{ $currency->id }}"  {{ $user->currency_id == $currency->id ? 'selected' : '' }}>{{ $currency->abbr }}</option>
                    @endforeach
                    </select>
                </li>
                <li>
                    <label>Languages</label>
                    <select class="" name="language_id">
                    @foreach ($languages as $key => $language)
                    <option value="{{ $language->id }}" {{ $user->lang_id == $language->id ? 'selected' : '' }}>{{ $language->lang }}</option>
                    @endforeach
                    </select>
                </li>
                <li>
                    <label for="terms_agreement">
                    <input type="checkbox" name="terms_agreement" class="name" id="terms_agreement" {{$user->terms_agreement == 1 ? 'checked' : ''}}>
                    <span>Terms Agreement</span>
                    </label>
                </li>
                <li>
                    <label for="promo_agreement">
                    <input type="checkbox" name="promo_agreement" class="name" id="promo_agreement" {{$user->promo_agreement == 1 ? 'checked' : ''}}>
                    <span>Promo Agreement</span>
                    </label>
                </li>
                <li>
                    <label for="personaldata_agreement">
                    <input type="checkbox" name="personaldata_agreement" class="name" id="personaldata_agreement" {{$user->personaldata_agreement == 1 ? 'checked' : ''}}>
                    <span>Personaldata Agreement</span>
                    </label>
                </li>
                <li>
                    <label>Date of Registration: {{$user->created_at}}</label>
                </li>
                <li>
                    <input type="submit" value="Save">
                </li>
            </ul>
        </div>
    <div class="part right-part">
        <h5>Shipping Information</h5>
        <div class="address">
            {{ csrf_field() }}
            <div class="frAdr">
                <div class="address">
                    @if (!is_null($user->address))
                        @include('admin::admin.frontusers.editAddress', ['address' => $user->address])
                    @else
                        @include('admin::admin.frontusers.address')
                    @endif
                </div>
            </div>
        </div>
    </div>
</form>

</div>
@stop
@section('footer')
<footer>
    @include('admin::admin.footer')
</footer>
@stop
