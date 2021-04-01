<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherCondition extends Model
{
    protected $table = 'teachers_conditions';
    protected $fillable = [
        'user_id',
        'max_groups_num',
        'min_group_size',
        'max_group_size',
    ];
}