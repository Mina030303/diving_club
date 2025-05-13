<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;


class Announcement extends Model
{
    use HasFactory;
    use Searchable;

    
    protected $fillable = [
        'title',
        'content',
        'is_pinned',
        'is_published',
        'user_id',
        'published_at'
    ];
    
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 搜尋的資料
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
        ];
    }
}