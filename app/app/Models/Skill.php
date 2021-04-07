<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    protected $table = 'skills';
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_skills');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'groups_skills');
    }

}