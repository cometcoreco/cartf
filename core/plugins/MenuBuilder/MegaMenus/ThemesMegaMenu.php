<?php

namespace Plugins\MenuBuilder\MegaMenus;

use Plugins\MenuBuilder\MegaMenuBase;

class ThemesMegaMenu extends MegaMenuBase
{

    function name()
    {
        // TODO: Implement name() method.
        return 'Themes';
    }

    function model(){
        return 'App\Models\Page';
    }

    function render($id, $lang, $settings)
    {
        // TODO: Implement render() method.
        $output = '';
        $output .= $this->body_start();

        $output .= view('menubuilder::themes', compact('id'))->render();

        $output .= $this->body_end();
        // TODO: return all makrup data for render it to frontend
        return $output;
    }

    function slug()
    {
        // TODO: Implement slug() method.
    }

    function query_type()
    {
        // TODO: Implement query_type() method.
    }

    function enable()
    {
        // TODO: Implement enable() method.
        return true;
    }

    function title_param()
    {
        // TODO: Implement title_param() method.
    }

    function category($id = null)
    {
        return __('Themes');
    }
}
