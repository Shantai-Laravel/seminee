<section class="request">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-5 blocOne">
                <div class="title">{{ trans('vars.FormFields.contactPopupHeading') }}</div>
            </div>
            <div class="col-md-7 blocTwo">
                <form action="{{ url('/'.$lang->lang.'/contact-feed-back') }}" method="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="text" placeholder="{{ trans('vars.FormFields.fieldFullName') }}" name="name" required/>
                    <input type="email" placeholder="{{ trans('vars.FormFields.fieldEmail') }}"name="email" required />
                    <input type="text" placeholder="{{ trans('vars.FormFields.fieldphone') }}" name="phone" required/>
                    <textarea name="message" required id="message" cols="30" rows="10" placeholder="{{ trans('vars.FormFields.needToKnow') }}"></textarea>
                    <label class="checkContainer">
                        <input type="checkbox" required="required">
                        {{ trans('vars.FormFields.termsUserAgreementPersonalData3') }}
                        <a href="{{ url($lang->lang.'/terms') }}" target="_blank"> {{ trans('vars.PagesNames.pageNameTermsConditions') }}</a>
                    </label>
                    <input type="submit" value="{{ trans('vars.FormFields.send') }}" class="butt" />
                </form>
            </div>
        </div>
    </div>
</section>
