<!-- State Edit Modal -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">{{ __('Add City') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('tenant.admin.city.all')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="country" class="label-title mt-3">{{ __('Select Country') }}</label>
                        <select name="country" id="country" class="form-control select2-country">
                            <option value="">{{ __('Select Country') }}</option>
                            @foreach($all_countries as $data)
                                <option value="{{ $data->id }}">{{ $data->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="state" class="label-title mt-3">{{ __('Select State') }}</label>
                        <select name="state" id="state" class="form-control get_country_state select2-state w-100">
                            <option value="">{{ __('Select State') }}</option>
                        </select>
                        <p class="info_msg"></p>
                    </div>

                    <div class="form-group">
                        <label for="city">{{__('City')}}</label>
                        <input type="text" name="city" id="city" class="form-control" placeholder="{{__('Enter city name')}}">
                    </div>

                    <select name="status" id="status" class="form-control">
                        <option value="publish">{{__('Active')}}</option>
                        <option value="draft">{{__('Inactive')}}</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary mt-4" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="submit" class="btn btn-primary mt-4 pr-4 pl-4 add_city">{{ __('Save') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
