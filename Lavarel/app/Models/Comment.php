<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt_id',
        'prompt_version_id',
        'user_id',
        'content',
        'parent_id',
    ];

    /**
     * The prompt this comment belongs to.
     */
    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    /**
     * The prompt version this comment is attached to (nullable).
     */
    public function promptVersion()
    {
        return $this->belongsTo(PromptVersion::class, 'prompt_version_id');
    }

    /**
     * The user who authored this comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The parent comment (for threaded comments).
     */
    public function parent()
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Child comments (replies).
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}