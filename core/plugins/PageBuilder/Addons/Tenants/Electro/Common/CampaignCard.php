<?php

namespace Plugins\PageBuilder\Addons\Tenants\Electro\Common;

use App\Facades\GlobalLanguage;
use App\Helpers\SanitizeInput;
use Modules\Campaign\Entities\Campaign;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Repeater;
use Plugins\PageBuilder\Fields\Select;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\Helpers\RepeaterField;
use Plugins\PageBuilder\PageBuilderBase;
use function __;

class CampaignCard extends PageBuilderBase
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

        $campaign = [];
        Campaign::where('status', 'publish')->chunk(50, function ($chunked_campaign) use (&$campaign) {
            foreach ($chunked_campaign as $item)
            {
                $campaign[$item->id] = $item->title;
            }
        });

        //repeater
        $output .= Repeater::get([
            'multi_lang' => false,
            'settings' => $widget_saved_values,
            'id' => 'repeater_data',
            'fields' => [
                [
                    'type' => RepeaterField::SELECT,
                    'name' => 'repeater_campaign',
                    'label' => __('Select Campaign'),
                    'options' => $campaign,
                    'value' => $widget_saved_values['repeater_campaign'] ?? null,
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'repeater_slogan',
                    'label' => __('Campaign Slogan').' '.'<sup class="text-primary">'.__('Optional').'</sup>',
                    'info' => '<p class="mt-2">'.__('To highlight a text use {h}{/h}. eg, Product {h}Collection{/h}').'</p>'
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'repeater_title',
                    'label' => __('Campaign Title').' '.'<sup class="text-primary">'.__('Optional').'</sup>',
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'repeater_button_text',
                    'label' => __('Button Text').' '.'<sup class="text-primary">'.__('Optional').'</sup>'
                ],
                [
                    'type' => RepeaterField::TEXT,
                    'name' => 'repeater_button_url',
                    'label' => __('Button URL').' '.'<sup class="text-primary">'.__('Optional').'</sup>'
                ],
                [
                    'type' => RepeaterField::SWITCHER,
                    'name' => 'repeater_button_target',
                    'label' => __('Button Target'),
                    'info' => __('keep on if you want to open the link in a different tab')
                ],
                [
                    'type' => RepeaterField::IMAGE,
                    'name' => 'repeater_image',
                    'label' => __('Image').' '.'<sup class="text-primary">'.__('Optional').'</sup>',
                    'dimensions'=> '320x287 | 320x290 px'
                ]
            ]
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

        $repeater = $this->setting_item('repeater_data') ?? '';

        $data = [
            'padding_top' => $padding_top,
            'padding_bottom' => $padding_bottom,
            'repeater' => $repeater,
        ];

        return self::renderView('tenant.electro.common.campaign-card', $data);
    }

    public function enable(): bool
    {
        return (bool)!is_null(tenant());
    }

    public function addon_title()
    {
        return __('Electro : Campaign Card');
    }
}
