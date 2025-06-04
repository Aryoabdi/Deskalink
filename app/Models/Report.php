<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $primaryKey = 'report_id';
    public $timestamps = false;

    protected $fillable = [
        'reported_by',
        'reported_user',
        'reason',
        'status'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'in review' => 'In Review',
        'resolved' => 'Resolved'
    ];

    // Relationships
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by', 'user_id');
    }

    public function reportedUser()
    {
        return $this->belongsTo(User::class, 'reported_user', 'user_id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInReview($query)
    {
        return $query->where('status', 'in review');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeByReporter($query, $userId)
    {
        return $query->where('reported_by', $userId);
    }

    public function scopeByReportedUser($query, $userId)
    {
        return $query->where('reported_user', $userId);
    }

    // Helper methods
    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? 'Unknown';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInReview()
    {
        return $this->status === 'in review';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    // Status update methods
    public function markAsInReview()
    {
        $this->update(['status' => 'in review']);
    }

    public function markAsResolved()
    {
        $this->update(['status' => 'resolved']);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($report) {
            if (empty($report->status)) {
                $report->status = 'pending';
            }
        });
    }
}
