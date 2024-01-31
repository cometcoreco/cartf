<?php


namespace Plugins\PageBuilder\Fields;


use Plugins\PageBuilder\Helpers\Traits\FieldInstanceHelper;
use Plugins\PageBuilder\PageBuilderField;

class HighlightedText extends PageBuilderField
{
    use FieldInstanceHelper;

    /**
     * render field markup
     * */
    public function render()
    {
        $output = '';
        $output .= $this->field_before();
        $output .= $this->label();
        foreach ($this->args['options'] as $index => $name){
            $placeholder = match ($index) {
                'value' => [__('Enter full text'), 'addon-text-left'],
                'highlight' => [__('Enter highlighted word/text'), 'addon-text-right']
            };
            $output .='<input type="text" value="'.$name.'" name="'.$this->name().'[]'.'" placeholder="'.$placeholder[0].'"  class="'.$this->field_class().' '.$placeholder[1].'"/>';
        }
        $output .= $this->field_after();

        return $output;
    }

//        How to use in addon
//        $output .= HighlightedText::get([
//            'label' => __('test'),
//            'name' => 'testTwo',
//            'options' => [
//            'value' => $widget_saved_values['testTwo'][0] ?? null,
//            'highlight' => $widget_saved_values['testTwo'][1] ?? null
//            ],
//            'value' => $widget_saved_values['testTwo'] ?? null,
//        ]);
}
