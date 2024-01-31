<?php

namespace Modules\MobileApp\Http\Resources;

use Illuminate\Http\Request;use Illuminate\Http\Resources\Json\JsonResource;

class PaymentGatewayResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request): array
    {
        $image = get_attachment_image_by_id($this['image']);
        $image_url = !empty($image) ? $image['img_url'] : '';

        return [
            "name" => $this['name'] ?? '',
            "image" => $image_url,
            "description" => $this['description'] ?? '',
            "status" => $this['status'] ?? '',
            "test_mode" => $this['test_mode'] ?? '',
            "credentials" => json_decode($this['credentials'] ?? "")
        ];
    }
}
