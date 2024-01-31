<?php

namespace Modules\MobileApp\Http\Resources;

use Illuminate\Http\Request;use Illuminate\Http\Resources\Json\JsonResource;

class MobileIntroResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        $image = get_attachment_image_by_id($this->image_id);
        $img_url = !empty($image) ? $image['img_url'] : '';

        return [
            "title" => $this->title,
            "description" => $this->description,
            "image" => $img_url
        ];
    }
}
