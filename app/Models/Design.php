<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Design extends Model
{
    protected $primaryKey = 'design_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'design_id',
        'partner_id',
        'title',
        'description',
        'price',
        'status',
        'file_url',
        'thumbnail',
        'category'
    ];

    protected $casts = [
        'price' => 'integer',
        'created_at' => 'datetime'
    ];

    // Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    public function previews()
    {
        return $this->hasMany(DesignPreview::class, 'design_id', 'design_id');
    }

    public function moderationLogs()
    {
        return $this->hasMany(ModerationLog::class, 'content_id', 'design_id')
                    ->where('content_type', 'design');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helper methods
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }
}
