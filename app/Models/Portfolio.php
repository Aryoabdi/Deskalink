<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portfolio extends Model
{
    protected $primaryKey = 'portfolio_id';
    public $timestamps = false;

    protected $fillable = [
        'partner_id',
        'title',
        'description',
        'image_url',
        'document_url',
        'type'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Define the possible types of portfolio items
    const TYPES = [
        'karya' => 'Karya',
        'sertifikat' => 'Sertifikat',
        'penghargaan' => 'Penghargaan',
        'lainnya' => 'Lainnya'
    ];

    // Relationships
    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id', 'user_id');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPartner($query, $partnerId)
    {
        return $query->where('partner_id', $partnerId);
    }

    // Helper methods
    public function getTypeLabel()
    {
        return self::TYPES[$this->type] ?? 'Unknown';
    }

    public function hasImage()
    {
        return !empty($this->image_url);
    }

    public function hasDocument()
    {
        return !empty($this->document_url);
    }

    // Boot method to handle file deletion when portfolio item is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($portfolio) {
            // Here you could add logic to delete the actual files if needed
            // For example:
            // if ($portfolio->hasImage()) {
            //     Storage::delete($portfolio->image_url);
            // }
            // if ($portfolio->hasDocument()) {
            //     Storage::delete($portfolio->document_url);
            // }
        });
    }
}
