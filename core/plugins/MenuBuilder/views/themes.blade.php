@php
    $ids = explode(',', $id);
    $pages = \App\Models\Page::whereIn('id', $ids)->get();
@endphp
<div class="col-lg-3 col-md-4">
    <div class="xg-mega-menu-single-column-wrap">
        <ul>
            @foreach($pages as $page)
                <li>
                    <a href="{{$page->slug}}">{{$page->title}}</a>
                </li>
            @endforeach
        </ul>
    </div>
</div>

<div class="col-lg-9 col-md-8">
    <div class="xg-mega-menu-single-column-wrap">
        <h4 class="mega-menu-title">{{__('Themes')}}</h4>
        <div class="theme_wrapper">
            <div class="row g-4 mt-1">
                @foreach(getAllThemeDataForAdmin() as $theme)
                    @php
                        $theme_slug = $theme->slug;
                        $theme_data = getIndividualThemeDetails($theme_slug);
                        $theme_name = $theme_data['name'];
                        $theme_image = loadScreenshot($theme_slug);
                    @endphp
                    <div class="col-lg-3">
                        @php
                            $theme_name = get_static_option_central($theme_data['slug'].'_theme_name') ?? $theme_name;
                            $theme_url = get_static_option_central($theme_data['slug'].'_theme_url') ?? $theme_slug;
                            $custom_theme_image = get_static_option_central($theme_data['slug'].'_theme_image') ?? $theme_image;
                        @endphp
                        <div class="themePreview">
                            <div class="themePreview_single">
                                <div class="themePreview_thumb">
                                    <img src="{{$theme_image}}" alt="{{$theme_name}}">
                                </div>
                                <div class="themePreview_contents">
                                    <h5 class="themePreview_contents_title">{{$theme_name}}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
