<section class="choose-area video-section section-bg-1" data-padding-top="{{$data['padding_top']}}"
         data-padding-bottom="{{$data['padding_bottom']}}" id="{{$data['section_id']}}">
    <div class="container">
        <div class="row align-items-center justify-content-center flex-column-reverse flex-lg-row">
            <div class="col-xl-6 mt-4">
                <div class="choose-thumb-content">
                    @php
                        $video_autoplay = $data['video_autoplay'] ? 'autoplay=1' : 'autoplay=0';
                        $video_mute = $data['video_mute'] ? 'mute=0' : 'mute=1';
                        $video_loop = $data['video_loop'] ? 'loop=0' : 'loop=1';
                        $video_control = $data['video_control'] ? 'controls=1' : 'controls=0';
                    @endphp
                    <iframe class="video_iframe"
                            src="{{$data['video_link'] ?? ''}}?{{$video_autoplay}}&{{$video_mute}}&{{$video_loop}}&{{$video_control}}"
                            title="{{str_replace(['{h}','{/h}'], '', $data['title']) ?? ''}}"
                            allowfullscreen
                    ></iframe>
                </div>
            </div>
            <div class="col-xl-6 col-lg-9 mt-4">
                <div class="choose-wrapper">
                    <div class="section-title text-left">
                        {!! get_modified_title($data['title']) !!}
                        <p class="section-para"> {{$data['subtitle'] ?? ''}} </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
