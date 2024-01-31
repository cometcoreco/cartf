<?php

namespace Modules\MobileApp\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $image = get_attachment_image_by_id($this->image);
        $img_url = !empty($image) ? $image['img_url'] : '';

        return [
            "id"=> $this->id,
            "title"=> $this->title,
            "subtitle"=> $this->subtitle,
            "image"=> $img_url,
            "start_date"=> $this->start_date,
            "end_date"=> $this->end_date,
        ];
    }
}
