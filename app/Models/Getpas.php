<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

    class Getpas extends Model
    {
        // app/Models/Getpas.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
