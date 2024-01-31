<style>
    .video-container{
        width: 100vw;
        height: 550px;
        margin-top: 85px;
        z-index: -1;
    }

    iframe {
        width: 100vw;
        height: 500px;

    }
    #text{
        position: absolute;
        color: #FFFFFF;
        left: 50%;
        top: 60%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100vh;
        text-align: center;
        padding-top: 300px;
        background: rgba(0,0,0,0.3);
    }
    #text h1, #text h2{
        color: #FFF !important;
        font-size: 50px;
    }
    #text p {
        color: #FFF;
    }
    @media (min-aspect-ratio: 16/9) {
        .video-container iframe {
            /* height = 100 * (9 / 16) = 56.25 */
            height: 56.25vw;
        }
    }
    @media only screen and (max-width: 480px){
        .video-container {
            width: 100%;
            height: 352px;
            margin-top: 100px;
            z-index: -1;
        }
        iframe {
            width: 100vw;
            height: 352px;
        }
        .video-container iframe {
            width: 100% !important;
        }
        #text {
            position: absolute;
            color: #FFFFFF;
            left: 50%;
            top: 284px;
            transform: translate(-50%, -50%);
            width: 100%;
            height: 282px !important;
            text-align: center;
            padding-top: 51px;
            background: rgba(0,0,0,0.3);
        }
        #text h1, #text h2 {
            color: #FFF !important;
            font-size: 21px !important;
        }
        .hero-banner-area {
            padding-bottom: 10px !important;
        }
    }
    @media (max-aspect-ratio: 16/9) {
        .video-container iframe {
            /* width = 100 / (9 / 16) = 177.777777 */
            width: 177.78vh;
        }
    }
</style>

<section class="hero-banner-area video-section section-bg-1" data-padding-top="{{$data['padding_top']}}"
         data-padding-bottom="{{$data['padding_bottom']}}" id="{{$data['section_id']}}">
    <div class="video-container">
        @php
            $video_autoplay = $data['video_autoplay'] ? 'autoplay=1' : 'autoplay=1';
            $video_mute = $data['video_mute'] ? 'mute=0' : 'mute=1';
            $video_loop = $data['video_loop'] ? 'loop=0' : 'loop=1';
            $video_control = $data['video_control'] ? 'controls=1' : 'controls=0';
        @endphp
        <iframe height="550px" width="100%"
                src="{{$data['video_link'] ?? ''}}?{{$video_autoplay}}&{{$video_mute}}&{{$video_loop}}&{{$video_control}}&rel=0"
                title="{{str_replace(['{h}','{/h}'], '', $data['title']) ?? ''}}"
                allowfullscreen
        ></iframe>
    </div>
    <div id="text">
        @php
            if (str_contains($data['title'], '{h}') && str_contains($data['title'], '{/h}'))
                {
                    $text = explode('{h}',$data['title']);

                    $highlighted_word = explode('{/h}', $text[1])[0];

                    $highlighted_text = '<span class="banner-content-title-shape title-shape">'. $highlighted_word .'</span>';
                    $final_title = '<h1 class="banner-content-title">'.str_replace('{h}'.$highlighted_word.'{/h}', $highlighted_text, $data['title']).'</h1>';
                } else {
                    $final_title = '<h1 class="banner-content-title">'. $data['title'] .'</h1>';
                }
        @endphp
        <h1>
            {!! $final_title !!}
        </h1>
        <p class="section-para"> {{$data['subtitle'] ?? ''}} </p>
            <div class="btn-wrapper mt-4 mt-lg-5">
                <a href="/register" style="z-index: 999" class="cmn-btn cmn-btn-bg-1"> Start Your Free Trial <i class="las la-arrow-right"></i>
                </a>
            </div>
    </div>
</section>

