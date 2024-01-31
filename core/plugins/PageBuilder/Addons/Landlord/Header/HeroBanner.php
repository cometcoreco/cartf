<?php

namespace Plugins\PageBuilder\Addons\Landlord\Header;

use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\Switcher;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class HeroBanner extends PageBuilderBase
{
    public function preview_image()
    {
        return 'Landlord/common/faq.png';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();
            $output .= Text::get([
                'name' => 'title',
                'label' => __('Title'),
                'value' => $widget_saved_values['title'] ?? null,
                'info' => __('To show the highlighted text, place your word between this code {h}YourText{/h]')
            ]);

            $output .= Text::get([
                'name' => 'subtitle',
                'label' => __('Subtitle'),
                'value' => $widget_saved_values['subtitle'] ?? null,
            ]);

            $output .= Text::get([
                'name' => 'video_link',
                'label' => __('Video Link'),
                'value' => $widget_saved_values['video_link'] ?? '',
            ]);

            $output .= Switcher::get([
                'name' => 'video_autoplay',
                'label' => __('Video Autoplay'),
                'value' => $widget_saved_values['video_autoplay'] ?? '',
            ]);

            $output .= Switcher::get([
                'name' => 'video_mute',
                'label' => __('Video Mute'),
                'value' => $widget_saved_values['video_mute'] ?? '',
            ]);

            $output .= Switcher::get([
                'name' => 'video_loop',
                'label' => __('Video Loop'),
                'value' => $widget_saved_values['video_loop'] ?? '',
            ]);

            $output .= Switcher::get([
                'name' => 'video_control',
                'label' => __('Video Control'),
                'value' => $widget_saved_values['video_control'] ?? '',
            ]);


        // add padding option
        $output .= $this->section_id_and_class_fields($widget_saved_values);
        $output .= $this->padding_fields($widget_saved_values);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $title = esc_html($this->setting_item('title')) ?? '';
        $subtitle = esc_html($this->setting_item('subtitle')) ?? '';

        $video_link = $this->setting_item('video_link') ?? '';
        $video_autoplay = $this->setting_item('video_autoplay') ?? '';
        $video_mute = $this->setting_item('video_mute') ?? '';
        $video_loop = $this->setting_item('video_loop') ?? '';
        $video_control = $this->setting_item('video_control') ?? '';

        $padding_top = esc_html($this->setting_item('padding_top'));
        $padding_bottom = esc_html($this->setting_item('padding_bottom'));

        $section_id = $this->setting_item('section_id') ?? '';

        $data = [
            'title' => $title,
            'subtitle' => $subtitle,
            'video_link' => $this->getYoutubeEmbedUrl($video_link),
            'video_autoplay' => $video_autoplay,
            'video_mute' => $video_mute,
            'video_loop' => $video_loop,
            'video_control' => $video_control,
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'section_id'=> $section_id,
        ];

        return self::renderView('landlord.addons.header.hero-banner', $data);
    }

    function getYoutubeEmbedUrl($url)
    {
        $youtube_id = '';
        $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
        $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';

        if (preg_match($longUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        if (preg_match($shortUrlRegex, $url, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }

        return 'https://www.youtube.com/embed/' . $youtube_id ;
    }

    public function enable(): bool
    {
        return (bool) is_null(tenant());
    }

    public function addon_title()
    {
        return __('Hero Banner: 01');
    }
}
