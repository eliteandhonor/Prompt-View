<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'prompt_id',
        'prompt_version_id',
        'user_id',
        'content',
        'created_at',
    ];

    /**
     * The prompt this outcome belongs to.
     */
    public function prompt()
    {
        return $this->belongsTo(Prompt::class);
    }

    /**
     * The version of the prompt this outcome is linked to (nullable).
     */
    public function promptVersion()
    {
        return $this->belongsTo(PromptVersion::class, 'prompt_version_id');
    }

    /**
     * The user who authored this outcome.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}