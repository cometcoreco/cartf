<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildCategoryResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $image = get_attachment_image_by_id($this->image_id);
        $image_url = !empty($image) ? $image['img_url'] : null;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $image_url,
        ];
    }
}
