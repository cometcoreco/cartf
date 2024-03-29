@if($errors->any())
    <div class="alert alert-danger search-results-fields">
        <ul class="list-none alert_list_inline">
            <button type="button btn-sm" class="close" data-bs-dismiss="alert">×</button>
            @foreach($errors->all() as $index => $error)
                <li>{{$loop->iteration}}. {{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif
