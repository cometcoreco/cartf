@php
    $classOption = $classOption ?? new stdClass();
@endphp

<tr>
    <td>
        <input value="{{ $classOption->tax ?? "" }}" type="checkbox" class="tax-option-row-check" id="tax-option-row-check"/>
    </td>
    <td>
        <input value="{{ $classOption->tax_name ?? "" }}" type="text" name="tax_name[]" class="form-control" required>
    </td>
    <td>
        <select name="country_id[]" id="country_id" class="form-control">
            <option value="">{{ __("Select Country") }}</option>

            @foreach($countries as $country)
                <option @if(!empty($classOption))
                            {{ $country->id == ($classOption->country_id ?? '') ? "selected" : "" }}
                        @endif value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="state_id[]" id="state_id" class="form-control">
            <option value="">{{ __("Select State") }}</option>
            @foreach($classOption?->states ?? [] as $state)
                <option {{ $state->id == $classOption->state_id ? "selected" : "" }} value="{{ $state->id ?? "" }}">{{ $state->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="city_id[]" id="city_id" class="form-control">
            <option value="">{{ __("Select City") }}</option>
            @foreach($classOption?->cities ?? [] as $city)
                <option {{ $city->id == $classOption->city_id ? "selected" : "" }} value="{{ $city->id ?? "" }}">{{ $city->name }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input value="{{ $classOption->postal_code ?? "" }}" type="text" name="postal_code[]" id="postal_code" class="form-control">
    </td>
    <td>
        <input value="{{ $classOption->rate ?? '0.00' }}" type="number" name="rate[]" step="0.01" id="rate" class="form-control" required
               pattern="[A-Za-z0-9]{5}">
    </td>
    <td class="d-none">
        <input {{ ($classOption->is_compound ?? "") == 1 ? "checked" : "" }} type="checkbox" name="is_compound[]"
               id="compound" value="1"/>
    </td>
    <td>
        <input {{ ($classOption->is_shipping ?? "") == 1 ? "checked" : "" }} type="checkbox" name="is_shipping[]"
               id="shipping" value="1"/>
    </td>
    <td>
        <input value="{{ $classOption->priority ?? "" }}" type="number" name="priority[]" id="priority" class="form-control" required>
    </td>
</tr>
