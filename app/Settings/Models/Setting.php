<?php

namespace App\Settings\Models;

use App\Core\Tenancy\Models\Concerns\BelongsToWebsite;
use App\Core\Website\Models\Website;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use BelongsToWebsite;

    protected $fillable = [
        'website_id',
        'group_name',
        'key_name',
        'value',
        'autoload',
    ];

    protected $casts = [
        'value' => 'array',
        'autoload' => 'boolean',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }
}
