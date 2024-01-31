<section class="promo-area" data-padding-top="{{$data['padding_top']}}" data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="row margin-top-10">
            @foreach($data['repeater_data']['repeater_title_'] ?? [] as $key => $title)
                <div class="col-xl-3 col-md-6 col-sm-6 col-6 promo-child margin-top-30">
                <div class="single-promo text-center">
                    <div class="icon color-four">
                        <i class="{{$data['repeater_data']['repeater_icon_'][$key] ?? ''}}"></i>
                    </div>

                    <div class="contents">
                        <h4 class="common-title"> <a href="javascript:void(0)"> {{esc_html($title)}} </a> </h4>
                        <p class="common-para"> {{esc_html($data['repeater_data']['repeater_subtitle_'][$key]) ?? ''}} </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
