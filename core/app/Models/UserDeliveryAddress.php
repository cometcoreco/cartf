<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\CountryManage\Entities\City;
use Modules\CountryManage\Entities\Country;
use Modules\CountryManage\Entities\State;

class UserDeliveryAddress extends Model
{
    use HasFactory;

    protected $fillable = [
      'user_id', 'country_id', 'state_id', 'city', 'address', 'full_name', 'phone', 'email', 'postal_code'
    ];

    public function state(): BelongsTo    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo    {
        return $this->belongsTo(Country::class);
    }

    public function city_rel(): BelongsTo    {
        return $this->belongsTo(City::class, 'city', 'id');
    }
}
