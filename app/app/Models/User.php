<?php


namespace App\Models;

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
        $redis = new RedisDAO();
        // переделать через Repository
        static::pivotAttached(function ($model, $relationName, $pivotIds, $pivotIdsAttributes) use ($redis) {
            if ($relationName == 'skills') {
                foreach ($pivotIds as $skillID) {
                    $redis->addSkillToUser($model->id, $skillID);
                }
            }
        });
        static::pivotDetached(function ($model, $relationName, $pivotIds) use ($redis) {
            if ($relationName == 'skills') {
                foreach ($pivotIds as $skillID) {
                    $redis->delSkillFromUser($model->id, $skillID);
                }
            }
        });
        static::deleted(function ($model) use ($redis) {
            $redis->delUserSkills($model->id);
        });
    }
}
