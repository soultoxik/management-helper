<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = 'groups';
    protected $fillable = [
        'name',
        'user_id',
        'min_students_num',
        'max_students_num',
        'min_skills_num',
        'max_skills_num',
        'max_useless_skill_students',
        'enabled'
    ];

    public function teacher()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function students()
    {
        return $this->belongsToMany('App\Models\User', 'groups_users');
    }

    public function skills()
    {
        return $this->belongsToMany('App\Models\Skill', 'groups_skills');
    }
}