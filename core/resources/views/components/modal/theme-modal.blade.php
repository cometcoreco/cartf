<div class="modal fade" data-selected="" id="{{$target}}" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{$title}}</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="modal-success-msg py-2 mb-4">
                        <h3 class="themeName text-center mb-0"></h3>
                    </div>

                    <div class="col-6">
                        <img class="modal-image" src="" alt="">
                    </div>

                    <div class="col-6">
                        <h2></h2>
                        <p></p>

                        @tenant
                        <div class="msg">
                            <small
                                class="text-primary">{{__('You can set theme by demo imported data or you can set with no imported data, If you set only (theme set) then you have to add all the addon for completing your home page from page builder section also set have to set home page from general settings/page settings, if its not previously set by you..!')}}</small>
                        </div>

                        <form class="theme-form">
                            <input type="hidden" class="form-control" id="tenant_default_theme"
                                   value="{{ get_static_option('tenant_default_theme') }}" name="tenant_default_theme">

                            <div class="mt-4">
                                @csrf
                                @php
                                    $options =
                                    [
                                        'set_theme' => __('Set without data'),
                                        'set_theme_with_demo_data' => __('Set theme with demo or old data')
                                    ];
                                @endphp
                                <x-fields.select name="theme_setting_type" class="theme_setting_type"
                                                 title="{{__('Theme setting type')}}">
                                    <option value="">{{__('Select Type')}}</option>
                                    @foreach($options as $index => $option)
                                        <option value="{{$index}}">{{$option}}</option>
                                    @endforeach
                                </x-fields.select>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">{{__('Close')}}</button>
                                <button type="submit"
                                        class="btn btn-primary theme_status_update_button">{{__('Set Default')}}</button>
                            </div>
                        </form>
                        @endtenant

                        @if($user != 'tenant')
                            <a href="javascript:void(0)" class="edit-btn text-capitalize"
                               data-bs-toggle="modal"
                               data-bs-target="#edit-modal"
                               data-id=""
                               data-name=""
                               data-description="">{{__('Edit Details')}}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
