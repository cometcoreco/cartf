<?php

namespace Modules\TaxModule\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\CountryManage\Entities\State;

class StateTax extends Model
{
    protected $fillable = [
        'state_id',
        'tax_percentage',
        'country_id'
    ];

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
