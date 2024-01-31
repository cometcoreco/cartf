<x-flash-msg/>
<x-error-msg/>
<table class="DataTable_activation table table-responsive bg-light">
    <thead>
    <tr>
        <th class="no-sort">
            <div class="mark-all-checkbox">
                <input type="checkbox" class="all-checkbox">
            </div>
        </th>
        <th>{{__('ID')}}</th>
        <th>{{__('City')}}</th>
        <th>{{__('State')}}</th>
        <th>{{__('Country')}}</th>
        <th>{{__('Status')}}</th>
        <th>{{__('Action')}}</th>
    </tr>
    </thead>
    <tbody>
    @forelse($all_cities as $city)
        <tr>
            <td>
                <x-bulk-delete-checkbox :id="$city->id"/>
            </td>
            <td>{{ $city->id }}</td>
            <td>{{ $city->name }}</td>
            <td>{{ $city->state?->name }}</td>
            <td>{{ $city->country?->name }}</td>
            <td>
                <x-status-span :status="$city->status"/>
            </td>
            <td>
                <a class="btn btn-info btn-sm edit_city_modal"
                   data-bs-toggle="modal"
                   data-bs-target="#editCityModal"
                   data-city="{{ $city->name }}"
                   data-city_id="{{ $city->id }}"
                   data-state_id="{{ $city->state_id }}"
                   data-country_id="{{ $city->country_id }}">
                    <i class="mdi mdi-pencil"></i>
                </a>

                <x-status-change :title="__('Change Status')" :url="route('tenant.admin.city.status',$city->id)"/>
                <x-delete-popover :title="__('Delete City')" :url="route('tenant.admin.city.delete',$city->id)"/>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">{{__('No Data Available')}}</td>
        </tr>
    @endforelse
    </tbody>
</table>

<div class="custom_pagination">
    {{ $all_cities->links() }}
</div>
