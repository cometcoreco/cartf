<!DOCTYPE html>
<html lang="ar"
      dir="rtl">
<head>
{{--    <title>{{ $invoice->name }}</title>--}}
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic&family=Noto+Sans+Arabic:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style type="text/css" media="screen">
        * {
            font-family: 'Noto Naskh Arabic', serif;
            font-family: 'Noto Sans Arabic', sans-serif;
            font-size: 25px;
            direction: rtl;
        }
    </style>
</head>

<body>
{{-- Header --}}
{{--@if($invoice->logo)--}}
{{--    <img src="{{ $invoice->getLogo() }}" alt="logo" height="100">--}}
{{--@endif--}}

{{--{{var_dump($name)}}--}}
<h1>{{$name}}</h1>

{{--<table class="table mt-5">--}}
{{--    <tbody>--}}
{{--    <tr>--}}
{{--        <td class="border-0 pl-0" width="70%">--}}
{{--            <h4 class="text-uppercase">--}}
{{--                <strong>{{ $invoice->name }}</strong>--}}
{{--            </h4>--}}
{{--        </td>--}}
{{--        <h4 class="border-0 pl-0">--}}
{{--            @if($invoice->status)--}}
{{--                <h4 class="text-uppercase">--}}
{{--                    <strong>{{ $invoice->status }}</strong>--}}
{{--                </h4>--}}
{{--            @endif--}}
{{--            <p>{{ __('invoices::invoice.serial') }} <strong>{{ $invoice->getSerialNumber() }}</strong></p>--}}
{{--            <p>{{ __('invoices::invoice.date') }}: <strong>{{ $invoice->getDate() }}</strong></p>--}}
{{--        </h4>--}}
{{--    </tr>--}}
{{--    </tbody>--}}
{{--</table>--}}

</body>
</html>
