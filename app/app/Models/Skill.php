<?php


namespace App\Models;

use \Illuminate\Database\Eloquent\Model;

class Skill extends Model
{

    protected $table = 'skills';
    protected $fillable = [
        'id',
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\Skill', 'users_skills');
    }

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group', 'groups_skills');
    }

    public static function getSkillID(array $skillsCollection)
    {
        $ids = [];
        foreach ($skillsCollection as $skill) {
            $ids[] = $skill->id;
        }
        return $ids;
    }

}