<?php

namespace App\Features\ForeignBuyVsSell\Models;

use Illuminate\Database\Eloquent\Model;

final class ForeignBuyVsSell extends Model
{
    protected $table = 'foreign_domestic_flow';

    public $timestamps = false;
}