<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function setDateAttribute($value)
    {
        $this->attributes['date'] = Carbon::createFromFormat('d-m-Y', $value)->format('Y-m-d H:m:s');
    }

    public function getDateAttribute($date)
    {
        return $this->attributes['date'] = Carbon::parse($date)->format('d-m-Y');
    }
}
