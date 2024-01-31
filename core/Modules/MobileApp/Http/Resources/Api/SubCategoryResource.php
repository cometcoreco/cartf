<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubCategoryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $child_category = [];
        $image_url = null;
        if(!empty($this->childcategory)){
            $child_category["child_categories"] = ChildCategoryResource::collection($this->childcategory);

            $image = get_attachment_image_by_id($this->image_id);
            $image_url = !empty($image) ? $image['img_url'] : null;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $image_url,
        ] + $child_category;
    }
}
