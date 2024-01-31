@php
    if(!isset($product)){
        $product = null;
    }
@endphp

<div class="general-info-wrapper">
    <h4 class="dashboard-common-title-two"> {{ __("General Information") }} </h4>
    <div class="general-info-form mt-0 mt-lg-4">
        <form action="#">
            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Name") }} </label>
                <input type="text" class="form--control radius-10" id="product-name" value="{{ $product?->name ?? "" }}" name="name" placeholder="{{ __("Write product Name...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Slug") }} </label>
                <input type="text" class="form--control radius-10" id="product-slug" value="{{ $product?->slug ?? "" }}" name="slug" placeholder="{{ __("Write product slug...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Summary") }} </label>
                <textarea style="height: 120px" class="form--control form--message  radius-10"  name="summary" placeholder="{{ __("Write product Summary...") }}">{{ $product?->summary ?? "" }}</textarea>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Description") }} </label>
                <textarea class="form--control summernote radius-10" name="description" placeholder="{{ __("Type Description") }}">{!! purify_html($product?->description ?? "") !!}</textarea>
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Included files") }} <sup class="text-primary">{{__('Optional')}}</sup> </label>
                <input type="text" class="form--control radius-10" id="included_file" value="{{ $product?->included_files ?? "" }}" name="included_files" placeholder="{{ __("Write included file names...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Version") }} <sup class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="text" class="form--control radius-10" id="version" value="{{ $product?->version ?? "" }}" name="version" placeholder="{{ __("Write version number...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Release Date") }} <sup class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="date" class="form--control radius-10 flatpickr" id="release_date" value="{{ $product?->release_date ?? "" }}" name="release_date" placeholder="{{ __("Write release date...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Latest Update") }} <sup class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="date" class="form--control radius-10 flatpickr" id="latest_date" value="{{ $product?->update_date ?? "" }}" name="update_date" placeholder="{{ __("Write latest update...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Preview Link") }} <sup class="text-primary">{{__('(Optional)')}}</sup> </label>
                <input type="text" class="form--control radius-10" id="preview_link" value="{{ $product?->preview_link ?? "" }}" name="preview_link" placeholder="{{ __("Write preview link...") }}">
            </div>

            <div class="dashboard-input mt-4">
                <label class="dashboard-label color-light mb-2"> {{ __("Quantity") }} <sup class="text-primary">{{__('(Optional - If applicable)')}}</sup> </label>
                <input type="text" class="form--control radius-10" id="quantity" value="{{ $product?->quantity ?? "" }}" name="quantity" placeholder="{{ __("Write quantity...") }}">
            </div>
        </form>
    </div>
</div>
