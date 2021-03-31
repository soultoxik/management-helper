<?php

namespace App\Repository;

use App\Models\Group;
use App\Models\User;
use League\Route\Http\Exception\NotFoundException;

class StudentRepository
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function findSuitableGroup()
    {
        $skills = $this->user->skills()->get();

        if (empty($skills)) {
            throw new NotFoundException('skills not found');
        }

        //получаю только id skill-ов
        $skillIds = $skills->pluck('id')->toArray();

        $sql = 'select * from groups where id = (
                select id
                from (
                         select groups.id, count(groups.id) as counter
                         from groups
                                  inner join groups_skills gs on groups.id = gs.group_id
                                  left join skills s on gs.skill_id = s.id
                         where skill_id in (1,3,5)
                         group by groups.id
                         order by counter desc
                     ) counter
                limit 1);';
//        $groups = Group::where([''])

        return ['group'];
    }
}