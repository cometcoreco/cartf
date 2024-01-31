<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{__('Request Call Back Mail')}}</title>
    <style>
        .mail-container {
            max-width: 650px;
            margin: 0 auto;
            text-align: center;
            background-color: #fffefe;
            border: 1px solid #e5e5e5;
            border-radius: 5px;
        }

        .mail-container .logo-wrapper {
            padding: 30px 15px;
        }
        .mail-container .message-box {
            padding: 30px;
        }

        table {
            margin: 0 auto;
        }

        table {
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        table td, table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #111d5c;
            color: white;
        }

        footer {
            font-size: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        footer p{
            font-size: 15px;
            color: #ffffff;
        }
        footer p a{
            color: #ffffff;
        }

        .mail-container .message-box{
            text-align: left;
            margin: 40px 0;
        }
    </style>
</head>
<body>
<div class="mail-container">
    <div class="logo-wrapper">
        <a href="{{url('/')}}">
            @php
                $site_logo = get_attachment_image_by_id(get_static_option('site_white_logo'),"full",false);
            @endphp
            @if (!empty($site_logo))
                <img width="250px" src="{{$site_logo['img_url']}}" alt="{{get_static_option('site_title')}}">
            @endif
        </a>
    </div>
    <div class="message-box">
        @php
            $uid = $data['uid'];
            $message = $data['message'];
            $message = str_replace('@username',' ',$message);
            $message = str_replace('@message',$data['message'],$message);
            $message = str_replace('@company',get_static_option('site_title'),$message);
        @endphp

        {!! $message !!}
    </div>

    <footer style="background-color: #0b0b0b;color: #ffffff">
        <p><a href="{{route(route_prefix('admin.newsletter.unsubscribe'), $uid)}}">{{__('Unsubscribe')}}</a> {{__('from this type of email')}}</p>
        <p>{!! '&copy;'.__('All Right Reserved By' .' '. get_static_option('site_title')) !!}</p>
    </footer>
</div>
</body>
</html>
