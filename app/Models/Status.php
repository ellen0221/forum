<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    // 微故事模型,与用户一对一关联
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
