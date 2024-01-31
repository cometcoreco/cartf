<?php

namespace Plugins\PageBuilder\Addons\Tenants\Electro\Header;

use Plugins\PageBuilder\Fields\Image;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Fields\Textarea;
use Plugins\PageBuilder\PageBuilderBase;

class Header extends PageBuilderBase
{
    public function preview_image()
    {
        return 'Tenant/Home/home-01-header-01.png';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name' => 'pre_title',
            'label' => __('Pre Title'),
            'value' => $widget_saved_values['pre_title'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);

        $output .= Textarea::get([
            'name' => 'description',
            'label' => __('description'),
            'value' => $widget_saved_values['description'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_text',
            'label' => __('Button Text'),
            'value' => $widget_saved_values['button_text'] ?? null,
        ]);

        $output .= Text::get([
            'name' => 'button_url',
            'label' => __('Button URl'),
            'value' => $widget_saved_values['button_url'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'primary_image',
            'label' => __('Primary Image'),
            'value' => $widget_saved_values['primary_image'] ?? null,
            'dimensions' => __('775x557 | 770x550 px')
        ]);

        $output .= Image::get([
            'name' => 'background_image',
            'label' => __('Background Image'),
            'value' => $widget_saved_values['background_image'] ?? null,
        ]);

        // add padding option
        $output .= $this->padding_fields($widget_saved_values);
        $output .= $this->admin_form_submit_button();
        $output .= $this->admin_form_end();
        $output .= $this->admin_form_after();

        return $output;
    }

    public function frontend_render()
    {
        $pre_title = esc_html($this->setting_item('pre_title'));
        $title = esc_html($this->setting_item('title'));
        $description = esc_html($this->setting_item('description'));
        $button_text = esc_html($this->setting_item('button_text'));
        $button_url = esc_url($this->setting_item('button_url'));

        $primary_image = $this->setting_item('primary_image');
        $background_image = $this->setting_item('background_image');

        $padding_top = esc_html($this->setting_item('padding_top'));
        $padding_bottom = esc_html($this->setting_item('padding_bottom'));

        $data = [
            'pre_title' => $pre_title,
            'title' => $title,
            'description' => $description,
            'button_text' => $button_text,
            'button_url' => $button_url,
            'primary_image' => $primary_image,
            'background_image' => $background_image,
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom
        ];

        return self::renderView('tenant.electro.header.header', $data);
    }

    public function enable(): bool
    {
        return !is_null(tenant());
    }

    public function addon_title()
    {
        return __('Electro: Header');
    }
}
