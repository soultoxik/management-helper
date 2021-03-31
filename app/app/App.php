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
    }
}