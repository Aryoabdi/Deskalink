<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DesignPreview extends Model
{
    protected $primaryKey = 'preview_id';
    public $timestamps = false;

    protected $fillable = [
        'design_id',
        'image_url'
    ];

    protected $casts = [
        'created_at' => 'datetime'
    ];

    // Relationships
    public function design()
    {
        return $this->belongsTo(Design::class, 'design_id', 'design_id');
    }

    // Helper methods
    public function getFullImageUrl()
    {
        return $this->image_url;
    }

    // Boot method to ensure preview is deleted when design is deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($preview) {
            // Here you could add logic to delete the actual image file if needed
            // For example: Storage::delete($preview->image_url);
        });
    }
}
