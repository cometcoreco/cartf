@php
    $dynamic_style = 'assets/landlord/frontend/css/dynamic-style.css';
@endphp
@if(file_exists($dynamic_style))
    <link rel="stylesheet" href="{{asset($dynamic_style)}}">
@endif
