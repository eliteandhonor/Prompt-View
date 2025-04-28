<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'current_version_id',
        'is_public',
    ];

    /**
     * The user who created the prompt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * All versions of this prompt.
     */
    public function versions()
    {
        return $this->hasMany(PromptVersion::class);
    }

    /**
     * The current version of this prompt.
     */
    public function currentVersion()
    {
        return $this->belongsTo(PromptVersion::class, 'current_version_id');
    }

    /**
     * Comments directly on this prompt.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Outcomes linked to this prompt.
     */
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }
}