<?php


namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = [
        'email',
        'first_name',
        'last_name',
        'phone',
        'enabled',
        'teacher'
    ];

    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'users_skills');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'groups_users');
    }

    public function teacherConditions()
    {
        return $this->hasOne('App\Models\TeacherCondition', 'user_id');
    }

}
