<?php

namespace App\Features\ForeignFlowVsNetValue\Models;

use Illuminate\Database\Eloquent\Model;

final class NetValue extends Model
{
    protected $table = 'net_value_per_emiten';

    public $timestamps = false;
}
