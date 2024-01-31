@extends("tenant.admin.admin-master")

@section("title", __("Tax Class"))

@section("style")

@endsection

@section("content")
    <div>
        <x-flash-msg/>
        <x-error-msg/>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="title">{{ __("Manage Tax Class") }}</h3>
            <div class="mt-2">
                <small class="text-secondary">{{ __("If a class has any associated options, you can't delete the class from here. You need to delete all options first, or you can use a force delete option.") }}</small>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card-body">
                    <table class="table table-responsive">
                    <thead>
                    <tr>
                        <th>{{ __("SL NO") }}</th>
                        <th>{{ __("Name") }}</th>
                        <th>{{ __("Action") }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($classes as $class)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $class->name }}</td>
                                <td>
                                    <a class="btn btn-info" href="{{ route('tenant.admin.tax-module.tax-class-option', $class->id) }}">{{ __("View") }}</a>
                                    <button data-id="{{ $class->id }}" data-name="{{ $class->name }}" id="updateTaxClassButton" class="btn btn-primary" data-bs-target="#updateTaxClass" data-bs-toggle="modal">{{ __("Edit") }}</button>
                                    <button id="deleteTaxClassButton" data-id="{{ $class->id }}" data-option-count="{{ $class->class_option_count }}" data-href="{{ route("tenant.admin.tax-module.tax-class-delete", $class->id) }}" class="btn btn-danger">{{ __("Delete") }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <div class="col-md-5">
                <div class="card-body border">
                    <h3 class="title">{{ __("Create tax class") }}</h3>
                    <form action="{{ route('tenant.admin.tax-module.tax-class') }}" method="post">
                        @csrf

                        <div class="form-group">
                            <label for="#tax-class-name" class="form-label">{{ __("Name") }}</label>
                            <input name="name" type="text" class="form-control" placeholder="{{ __("Write tax class name") }}"/>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-primary">{{ __("Create") }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateTaxClass" tabindex="-1" aria-labelledby="exampleUpdateTaxClass" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tenant.admin.tax-module.tax-class') }}" method="post">
                    @csrf
                    @method("PUT")
                    <input type="hidden" name="id" value="" id="tax-class-id" class="form-control">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">{{ __("Update tax class") }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="#update-tax-class-name" class="form-label">{{ __("Name") }}</label>
                            <input id="update-tax-class-name" name="name" type="text" class="form-control" placeholder="{{ __("Write tax class name") }}"/>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __("Close") }}</button>
                        <button type="submit" class="btn btn-primary">{{ __("Save changes") }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section("scripts")
    <script>
        $(document).on("click", "#updateTaxClassButton", function (){
            $("#updateTaxClass #tax-class-id").val($(this).attr("data-id"));
            $("#updateTaxClass #update-tax-class-name").val($(this).attr("data-name"));

        })

        $(document).on("click","#deleteTaxClassButton", function (){
            let countOption = $(this).attr("data-option-count");
            let formData = new FormData();
            formData.append("_method", "DELETE");
            formData.append("_token", "{{ csrf_token() }}");
            formData.append("id", $(this).attr("data-id"));

            if(countOption > 0){
                Swal.fire({
                    title: `{{__('Are you sure?')}}`,
                    text: `{{__("if delete this tax class then all tax class option will be deleted and You won't be able to revert those!")}}`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: `{{__('Yes, delete it!')}}`,
                    cancelButtonText: `{{__('Cancel')}}`
                }).then((result) => {
                    if (result.isConfirmed) {
                        send_ajax_request("GET", formData,$(this).data("data-href"), () => {
                            // before send request
                            toastr.warning(`{{__("Request send please wait while")}}`);
                        }, (data) => {
                            Swal.fire(
                                `{{__('Deleted!')}}`,
                                `{{__('Your file has been deleted.')}}`,
                                'success'
                            );

                            $(this).parent().parent().remove();
                        }, (data) => {
                            prepare_errors(data);
                        })
                    }
                });
            }

            Swal.fire({
                title: `{{__('Are you sure?')}}`,
                text: `{!! __("You won't be able to revert this!") !!}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: `{{__('Yes, delete it!')}}`,
                cancelButtonText: `{{__('Cancel')}}`
            }).then((result) => {
                if (result.isConfirmed) {
                    send_ajax_request("post",formData,$(this).data("data-href"), () => {
                        // before send request
                        toastr.warning(`{{__("Request send please wait while")}}`);
                    }, (data) => {
                        Swal.fire(
                            `{{__('Deleted!')}}`,
                            `{{__('Your file has been deleted.')}}`,
                            'success'
                        );

                        $(this).parent().parent().remove();
                    }, (data) => {
                        prepare_errors(data);
                    })
                }
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
