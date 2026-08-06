<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assets extends Model
{
    protected $guarded = [];

    protected $casts = [
    'actual_pm_date'   => 'date',
    ];

    public function assetType()
    {
        return $this->belongsTo(AssetType::class, 'asset_id');
    }
}
