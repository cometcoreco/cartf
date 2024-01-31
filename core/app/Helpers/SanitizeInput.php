<?php


namespace App\Helpers;


use DOMDocument;
use Mews\Purifier\Facades\Purifier;

class SanitizeInput
{
    /**
     * sanitize string to remove script
     * */
    public static function esc_html($val): string
    {
        return htmlspecialchars(strip_tags($val));
    }

    /**
     * sanitize url to remove script
     * */
    public static function esc_url($val): string
    {
        return htmlspecialchars(filter_var($val, FILTER_SANITIZE_URL));
    }

    public static function esc_javascript($content)
    {
        // Remove script tags and their content
        $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

        // Remove potentially dangerous tags
        $content = preg_replace('/<\/?(frame|embed|object|applet|meta|script)[^>]*>/i', '', $content);

        // Remove potentially dangerous attributes
        $dangerousAttributes = [
            'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover',
            'onmouseup', 'onkeydown', 'onkeypress', 'onkeyup', 'onclick',
            'ondblclick', 'oncontextmenu', 'onwheel', 'onmouseleave',
            'onmouseenter', 'onload', 'onerror', 'onbeforeunload',
            'onresize', 'onscroll', 'javascript', 'vbscript', 'data'
        ];

        foreach ($dangerousAttributes as $attr) {
            $content = preg_replace('/'.$attr.'=[\'"]?[^\'" >]+[\'" >]/i', '', $content);
            $content = preg_replace("/".$attr.'=[\'"]?[^\'" >]+[\'" >]/i', '', $content);
        }

        // Optionally, use DOMDocument for further sanitization
        $dom = new DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Loop through each element and remove attributes that might be dangerous
        foreach ($dom->getElementsByTagName('*') as $node) {
            foreach ($dangerousAttributes as $attr) {
                $node->removeAttribute($attr);
            }
        }

        return $dom->saveHTML();
    }

    /**kses_basic
     * kses will allow given html tag with attribute
     * */
    public static function kses($val, array $args): string
    {
        return strip_tags($val, $args);
    }

    /**
     * kses will allow given html tag with attribute
     * */
    public static function kses_basic($val): string
    {
        return Purifier::clean($val);
    }

}
