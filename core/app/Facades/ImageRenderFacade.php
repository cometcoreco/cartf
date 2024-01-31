<?php

namespace App\Facades;

use App\Http\Services\RenderImageMarkupService;
use Illuminate\Support\Facades\Facade;

/**
 * @see RenderImageMarkupService
 *
 * @method static RenderImageMarkupService getParent($parent_image, $parent_class = '', $parent_style = '')
 * @param string $parent_image The parent image, specified either as a path or an Image object.
 * @param string $parent_class Optional. The CSS class for the parent image.
 * @param string $parent_style Optional. The CSS style for the parent image.
 *
 * @method RenderImageMarkupService getChild($child_ref = '', $child_class = '', $child_id = '')
 * @method RenderImageMarkupService getGrandChild($grandchild_class = '', $size = 'full', $default = false, $is_lazy = true)
 * @method RenderImageMarkupService renderAll()
 * @method RenderImageMarkupService render()
 * */
class ImageRenderFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
   public static function getFacadeAccessor(){
       return 'ImageRenderFacade';
   }
}
