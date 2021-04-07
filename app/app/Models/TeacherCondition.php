<?php


namespace App\Models;

use App\Storage\RedisDAO;
use Fico7489\Laravel\Pivot\Traits\PivotEventTrait;
use Illuminate\Database\Eloquent\Model;

class TeacherCondition extends Model
{
    use PivotEventTrait;

    protected $table = 'teachers_conditions';
    protected $fillable = [
        'user_id',
        'max_groups_num',
        'min_group_size',
        'max_group_size',
    ];

    public static function boot()
    {
        parent::boot();
        $redis = new RedisDAO();
        // переделать через Repository
        static::deleted(function ($model) use ($redis) {
            $redis->delTeacherConditionByID($model->id);
        });
        static::saved(function ($model) use ($redis) {
            $redis->setTeacherCondition($model);
        });
    }
}