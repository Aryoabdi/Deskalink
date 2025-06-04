<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModerationLog extends Model
{
    protected $primaryKey = 'log_id';
    public $timestamps = false;

    protected $fillable = [
        'content_id',
        'content_type',
        'moderator_id',
        'action',
        'reason'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    const ACTIONS = [
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'banned' => 'Banned',
        'pending' => 'Pending'
    ];

    const CONTENT_TYPES = [
        'service' => 'Service',
        'design' => 'Design'
    ];

    // Relationships
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id', 'user_id');
    }

    public function content()
    {
        return $this->morphTo(__FUNCTION__, 'content_type', 'content_id');
    }

    // Scopes
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    public function scopeByContentType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    public function scopeByModerator($query, $moderatorId)
    {
        return $query->where('moderator_id', $moderatorId);
    }

    public function scopeByContent($query, $contentId, $contentType)
    {
        return $query->where('content_id', $contentId)
                    ->where('content_type', $contentType);
    }

    // Helper methods
    public function getActionLabel()
    {
        return self::ACTIONS[$this->action] ?? 'Unknown';
    }

    public function getContentTypeLabel()
    {
        return self::CONTENT_TYPES[$this->content_type] ?? 'Unknown';
    }

    public function isApproval()
    {
        return $this->action === 'approved';
    }

    public function isRejection()
    {
        return $this->action === 'rejected';
    }

    public function isBan()
    {
        return $this->action === 'banned';
    }

    public function isPending()
    {
        return $this->action === 'pending';
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($log) {
            if (!$log->created_at) {
                $log->created_at = now();
            }
        });
    }
}
