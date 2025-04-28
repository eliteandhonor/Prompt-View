<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromptVersion extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'content',
        'version_number',
        'created_by',
        'created_at',
    ];

    /**
     * The prompt this version belongs to.
     */
    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    /**
     * The user who created this version.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Comments on this prompt version.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Outcomes linked to this prompt version.
     */
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }
}