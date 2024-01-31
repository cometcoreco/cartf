<section class="release-area position-relative" data-padding-top="{{$data['padding_top']}}"
         data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="release-wrapper bg-item-four">
            <div class="release-contents">
                <h2 class="release-title">
                    {!! highlighted_text($data['title'] ?? [], 'release-top color-four') !!}
                </h2>

                <span class="release-price ff-rubik color-four margin-top-30"> {{amount_with_currency_symbol($data['price'] ?? '')}} </span>

                @if(!empty($data['button_url']) && !empty($data['button_text']))
                    <div class="btn-wrapper margin-top-40">
                        <a href="{{$data['button_url']}}" class="cmn-btn btn-bg-4 radius-0"> {{$data['button_text']}} </a>
                    </div>
                @endif
            </div>

            <div class="release-image wow fadeInUp" data-wow-delay=".3s">
                {!! render_image_markup_by_attachment_id($data['image']) !!}
            </div>

            <div class="release-shapes">
                {!! render_image_markup_by_attachment_id($data['background_image']) !!}
            </div>

            @if(!empty($data['floating_text']))
                @php
                    $tags = str_replace(',', '<br>',$data['floating_text']);
                @endphp
                <span class="sale-offer ff-rubik style-02 bg-color-four"> {!! $tags !!} </span>
            @endif
        </div>
    </div>
</section>
