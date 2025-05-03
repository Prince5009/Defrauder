<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    protected $fillable = [
        'user_id',
        'activity_type',
        'page_url',
        'last_activity_at'
    ];

    protected $casts = [
        'last_activity_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function updateActivity($userId, $activityType = 'page_view')
    {
        return self::updateOrCreate(
            ['user_id' => $userId],
            [
                'activity_type' => $activityType,
                'page_url' => request()->path(),
                'last_activity_at' => now()
            ]
        );
    }

    public static function isUserActive($userId, $minutes = 2)
    {
        $activity = self::where('user_id', $userId)
            ->where('last_activity_at', '>=', now()->subMinutes($minutes))
            ->first();

        return $activity !== null;
    }
} 