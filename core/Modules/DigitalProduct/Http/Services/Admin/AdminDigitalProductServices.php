<?php

namespace Modules\DigitalProduct\Http\Services\Admin;

use Modules\Campaign\Entities\CampaignSoldProduct;
use Modules\DigitalProduct\Http\Traits\DigitalProductGlobalTrait;

class AdminDigitalProductServices
{
    use DigitalProductGlobalTrait;

    public function store($data, $request): string
    {
        // store product
        return $this->productStore($data, $request);
    }

    public function update($data, $id){
        return $this->productUpdate($data, $id);
    }

    public function get_edit_product($id){
        return $this->get_product("edit", $id);
    }

    public function clone($id){
        return $this->productClone($id);
    }

    public function delete(int $id)
    {;
        return $this->destroy($id);
    }

    public function bulk_delete_action(array $ids)
    {
        return $this->bulk_delete($ids);
    }

    public function trash_delete(int $id)
    {
        return $this->trash_destroy($id);
    }

    public function trash_bulk_delete_action(array $ids)
    {
        return $this->trash_bulk_delete($ids);
    }

    public static function productSearch($request): array
    {
        $route = 'tenant.admin';
        return (new self)->search($request, $route);
    }
}
