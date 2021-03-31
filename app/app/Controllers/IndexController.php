<?php

namespace App\Controllers;

use App\Models\Group;
use App\Models\Skill;
use App\Models\Student;
use App\Models\TeacherCondition;
use App\Models\User;
use App\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class IndexController
{
    public function index(ServerRequestInterface $request)
    {
        try {
            $user = User::create([
                'email' => 'test1@mail.ru',
                'first_name' => 'test1',
                'last_name' => 'test1',
                'phone' => '123456678',
                'enabled' => true,
                'teacher' => false
            ]);

            $skill = Skill::create([
                'name' => 'wordpress2'
            ]);

            $teacher = Group::create([
                'name' => 'group_' . time(),
                'user_id' => $user->id,
                'min_students_num' => rand(1, 10),
                'max_students_num' => rand(1, 10),
                'min_skills_num' => rand(1, 10),
                'max_skills_num' => rand(1, 10),
                'max_useless_skill_students' => rand(1, 10) / 5,
                'enabled' => rand(0, 1)
            ]);

            $skill = $user->skills()->save($skill);

            return JsonResponse::respond('ok', 201);
        } catch (\Exception $exception) {
            return JsonResponse::respond(['message' => $exception->getMessage()], 422);
        }
    }

    public function test()
    {
        $data = ['asd' => 123];
        return JsonResponse::respond($data, 201);
    }
}