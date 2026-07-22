<?php

namespace App\Features\CumulativeNetValue\Models;

use Illuminate\Database\Eloquent\Model;

final class CumulativeNetValue extends Model
{
    protected $table = 'net_value_per_emiten';

    public $timestamps = false;
}
