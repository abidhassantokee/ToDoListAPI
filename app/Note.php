<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note',
        'user_id'
    ];

    /**
     * A note belongs to a user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * This scope modifies the query to return the note list of logged in user
     *
     * @param $query
     */
    public function scopeCurrentUserNotes($query)
    {
        $query->where('user_id', Auth::user()->id);
    }
}
