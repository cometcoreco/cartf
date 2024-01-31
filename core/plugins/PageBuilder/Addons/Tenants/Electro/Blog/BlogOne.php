<?php

namespace Plugins\PageBuilder\Addons\Tenants\Electro\Blog;

use App\Helpers\SanitizeInput;
use Modules\Blog\Entities\Blog;
use Modules\Blog\Entities\BlogCategory;
use Plugins\PageBuilder\Fields\NiceSelect;
use Plugins\PageBuilder\Fields\Number;
use Plugins\PageBuilder\Fields\Select;
use Plugins\PageBuilder\Fields\Text;
use Plugins\PageBuilder\PageBuilderBase;

class BlogOne extends PageBuilderBase
{

    public function preview_image()
    {
        return 'Tenant/blog/blog-01.png';
    }

    public function admin_render()
    {
        $output = $this->admin_form_before();
        $output .= $this->admin_form_start();
        $output .= $this->default_fields();

        $widget_saved_values = $this->get_settings();

        $output .= Text::get([
            'name' => 'title',
            'label' => __('Section Title'),
            'value' => $widget_saved_values['title'] ?? null,
        ]);

        $blogs = [];
        Blog::where('status', 1)->chunk(50, function ($chunked_blogs) use (&$blogs) {
                foreach ($chunked_blogs as $blog)
                {
                    $blogs[$blog->id] = $blog->title;
                }
        });

        $output .= NiceSelect::get([
            'multiple' => true,
            'name' => 'blogs',
            'label' => __('Select Blogs'),
            'options' => $blogs,
            'value' => $widget_saved_values['blogs'] ?? null,
            'info' => __('you can select your desired blogs or leave it empty')
        ]);

        $output .= Select::get([
            'name' => 'order_by',
            'label' => __('Order By'),
            'options' => [
                'id' => __('ID'),
                'created_at' => __('Date'),
            ],
            'value' => $widget_saved_values['order_by'] ?? null,
            'info' => __('set order by')
        ]);
        $output .= Select::get([
            'name' => 'order',
            'label' => __('Order'),
            'options' => [
                'asc' => __('Accessing'),
                'desc' => __('Decreasing'),
            ],
            'value' => $widget_saved_values['order'] ?? null,
            'info' => __('set order')
        ]);
        $output .= Number::get([
            'name' => 'items',
            'label' => __('Items'),
            'value' => $widget_saved_values['items'] ?? null,
            'info' => __('enter how many item you want to show in frontend'),
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
        $title = esc_html($this->setting_item('title'));

        $blogs_id = $this->setting_item('blogs');
        $order_by = esc_html($this->setting_item('order_by')) ?? 'id';
        $order = esc_html($this->setting_item('order')) ?? 'asc';
        $items = esc_html($this->setting_item('items'));
        $padding_top = esc_html($this->setting_item('padding_top'));
        $padding_bottom = esc_html($this->setting_item('padding_bottom'));

        $blogs = Blog::where('status', 1);
        if(!empty($blogs_id)) {
            $blogs->whereIn('category_id',$blogs_id);
        }

        $blogs =  $blogs->orderBy($order_by ?? 'id', $order ?? 'desc')->take($items ?? 3)->get();


        $data = [
            'title'=> $title,
            'blogs'=> $blogs,
            'padding_top'=> $padding_top,
            'padding_bottom'=> $padding_bottom,
        ];

        return self::renderView('tenant.electro.blog.blog',$data);
    }

    public function enable(): bool
    {
        return (bool) !is_null(tenant());
    }

    public function addon_title()
    {
        return __('Electro: Blogs');
    }
}
