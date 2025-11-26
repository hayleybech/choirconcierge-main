<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CustomFieldEntry extends Pivot
{
    protected $table = 'custom_field_entries';
    public $incrementing = true;
}
