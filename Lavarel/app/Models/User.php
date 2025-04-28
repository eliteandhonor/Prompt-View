<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Prompts created by the user.
     */
    public function prompts()
    {
        return $this->hasMany(Prompt::class);
    }

    /**
     * Comments authored by the user.
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Outcomes authored by the user.
     */
    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }
}
