<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'name'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, "category_id", "id");
    }

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }
}
