@extends("tenant.admin.admin-master")

@section("title", __("Tax Class"))

@section("style")

@endsection

@section("content")
    <div class="message-wrapper">
        <x-flash-msg/>
        <x-error-msg/>
    </div>
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h5 class="title">{{ __("Tax Class Options") }}</h5>
            <div class="tax-class-options">
                <button class="btn btn-primary add-tax-option">{{ __("Add") }}</button>
                <button class="btn btn-danger remove-tax-option">{{ __("Delete") }}</button>
                <button class="btn btn-info store-tax-option">{{ __("Update") }}</button>
            </div>
        </div>

        <div class="card-body">
            <ol class="mb-3">
                <li>{{ __("The tax will be applied to all countries if you do not select any") }}</li>
                <li>{{ __('The "Name" and "Priority" field is a required entry for data storage in the database. If the name is not provided, the corresponding field data will not be stored.') }}</li>
            </ol>
            <form id="tax-class-option-form" action="{{ route('tenant.admin.tax-module.tax-class-option', $taxClass->id) }}" method="post">
                @csrf
                <table class="table table-responsive" id="tax-option-table">
                    <thead>
                        <tr>
                            <td class="d-flex justify-content-center align-items-center">
                                <input type="checkbox" name="select-all-text-class-option" id="select-all-text-class-option" class="form-check">
                            </td>
                            <td>* {{ __("Name") }}</td>
                            <td>{{ __("Country") }}</td>
                            <td>{{ __("State") }}</td>
                            <td>{{ __("City") }}</td>
                            <td>{{ __("Postal Code") }}</td>
                            <td>{{ __("Rate (%)") }}</td>
                            <td class="d-none">{{ __("Compound") }}</td>
                            <td>{{ __("Shipping") }}</td>
                            <td>* {{ __("Priority") }}</td>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($taxClass->classOption as $classOption)
                            <x-taxmodule::tax-class-option-row :$countries :$classOption />
                        @endforeach
                    </tbody>
                </table>
            </form>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        // todo:: if user clicked on this button then trigger tax class option form to submit
        $(document).on("click", ".store-tax-option", function (){
            $("#tax-class-option-form").trigger("submit");
        })

        $(document).on("click", "#select-all-text-class-option", function (){
            let isSelected = $(this).is(":checked");

            $(".tax-option-row-check").each(function (){
                if(isSelected){
                    $(this).attr("checked", true);
                }else{
                    $(this).attr("checked", false);
                }
            });
        })

        // todo:: fetch all states according to selected country
        $(document).on("change","#country_id", function (){
            let el = $(this);
            let country_id = el.val();

            // todo:: send request for fetching tax class option data
            send_ajax_request("get", '',"{{ route('tenant.admin.tax-module.country.state.info.ajax') }}?id=" + country_id, () => {}, (data) => {
                el.parent().parent().find("#state_id").html(data);
            }, (errors) => prepare_errors(errors))
        });

        // todo:: fetch all cities according to selected state
        $(document).on("change","#state_id", function (){
            let el = $(this);
            let state_id = el.val();

            // todo:: send request for fetching tax class option data
            send_ajax_request("get", '',"{{ route('tenant.admin.tax-module.state.city.info.ajax') }}?id=" + state_id, () => {}, (data) => {
                el.parent().parent().find("#city_id").html(data);
            }, (errors) => prepare_errors(errors))
        });


        // todo:: this method will add new row on tax class option
        $(document).on("click",".add-tax-option", function (){
            let tr = `<x-taxmodule::tax-class-option-row :$countries />`;

            $('#tax-option-table tbody').append(tr);
        });

        // todo:: this method will remove a row from table tbody
        $(document).on("click",".remove-tax-option", function (){
            // todo:: first need to get all selected tax option first
            $("#tax-option-row-check:checked").each(function (){
                $(this).parent().parent().remove();
            });
        });

        function send_ajax_request(request_type,request_data,url,before_send,success_response,errors){
            $.ajax({
                url: url,
                type: request_type,
                beforeSend: (typeof before_send !== "undefined" && typeof before_send === "function") ? before_send : () => { return ""; } ,
                processData: false,
                contentType: false,
                data: request_data,
                success:  (typeof success_response !== "undefined" && typeof success_response === "function") ? success_response : () => { return ""; },
                error:  (typeof errors !== "undefined" && typeof errors === "function") ? errors : () => { return ""; }
            });
        }

        function prepare_errors(data,form,msgContainer,btn){
            let errors = data.responseJSON;

            if(errors.success !== undefined){
                toastr.error(errors.msg.errorInfo[2]);
                toastr.error(errors.custom_msg);
            }

            $.each(errors.errors,function (index,value){
                toastr.error(value[0]);
            })
        }
    </script>
@endsection
