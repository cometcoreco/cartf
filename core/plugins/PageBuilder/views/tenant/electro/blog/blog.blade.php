<section class="blog-area position-relative" data-padding-top="{{$data['padding_top']}}" data-padding-bottom="{{$data['padding_bottom']}}">>
    <div class="container-three">
        <div class="row">
            <div class="col-lg-12">
                <div class="section-title justify-content-center">
                    <h2 class="title"> {{$data['title'] ?? ''}} </h2>
                </div>
            </div>
        </div>
        <div class="row margin-top-10 padding-top-10">
            @foreach($data['blogs'] ?? [] as $blog)
                @php
                    $class_delay = $loop->odd ? ['fadeInUp', '.1s'] : ['fadeInDown', '.2s'];

                    $image_markup = \App\Facades\ImageRenderFacade::getParent($blog->image)
                            ->getChild(tenant_blog_single_route($blog->slug), 'blog-image')
                            ->getGrandchild('radius-0')
                            ->renderAll();

                    $category_name = $blog?->category?->title;
                    $category_slug = $blog?->category?->slug;
                @endphp

                <div class="col-lg-3 col-md-4 col-sm-6 col-6 margin-top-30 wow {{current($class_delay)}}" data-wow-delay="{{last($class_delay)}}">
                    <div class="single-blog">
                        {!! $image_markup !!}

                        <div class="contents">
                            <span class="category color-four">
                                <a href="{{tenant_blog_category_route($category_slug)}}"> {{$category_name}} </a>
                            </span>

                            <h3 class="blog-grid-title hover-color-four">
                                <a href="{{tenant_blog_single_route($blog->slug)}}"> {{blog_limited_text($blog->title)}} </a>
                            </h3>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
