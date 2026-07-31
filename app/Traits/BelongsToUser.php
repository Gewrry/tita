<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToUser
{
    /**
     * Boot the trait to handle the data isolation.
     */
    protected static function bootBelongsToUser()
    {
        // Global Scope: Automatically filters for records belonging to the authenticated user.
        static::addGlobalScope('user_id', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where($builder->getQuery()->from . '.user_id', Auth::id());
            }
        });

        // Event Hook: Automatically assigns Auth::id() when creating a new record.
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->user_id = Auth::id();
            }
        });
    }

    /**
     * Define the relationship to the user.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
