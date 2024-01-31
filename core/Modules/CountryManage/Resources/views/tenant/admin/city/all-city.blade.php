@extends('tenant.admin.admin-master')

@section('title', __('All Cities'))

@section('style')
    <link rel="stylesheet" href="{{global_asset('assets/common/css/select2.min.css')}}">
    <style>
        .swal_delete_button{
            margin-bottom: 0 !important;
        }
        .select2-container {
            display: block;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="customMarkup__single">
                            <div class="customMarkup__single__item">
                                <div class="d-flex justify-content-between">
                                    <div class="customMarkup__single__item__flex">
                                        <h4 class="customMarkup__single__title">{{ __('All Cities') }}</h4>
                                    </div>
                                    <x-btn.add-modal :title="__('Add City')" />
                                </div>
                                <div class="search_delete_wrapper">
                                    <x-bulk-action.bulk-action />
                                    <x-search.search-in-table :id="'string_search'" />
                                </div>
                                <div class="customMarkup__single__inner mt-4">
                                    <!-- Table Start -->
                                    <div class="custom_table style-04 search_result">
                                        @include('countrymanage::tenant.admin.city.search-result')
                                    </div>
                                    <!-- Table End -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('countrymanage::tenant.admin.city.add-modal')
    @include('countrymanage::tenant.admin.city.edit-modal')
@endsection

@section('scripts')
    <script src="{{global_asset('assets/common/js/select2.min.js')}}"></script>
    <x-bulk-action.js :route="route('tenant.admin.city.delete.bulk.action')" />
    @include('countrymanage::tenant.admin.city.city-js')

    <script>
        $(document).on('click','.swal_status_change_button',function(e){
            e.preventDefault();
            Swal.fire({
                title: '{{__("Are you sure?")}}',
                text: '{{__("You would change status any time")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: "{{__('Yes, Change it!')}}"
            }).then((result) => {
                if (result.isConfirmed) {
                    $(this).next().find('.swal_form_submit_btn').trigger('click');
                }
            });
        });
    </script>
@endsection
