@php
    $primary_image = get_attachment_image_by_id($data['primary_image']);
    $primary_image = !empty($primary_image) ? $primary_image['img_url'] : '';

    $background_image = get_attachment_image_by_id($data['background_image']);
    $background_image = !empty($background_image) ? $background_image['img_url'] : '';
@endphp

<div class="banner-area banner-seven bg-item-four" data-padding-top="{{$data['padding_top']}}" data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="banner-waves">
        <img src="{{$background_image}}" alt="">
    </div>
    <div class="container-three">
        <div class="banner-contents-wrapper position-relative">
            <div class="banner-contents wow fadeInDown">
                <span class="banner-store fs-30 color-four fw-500"> {{$data['pre_title'] ?? ''}} </span>
                <h2 class="title"> {{$data['title'] ?? ''}} </h2>
                <span class="banner-para fs-18 color-light"> {{$data['description']}} </span>

                @if($data['button_url'] && $data['button_text'])
                    <div class="btn-wrapper margin-top-50">
                        <a href="{{$data['button_url']}}" class="cmn-btn btn-bg-4 radius-0"> {{$data['button_text']}}</a>
                    </div>
                @endif

            </div>
            <div class="banner-right-contents-all wow fadeInUp" data-wow-delay=".3s">
                <div class="banner-images-right">
                    <img src="{{$primary_image}}" alt="">
                </div>
            </div>
        </div>
    </div>
</div>
