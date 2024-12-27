<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id", "id");
    }

    public function categories(): BelongsTo
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
