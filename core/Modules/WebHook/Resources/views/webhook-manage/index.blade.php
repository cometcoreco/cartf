@extends('landlord.admin.admin-master')
@section('title')
    {{ __('All Web Hooks') }}
@endsection

@section('style')
    <style>
        .wehbook--event-wrapper {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
        }

        .wehbook--event-wrapper label {
            font-size: 12px;
            line-height: 16px;
            font-weight: 600;
        }

        .wehbook--event-wrapper .switch {
            width: 40px;
            height: 20px;
        }

        .wehbook--event-wrappe input:checked + .onff.slider:before{
            content: "";
        }

        .wehbook--event-wrapper .slider:before {
            width: 20px;
            height: 12px;
            font-size: 0;
        }

        .wehbook--event-wrapper input:checked + .slider:before {
            transform: translateX(10px);
            font-size: 0;
        }

        .wehbook--event-wrapper input:checked + .onff.slider:before {
            content: "";
            font-size: 0;
        }

        .wehbook--event-wrapper input:checked + .slider {
            background: #379d11;
        }
    </style>
@endsection
@section('content')
    <div class="dashboard-recent-order card p-3">
        <div class="row">

            <div class="col-md-12">
                <x-flash-msg/>
                <x-error-msg/>

                <div class="recent-order-wrapper dashboard-table bg-white padding-30">
                    <x-admin.header-wrapper>
                        <x-slot name="left">
                            <h4 class="card-title mb-4">{{__('All WebHooks')}}</h4>
                            <p>{{__("you can configure webhook based on various events happened into the websites")}}</p>
                        </x-slot>
                        <x-slot name="right" class="d-flex">
                            <p></p>
                            <button class="btn btn-info btn-sm mb-3" data-bs-toggle="modal" data-bs-target="#new_webhook">{{__('Add New Web Hook')}}</button>
                        </x-slot>
                    </x-admin.header-wrapper>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="table-wrap table-responsive">
                    <table class="table-striped table">
                        <thead>
                            <th>{{__("id")}}</th>
                            <th>{{__("Name")}}</th>
                            <th>{{__("Type")}}</th>
                            <th>{{__("Events")}}</th>
                            <th>{{__("Action")}}</th>
                        </thead>
                        <tbody>
                        @foreach($all_webhook as $webk)
                            <tr>
                                <td>{{$webk->id}}</td>
                                <td>{{$webk->name}}</td>
                                <td>{{$webk->method_type}}</td>
                                <td>{{implode(', ',$webk->events->pluck("event_name")->toArray())}}</td>
                                <td>
                                    <a data-action="{{route('webhook.update',$webk->id)}}"
                                       data-bs-toggle="modal"
                                       data-bs-target="#edit_webhook"
                                       data-settings="{{json_encode(['id' => $webk->id,'url' => $webk->url,'name' => $webk->name,'method_type' => $webk->method_type,'events' => $webk->events->pluck("event_name")->toArray()])}}"
                                       href="#"
                                       class="btn btn-info btn-sm mb-3 mr-1 btn_webhook_edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </a>
                                    <x-delete-popover permissions="webhook-delete" :method="'DELETE'" :url="route('webhook.destroy',$webk->id)"/>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="new_webhook" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{__('New Web Hook')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route("webhook.store")}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        @csrf
                        <x-fields.input name="name" label="{{__('Name')}}" />
                        <x-fields.input type="url" name="url" label="{{__('URL')}}" />
                        <x-fields.select name="method_type" title="{{__('Method')}}">
                            <option value="GET">{{__('GET')}}</option>
                            <option value="POST">{{__('POST')}}</option>
                        </x-fields.select>
                        <div class="wehbook--event-wrapper">
                            @if(is_null(tenant()))
{{--                                <x-fields.switcher name="event[]" setValue="subscription:create" label="Subscription:Create"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:update" label="Subscription:Update"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:cancel" label="Subscription:Cancel"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:delete" label="Subscription:Delete"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="wallet:deposit" label="Wallet:Deposit"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="wallet:deduct" label="Wallet:Deduct"/>--}}
                                <x-fields.switcher name="event[]" setValue="user:register" label="User:Register"/>
                                <x-fields.switcher name="event[]" setValue="user:login" label="User:Login"/>
                                <x-fields.switcher name="event[]" setValue="user:delete" label="User:Delete"/>
                            @endif
                        </div>


                        <x-fields.select name="status" title="{{__('Status')}}">
                            <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                            <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                        </x-fields.select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_webhook" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">{{__('Edit Webhook')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <x-fields.input name="name" label="{{__('Name')}}" />
                        <x-fields.input type="url" name="url" label="{{__('URL')}}" />
                        <x-fields.select name="method_type" title="{{__('Method')}}">
                            <option value="GET">{{__('GET')}}</option>
                            <option value="POST">{{__('POST')}}</option>
                        </x-fields.select>
                        <div class="wehbook--event-wrapper">
                            @if(is_null(tenant()))
{{--                                <x-fields.switcher name="event[]" setValue="subscription:create" label="Subscription:Create"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:update" label="Subscription:Update"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:cancel" label="Subscription:Cancel"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="subscription:delete" label="Subscription:Delete"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="wallet:deposit" label="Wallet:Deposit"/>--}}
{{--                                <x-fields.switcher name="event[]" setValue="wallet:deduct" label="Wallet:Deduct"/>--}}
                                <x-fields.switcher name="event[]" setValue="user:register" label="User:Register"/>
                                <x-fields.switcher name="event[]" setValue="user:login" label="User:Login"/>
                                <x-fields.switcher name="event[]" setValue="user:delete" label="User:Delete"/>
                            @endif
                        </div>


                        <x-fields.select name="status" title="{{__('Status')}}">
                            <option value="{{\App\Enums\StatusEnums::PUBLISH}}">{{__('Publish')}}</option>
                            <option value="{{\App\Enums\StatusEnums::DRAFT}}">{{__('Draft')}}</option>
                        </x-fields.select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary">{{__('Save Changes')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        (function ($){
            "use strict";



            /**
            * handle plugin delete option
            * */
            $(document).on("click",".btn_webhook_edit",function(e){
                e.preventDefault();
               let modalContainer = $('#edit_webhook');
               var el = $(this);
               let allData = el.data();
               let allSettings = el.data('settings');
                modalContainer.find('form').attr('action',allData.action);
                modalContainer.find('input[name="name"]').val(allSettings.name);
                modalContainer.find('input[name="url"]').val(allSettings.url);
                modalContainer.find('select[name="method_type"] option').attr('selected',false);
                modalContainer.find('select[name="method_type"] option[value="'+allSettings.method_type+'"]').attr('selected',true);

                let allEvents = modalContainer.find('input[name="event[]"]');
                $.each(allEvents,function (index,item){
                        item.removeAttribute('checked');
                    if(allSettings.events.includes(item.getAttribute('value'))){
                        item.setAttribute('checked',true);
                    }
                });

            });

        })(jQuery);
    </script>
@endsection
