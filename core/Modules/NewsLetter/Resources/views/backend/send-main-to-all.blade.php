@extends(route_prefix().'admin.admin-master')
@section('style')
    <x-summernote.css/>
    <link rel="stylesheet" href="{{asset('assets/admin/css/dropzone.css')}}">
@endsection
@section('title')
    {{__('Send Mail To All Newsletter Subscriber')}}
@endsection
@section('content')
    <div class="col-lg-12 col-ml-12">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="margin-top-40 m-0">
                   <x-error-msg/>
                    <x-flash-msg/>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-4">{{__('Send Mail To All Newsletter Subscriber')}}</h4>
                        <form action="{{route(route_prefix().'admin.newsletter.mail')}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="edit_icon">{{__('Subject')}}</label>
                                <input type="text" class="form-control"  id="subject" name="subject" placeholder="{{__('Subject')}}">
                            </div>
                            <div class="form-group">
                                <label for="message">{{__('Message')}}</label>
                                <input type="hidden" name="message" >
                                <textarea class="summernote" name="message"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-lg btn-primary">{{__('Send Mail')}}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <x-summernote.js/>
    <script src="{{asset('assets/admin/js/summernote-bs4.js')}}"></script>
    <script src="{{asset('assets/admin/js/dropzone.js')}}"></script>
@endsection
