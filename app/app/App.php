<?php

namespace App;

use App\Models\Request;
use App\Models\Student;
use App\Models\TeacherCondition;
//use Illuminate\Support\Collection;
use Routes\Router;
use App\Models\User;
use App\Models\Skill;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;


class App
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->router->create();
    }

    public function run()
    {
//        $teacher = Skill::create([
//            'name' => 'compose'
//        ]);

//        $skill = new Skill(['name' => 'wordpress']);
//        $user = User::find(5);
//        $skill = $user->skills()->save($skill);
//        var_dump($skill);

//        $teacher = Group::create([
//            'name' => 'group_' . time(),
//            'user_id' => rand(1, 10),
//            'min_students_num' => rand(1, 10),
//            'max_students_num'=> rand(1, 10),
//            'min_skills_num'=>rand(1, 10),
//            'max_skills_num'=>rand(1, 10),
//            'max_useless_skill_students'=> rand(1, 10)/5,
//            'enabled' => rand(0, 1)
//        ]);

//        $teacher = User::create([
//            'email' => 'user_' . rand(999, 99999) . '@example.com',
//            'first_name' => 'first name_' . time(),
//            'last_name' => 'last name_' . time(),
//            'phone' => '8904' . rand(2222222,9999999),
//            'enabled' => false,
//            'teacher' => rand(0, 1)
//        ]);

//        $teacherCondition = TeacherCondition::create([
//            'user_id' => rand(10, 100),
//            'max_groups_num' => rand(10, 100),
//            'min_group_size' => rand(10, 100),
//            'max_group_size' => rand(10, 100),
//        ]);

//        $result = Student::findByID(8);
//        var_dump($result);

//        $user = new User;
//        $user->email = 'newem' . rand(2, 15) . ' ail@example.com';
//        $user->first_name = 'first name_' . time();
//        $user->last_name = 'last_name name_' . time();
//        $user->phone = '8904' . rand(2222222,9999999);
//
//        $skills = [
//            1,2
//        ];
//        $reseul = Student::insert($user, $skills);

//        $student = Student::findByID(194);
//        $student->user->first_name = 'qqqqqqqqq';
//        var_dump($student);
//        Student::change($student->user, [7, 5]);

//        Student::remove(196);

    }
}