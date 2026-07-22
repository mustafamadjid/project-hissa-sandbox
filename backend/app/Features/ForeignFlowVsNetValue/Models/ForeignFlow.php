<?php

namespace App\Features\ForeignFlowVsNetValue\Models;

use Illuminate\Database\Eloquent\Model;

final class ForeignFlow extends Model
{
    protected $table = 'foreign_domestic_flow';

    public $timestamps = false;
}
