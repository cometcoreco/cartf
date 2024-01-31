@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Storage Settings')}}
@endsection
@section('style')
    <x-media-upload.css/>

    <style>
        .credentials{
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <x-admin.header-wrapper>
                    <x-slot name="left">
                        <h4 class="card-title mb-5">{{__('Storage Settings')}}</h4>
                    </x-slot>
                    <x-slot name="right" class="d-flex">
                        <form action="{{route(route_prefix().'admin.general.storage.settings')}}" method="post">
                            @csrf
                            <input type="hidden" name="_action" value="sync_file">
                            <button class="btn btn-info btn-sm"
                                    type="submit">{{__('Sync Local File To Cloud')}}</button>
                        </form>
                    </x-slot>
                </x-admin.header-wrapper>

                <x-error-msg/>
                <x-flash-msg/>
                <form class="forms-sample" method="post"
                      action="{{route(route_prefix().'admin.general.storage.settings')}}">
                    @csrf
                    <x-fields.select name="storage_driver" id="storage-driver" title="{{__('Disks Driver')}}"
                                     info="{{__('By default it is local, if you have disk driver you can set here, unless leave this as (Local)')}}">
                        <option
                            value="LandlordMediaUploader" {{ get_static_option_central('storage_driver') == 'LandlordMediaUploader' ? 'selected' : '' }}>{{__('Local')}}</option>
                        <option
                            value="cloudFlareR2" {{ get_static_option_central('storage_driver') == 'cloudFlareR2' ? 'selected' : '' }}>{{__('cloud Flare R2')}}</option>
                        <option
                            value="wasabi" {{ get_static_option_central('storage_driver') == 'wasabi' ? 'selected' : '' }}>{{__('Wasabi s3')}}</option>
                        <option
                            value="s3" {{ get_static_option_central('storage_driver') == 's3' ? 'selected' : '' }}>{{__('Aws s3')}}</option>
                    </x-fields.select>


                    <div class="credentials_wrapper mt-5">
                        <div class="credentials wasabi" @style(['display:block' => get_static_option_central('storage_driver') == 'wasabi'])>
                            <div class="form-group">
                                <label for="">{{__('WAS Access Key ID')}}</label>
                                <input class="form-control" type="text" name="wasabi_access_key_id" value="{{get_static_option_central('wasabi_access_key_id')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('WAS Secret Access Key')}}</label>
                                <input class="form-control" type="text" name="wasabi_secret_access_key" value="{{get_static_option_central('wasabi_secret_access_key')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('WAS Default Region')}}</label>
                                <input class="form-control" type="text" name="wasabi_default_region" value="{{get_static_option_central('wasabi_default_region')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('WAS Bucket')}}</label>
                                <input class="form-control" type="text" name="wasabi_bucket" value="{{get_static_option_central('wasabi_bucket')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('WAS ENDPOINT')}}</label>
                                <input class="form-control" type="text" name="wasabi_endpoint" value="{{get_static_option_central('wasabi_endpoint')}}">
                            </div>
                        </div>

                        <div class="credentials cloudFlareR2" @style(['display:block' => get_static_option_central('storage_driver') == 'cloudFlareR2'])>
                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 Access Key ID')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_access_key_id" value="{{get_static_option_central('cloudflare_r2_access_key_id')}}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 Secret Access Key')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_secret_access_key" value="{{get_static_option_central('cloudflare_r2_secret_access_key')}}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 Bucket')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_bucket" value="{{get_static_option_central('cloudflare_r2_bucket')}}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 URL')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_url" value="{{get_static_option_central('cloudflare_r2_url')}}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 Endpoint')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_endpoint" value="{{get_static_option_central('cloudflare_r2_endpoint')}}">
                            </div>

                            <div class="form-group">
                                <label for="">{{__('Cloudflare R2 Use Path Style Endpoint')}}</label>
                                <input class="form-control" type="text" name="cloudflare_r2_use_path_style_endpoint" value="{{get_static_option_central('cloudflare_r2_use_path_style_endpoint')}}">
                            </div>
                        </div>

                        <div class="credentials s3" @style(['display:block' => get_static_option_central('storage_driver') == 's3'])>
                            <div class="form-group">
                                <label for="">{{__('AWS Access Key ID')}}</label>
                                <input class="form-control" type="text" name="aws_access_key_id" value="{{get_static_option_central('aws_access_key_id')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS Secret Access Key')}}</label>
                                <input class="form-control" type="text" name="aws_secret_access_key" value="{{get_static_option_central('aws_secret_access_key')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS Default Region')}}</label>
                                <input class="form-control" type="text" name="aws_default_region" value="{{get_static_option_central('aws_default_region')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS Bucket')}}</label>
                                <input class="form-control" type="text" name="aws_bucket" value="{{get_static_option_central('aws_bucket')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS URL')}}</label>
                                <input class="form-control" type="text" name="aws_url" value="{{get_static_option_central('aws_url')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS Endpoint')}}</label>
                                <input class="form-control" type="text" name="aws_endpoint" value="{{get_static_option_central('aws_endpoint')}}">
                            </div>
                            <div class="form-group">
                                <label for="">{{__('AWS Use Path Style Endpoint')}}</label>
                                <input class="form-control" type="text" name="aws_use_path_style_endpoint" value="{{get_static_option_central('aws_use_path_style_endpoint')}}">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary me-2">{{__('Save Changes')}}</button>
                </form>
            </div>
        </div>
    </div>
    <x-media-upload.markup/>
@endsection
@section('scripts')
    <x-media-upload.js/>

    <script>
        $(document).ready(function () {
            $(document).on('change', '#storage-driver', function () {
                let driver = $(this).val();

                $('.credentials').hide();
                $(`.${driver}`).fadeIn();
            });
        });
    </script>
@endsection
