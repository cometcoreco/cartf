@extends(route_prefix().'admin.admin-master')
@section('title')
    {{__('Admin Health')}}
@endsection

@section('style')
    <x-media-upload.css/>
    <x-datatable.css/>

    <style>
        .issue-animation{
            animation: zoom ease-in-out 2s infinite;
        }

        @keyframes zoom {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.25);
            }
        }
    </style>
@endsection

@section('content')
    @php
        $display_errors =  "ini_get method not allowed";
        $memory_limit =  "ini_get method not allowed";
        $post_max_size =  "ini_get method not allowed";
        $max_execution_time =  "ini_get method not allowed";
        $upload_max_filesize =  "ini_get method not allowed";

        $issues = 0;
        if (function_exists('ini_get')){
            // GTR = Greater, EQL = Equal
            $display_errors =  ini_get("display_errors");
            $memory_limit =  ini_get("memory_limit"); //Must be GTR or EQL 512
            $post_max_size =  ini_get("post_max_size"); //Must be GTR or EQL 128
            $max_execution_time =  ini_get("max_execution_time"); //Must be GTR or EQL 300
            $upload_max_filesize =  ini_get("upload_max_filesize"); //Must be GTR or EQL 128

            foreach (
                [
                    [(int)str_replace('M','',$memory_limit), 512],
                    [(int)str_replace('M','',$post_max_size), 128],
                    [(int)$max_execution_time, 300],
                    [(int)str_replace('M','',$upload_max_filesize), 128]
                ] ?? [] as $item)
                {
                    if (current($item) < last($item))
                    {
                        $issues++;
                    }
                }
        }
    @endphp

    <div class="row">
        <div class="col-sm-6 m-auto">
            @if($issues > 0)
                <div class="alert alert-danger">
                    <p><strong class="text-danger">{!! __(number_to_word($issues) . ' ' . ($issues > 1 ? 'issues detected!' : 'issue detected!')) !!}</strong></p>
                    <p>{{ __('If all necessary values are not configured correctly, it may affect the system’s functionality') }}</p>
                </div>
            @endif

            <ul class="list-group">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    PHP version
                    <span class="badge badge-info badge-pill">
                        @php
                            echo "V"." ".phpversion();
                        @endphp
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('MySQL version')}}
                    <span class="badge badge-info badge-pill">
                        @php
                            echo "V"." ". DB::select("SELECT VERSION() as version")[0]->version;
                        @endphp
                    </span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    Laravel version
                    <span class="badge badge-info badge-pill">
                    @php
                        echo "V"." ".app()->version();
                    @endphp
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Database create permission')}}
                    @php
                        $website_has_permission_to_create_database = get_static_option('website_has_permission_to_create_database');
                    @endphp
                    <span
                        class="badge @if($website_has_permission_to_create_database === 'yes') badge-success @else badge-danger @endif  badge-pill">{{$website_has_permission_to_create_database}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Wildcard subdomain')}}
                    @php
                        $website_has_permission_to_create_database = get_static_option('website_has_permission_to_create_database');
                    @endphp
                    <span
                        class="badge @if($website_has_permission_to_create_database == 'yes') badge-success @else badge-danger @endif badge-pill">{{$website_has_permission_to_create_database}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Wildcard SSL')}}
                    @php
                        $website_wildcard_subdomain_working = get_static_option('website_wildcard_subdomain_working');
                    @endphp
                    <span
                        class="badge @if($website_wildcard_subdomain_working == 'yes') badge-success @else badge-danger @endif badge-pill">{{$website_wildcard_subdomain_working}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Cron Job')}}
                    @php
                        $website_cron_job = get_static_option('website_cron_job');
                    @endphp
                    <span
                        class="badge @if($website_cron_job == 'yes') badge-success @else badge-danger @endif badge-pill">{{$website_cron_job}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p> {{__('Memory Limit')}} <small class="d-block">{{__('recommended memory limit is 512MB')}}</small></p>
                    <span class="badge {{str_replace('M','',$memory_limit) >= 512 ? 'badge-success' : 'badge-danger issue-animation'}} badge-pill">{{$memory_limit}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p> {{__('Maximum Execution Time')}} <small
                            class="d-block">{{__('recommended maximum execution time is 300')}}</small></p>

                    <span class="badge {{$max_execution_time >= 300 ? 'badge-success' : 'badge-danger issue-animation'}} badge-pill">{{$max_execution_time}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Display Errors')}}
                    <span
                        class="badge @if($display_errors == 'Off') badge-danger @else badge-success @endif badge-pill">{{$display_errors}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p> {{__('Max File Upload Size')}} <small class="d-block">{{__('recommended post size is 128M')}}</small></p>
                    <span class="badge {{str_replace('M','',$upload_max_filesize) >= 128 ? 'badge-success' : 'badge-danger issue-animation'}} badge-pill">{{$upload_max_filesize}}</span>
                </li>

                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <p> {{__('Post Max Size')}} <small class="d-block">{{__('recommended post size is 128M')}}</small></p>
                    <span class="badge {{str_replace('M','',$post_max_size) >= 128 ? 'badge-success' : 'badge-danger issue-animation'}} badge-pill">{{$post_max_size}}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{__('Database engine')}}
                    <span
                        class="badge badge-info badge-pill">{{\Config::get('database.connections.mysql.engine')}}</span>
                </li>

                <li class="list-group-item d-flex  justify-content-start align-items-center flex-wrap">
                    <p class="d-block mb-3">{{__('Php Extension list')}}</p>
                    @php
                        $colors = ["badge-success",'badge-primary','badge-secondary','badge-danger','badge-warning'];
                    @endphp
                    @foreach(get_loaded_extensions() ?? [] as $ext)
                        <span class="badge badge-secondary badge-pill m-1 extension">{{$ext}}</span>
                    @endforeach
                </li>


            </ul>
        </div>
    </div>
    {{--end --}}
@endsection

@section('scripts')
    <!-- Start datatable js -->
    <x-datatable.js/>
    <x-media-upload.js/>
    <script>
        (function($){
            "use strict";
            $(document).ready(function() {
                $(document).on('click','.user_change_password_btn',function(e){
                    e.preventDefault();
                    var el = $(this);
                    var form = $('#user_password_change_modal_form');
                    form.find('#ch_user_id').val(el.data('id'));
                });
                $('#all_user_table').DataTable( {
                    "order": [[ 0, "desc" ]]
                } );

            } );

        })(jQuery);
    </script>
@endsection
