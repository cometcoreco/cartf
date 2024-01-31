<div class="comingsoon-area" data-padding-top="{{$data['padding_top']}}"
     data-padding-bottom="{{$data['padding_bottom']}}">
    <div class="container-three">
        <div class="row margin-top-10">
            @foreach($data['repeater']['repeater_title_'] ?? [] as $index => $info)
                @php
                    $campaign_id = $data['repeater']['repeater_campaign_'][$index];
                    $campaign = \Modules\Campaign\Entities\Campaign::find($campaign_id);

                    $title = $data['repeater']['repeater_title_'][$index] ?? $campaign?->title;
                    $slogan = $data['repeater']['repeater_slogan_'][$index] ?? $campaign?->subtitle;

                    $image_id = $data['repeater']['repeater_image_'][$index] ?? $campaign?->image;

                    $campaign_url = route('frontend.products.campaign', $campaign_id);
                    $button_text = $data['repeater']['repeater_button_text_'][$index] ?? __('Order Now');
                    $button_url = $data['repeater']['repeater_button_url_'][$index] ?? $campaign_url;
                    $button_target = $data['repeater']['repeater_button_target_'][$index] ? 'target="_blank"' : '';
                @endphp

                <div class="col-lg-6 margin-top-30">
                    <div class="single-coming-soon-ad bg-item-four radius-10">
                        <div class="coming-soon-image-contents">
                            <div class="coming-soon-flex">
                                <div class="coming-soon-contents mt-4 mt-sm-0">
                                    <span class="coming-soon-top color-heading"> {{$title}} </span>
                                    <h2 class="coming-soon-title color-three">
                                        <a href="{{$button_url}}">
                                            {!! get_tenant_highlighted_text($slogan, 'title-small') !!}
                                        </a>
                                    </h2>

                                    <a href="{{$button_url}}" class="preorder-btn mt-4" {{$button_target}}>
                                        <span class="icon">
                                            <i class="las la-arrow-right"></i>
                                        </span>
                                        {{$button_text}}
                                    </a>
                                </div>
                                <div class="coming-soon-img">
                                    {!! render_image_markup_by_attachment_id($image_id) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
