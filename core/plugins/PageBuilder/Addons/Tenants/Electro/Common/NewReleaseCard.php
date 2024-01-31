<?php

namespace Plugins\PageBuilder\Addons\Tenants\Electro\Common;

use App\Facades\GlobalLanguage;
use App\Helpers\SanitizeInput;
use Plugins\PageBuilder\Fields\HighlightedText;
use Plugins\PageBuilder\Fields\Image;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Select;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\PageBuilder\PageBuilderBase;
use function __;

class NewReleaseCard extends PageBuilderBase
{

    public function preview_image()
    {
        return 'Tenant/common/testimonial-01.png';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= HighlightedText::get([
            'label' => __('Enter Full Title'),
            'name' => 'title',
            'options' => [
                'value' => $widget_saved_values['title'][0] ?? null,
                'highlight' => $widget_saved_values['title'][1] ?? null
            ],
            'value' => $widget_saved_values['title'] ?? null,
        ]);

        $output .= Number::get([
            'label' => __('Enter Price'),
            'name' => 'price',
            'placeholder' => __('Price'),
            'value' => $widget_saved_values['price'] ?? null,
        ]);

        $output .= Text::get([
            'label' => __('Enter Button Text'),
            'name' => 'button_text',
            'placeholder' => __('Button Text'),
            'value' => $widget_saved_values['button_text'] ?? null,
        ]);

        $output .= Text::get([
            'label' => __('Enter Button URL'),
            'name' => 'button_url',
            'placeholder' => __('Button URL'),
            'value' => $widget_saved_values['button_url'] ?? null,
        ]);

        $output .= Image::get([
            'name' => 'image',
            'label' => __('Image'),
            'value' => $widget_saved_values['image'] ?? '',
            'dimensions' => '~1600x570px'
        ]);

        $output .= Image::get([
            'name' => 'background_image',
            'label' => __('Background Image'),
            'value' => $widget_saved_values['background_image'] ?? '',
            'dimensions' => '~3:1'
        ]);

        $output .= Text::get([
            'label' => __('Floating Text'),
            'name' => 'floating_text',
            'placeholder' => __('Floating Text'),
            'value' => $widget_saved_values['floating_text'] ?? null,
            'info' => __('This text will appear on a floating box. use comma(,) after every word')
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
        $padding_top = esc_html($this->setting_item('padding_top'));
        $padding_bottom = esc_html($this->setting_item('padding_bottom'));

        $title = $this->setting_item('title') ?? [];
        $price = esc_html($this->setting_item('price')) ?? '';
        $button_text = esc_html($this->setting_item('button_text')) ?? '';
        $button_url = esc_url($this->setting_item('button_url')) ?? '';
        $floating_text = esc_html($this->setting_item('floating_text')) ?? '';

        $image = $this->setting_item('image') ?? '';
        $background_image = $this->setting_item('background_image') ?? '';

        $data = [
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'title' => $title,
            'price' => $price,
            'button_text' => $button_text,
            'button_url' => $button_url,
            'floating_text' => $floating_text,
            'image' => $image,
            'background_image' => $background_image,
        ];

        return self::renderView('tenant.electro.common.new-release-card', $data);
    }

    public function enable(): bool
    {
        return (bool)!is_null(tenant());
    }

    public function addon_title()
    {
        return __('Electro : New Release Card');
    }
}
