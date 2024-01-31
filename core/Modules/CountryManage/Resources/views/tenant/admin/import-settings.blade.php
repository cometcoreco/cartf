@extends('tenant.admin.admin-master')

@section('title', __('Import Settings'))

@section('style')
    <style>
        li{
            font-size: 16px;
            cursor: pointer;
        }

        ol.level-one{
            list-style-type: none;
        }
        ol.level-one > li{
            background-color: rgb(241, 242, 252);
            border: 1px solid #c1c2cb;
            border-radius: 5px;
            padding: 20px;
            padding-left: 30px;
            margin-bottom: 15px;
        }
        ol.level-one > li:hover{
            border-radius: 5px;
            background-color: rgb(255, 249, 249);
        }

        ol.level-two{
            background-color: rgb(232, 238, 246);
            border: 1px solid #b1b5bb;
            border-radius: 5px;
            padding: 20px;
            padding-left: 30px;
        }
        ol.level-two > li{
            margin-bottom: 15px;
        }
        ol.level-two > li:hover{
            border-radius: 5px;
            background-color: rgb(255, 246, 246);
        }

        ol.level-three{
            background-color: rgb(238, 247, 248);
            border: 1px solid #b7c4c5;
            border-radius: 5px;
            padding: 20px;
            padding-left: 45px;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard__body">
        <div class="row">
            <div class="col-lg-12">
                <x-error-msg/>
                <x-flash-msg/>

                <div class="customMarkup__single">
                    <div class="customMarkup__single__item">
                        <div class="d-flex justify-content-between">
                            <h4 class="customMarkup__single__title text-capitalize">{{ __('Import Countries, States and Cities (Only CSV File)') }}</h4>
                            <a href="{{route('tenant.admin.settings.csv.download.sample')}}" class="btn btn-info btn-sm">{{__('Download Sample File')}}</a>
                        </div>

                        <div class="customMarkup__single__inner mt-4">
                            @if(!isset($import_data))
                                <form action="{{route('tenant.admin.settings.import.csv.update.settings')}}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="#">{{__('File')}}</label>
                                        <input type="file" name="csv_file" accept=".csv" class="form-control" required>
                                        <div class="info-text">{{__('only csv file are allowed with separate by (,) comma.')}}</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary loading-btn">{{__('Submit')}}</button>
                                </form>
                            @else
                                <form action="{{route('tenant.admin.settings.import.database')}}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <ol class="level-one">
                                        @foreach($import_data['country'] ?? [] as $index => $country)
                                            <li>
                                                {{$index.'. '.$country['name']}}
                                                <ol class="level-two">
                                                    @foreach($import_data['state'] ?? [] as $state)
                                                        @continue($state['country_id'] !== $country['id'])

                                                        <li>
                                                            {{$state['name']}}
                                                            <ol class="level-three">
                                                                @foreach($import_data['city'] ?? [] as $city)
                                                                    @continue($city['state_id'] !== $state['id'])

                                                                    <li>{{$city['name']}}</li>
                                                                @endforeach
                                                            </ol>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </li>
                                        @endforeach
                                    </ol>

                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{route('tenant.admin.settings.import.cancel')}}" class="btn btn-danger loading-btn mt-4">{{__('Discard')}}</a>
                                        <button type="submit" class="btn btn-primary loading-btn mt-4">{{__('Import')}}</button>
                                    </div>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
