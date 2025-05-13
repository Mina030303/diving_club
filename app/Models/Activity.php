<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable; 


class Activity extends Model
{
    use HasFactory;
    use Searchable;

    
    // 在 Activity 模型中確保有以下關聯和可變屬性
    protected $fillable = [
        'title',
        'description',
        'content',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'max_participants',
        'location',
        'price',
        'activity_category_id',
        'is_published'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'is_published' => 'boolean',
        'price' => 'float',
        'max_participants' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(ActivityCategory::class, 'activity_category_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // 添加一個方便的方法來檢查用戶是否已經報名
    public function isUserRegistered($userId)
    {
        return $this->registrations()->where('user_id', $userId)->exists();
    }

    // 添加一個方便的方法來檢查活動是否已滿
    public function isFull()
    {
        if ($this->max_participants <= 0) {
            return false; // 無限制人數
        }
        return $this->registrations()->count() >= $this->max_participants;
    }

    // 添加一個方便的方法來檢查是否在報名期間
    public function isRegistrationOpen()
    {
        $now = now();
        return $now->between($this->registration_start, $this->registration_end);
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