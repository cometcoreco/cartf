<?php
    if (!isset($authors)) {
        $authors = [];
    }

    if (!isset($languages)) {
        $languages = [];
    }

    if (!isset($product))
    {
        $product = null;
    }
?>

<div class="general-info-wrapper px-3">
    <h4 class="dashboard-common-title-two"> {{__('Product Additional Field Info')}} </h4>
    <div class="general-info-form mt-0 mt-lg-4">
            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Author") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
                <select name="author_id" id="tax" class="form-control">
                    <option value="">{{__('Select an author')}}</option>
                    @foreach($authors as $author)
                        <option value="{{$author->id}}" {{$product?->additionalFields?->author_id == $author->id ? 'selected' : ''}}>{{$author->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Pages") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
                <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->pages }}" name="page"
                       placeholder="{{ __("Enter page number...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Language") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
                <select name="language" id="tax" class="form-control">
                    <option value="">{{__('Select a language')}}</option>
                    @foreach($languages as $author)
                        <option value="{{$author->id}}" {{$product?->additionalFields?->language == $author->id ? 'selected' : ''}}>{{$author->name}}</option>
                    @endforeach
                </select>
            </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Formats") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->formats }}" name="formats"
                   placeholder="{{ __("Enter formats...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Words") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->words }}" name="word"
                   placeholder="{{ __("Enter words...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Tool Used") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->tool_used }}" name="tool_used"
                   placeholder="{{ __("Enter tool names...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Database Used") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->database_used }}" name="database_used"
                   placeholder="{{ __("Enter database names...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Compatible Browsers") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->compatible_browsers }}" name="compatible_browsers"
                   placeholder="{{ __("Enter compatible browser names...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("Compatible OS") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <input type="text" class="form--control radius-10" value="{{ $product?->additionalFields?->compatible_os }}" name="compatible_os"
                   placeholder="{{ __("Enter compatible os names...") }}">
        </div>

        <div class="dashboard-input mt-4">
            <label class="dashboard-label color-light mb-2"> {{ __("High Resolution") }} <sup class="text-primary">{{__('(Optional)')}}</sup></label>
            <select name="high_resolution" id="high_resolution" class="form-control">
                <option value="">{{__('Select an option')}}</option>
                <option value="yes" {{$product?->additionalFields?->high_resolution == 'yes' ? 'selected' : ''}}>{{__('Yes')}}</option>
                <option value="no" {{$product?->additionalFields?->high_resolution == 'no' ? 'selected' : ''}}>{{__('No')}}</option>
            </select>
        </div>
    </div>

    <h4 class="dashboard-common-title-two mt-5"> {{__('Product Additional Custom Field Info')}} <sup class="text-primary">{{__('(Optional)')}}</sup> </h4>
    <div class="general-info-form mt-0 mt-lg-4">
        @php
            $custom_fields = $product?->additionalCustomFields ?? [];
        @endphp

        @if(count($custom_fields) > 0)
            @foreach($custom_fields as $field)
                <div class="row mt-4 custom-additional-field-row">
                    <div class="col-5">
                        <input type="text" class="form--control radius-10" value="{{$field->option_name}}" name="option_name[]"
                               placeholder="{{ __("Option Name") }}">
                    </div>
                    <div class="col-5">
                        <input type="text" class="form--control radius-10" value="{{$field->option_value}}" name="option_value[]"
                               placeholder="{{ __("Option Value") }}">
                    </div>
                    <div class="col-2">
                        <div class="custom-button d-flex gap-3">
                            <a class="btn btn-info custom-plus" href="javascript:void(0)"><span class="mdi mdi-plus"></span></a>
                            <a class="btn btn-danger custom-minus" href="javascript:void(0)"><span class="mdi mdi-minus"></span></a>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="row custom-additional-field-row">
                <div class="col-5">
                    <input type="text" class="form--control radius-10" value="" name="option_name[]"
                           placeholder="{{ __("Option Name") }}">
                </div>
                <div class="col-5">
                    <input type="text" class="form--control radius-10" value="" name="option_value[]"
                           placeholder="{{ __("Option Value") }}">
                </div>
                <div class="col-2">
                    <div class="custom-button d-flex gap-3">
                        <a class="btn btn-info custom-plus" href="javascript:void(0)"><span class="mdi mdi-plus"></span></a>
                        <a class="btn btn-danger custom-minus" href="javascript:void(0)"><span class="mdi mdi-minus"></span></a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
