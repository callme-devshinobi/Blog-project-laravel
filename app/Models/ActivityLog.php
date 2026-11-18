<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    public const UPDATED_AT = null;   // logs are written once and never edited

    protected $fillable = ['user_id', 'action', 'description', 'subject_type', 'subject_id', 'ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Write one log entry. It never throws, so logging can't break the app. */
    public static function record(string $action, string $description, ?Model $subject = null): void
    {
        try {
            static::create([
                'user_id'      => auth()->id(),
                'action'       => $action,
                'description'  => Str::limit($description, 250),
                'subject_type' => $subject?->getMorphClass(),
                'subject_id'   => $subject?->getKey(),
                'ip_address'   => request()?->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}