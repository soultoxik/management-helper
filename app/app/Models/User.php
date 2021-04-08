<?php


namespace App\Models;

use App\Actions\ActionsUser;
use App\Models\Traits\Transaction;
use App\Storage\RedisDAO;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use \Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use Transaction;
    use PivotEventTrait;

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

    public function teacherGroups()
    {
        return $this->hasMany('App\Models\Group');
    }

    public function isStudent()
    {
        return !$this->teacher;
    }

    public function isTeacher()
    {
        return $this->teacher;
    }

    public static function boot()
    {
        parent::boot();
        $actions = new ActionsUser();
        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) use ($actions) {
            if ($relationName == 'skills') {
                $actions->addSkills($model->id, $pivotIds);
            }
            if ($relationName == 'groups') {
                $actions->addGroups($model->id, $pivotIds);
            }
        });
        static::pivotDetached(function ($model, $relationName, $pivotIds) use ($actions) {
            if ($relationName == 'skills') {
                $actions->delSkills($model->id, $pivotIds);
            }
            if ($relationName == 'groups') {
                $actions->delGroups($model->id, $pivotIds);
            }
        });
        static::deleted(function ($model) use ($actions) {
            $actions->delUserSkill($model->id);
        });
    }
}
