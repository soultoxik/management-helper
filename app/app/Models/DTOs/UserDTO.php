<?php


namespace App\Models\DTOs;

class UserDTO
{
    public int $id;
    public string $email;
    public string $firstName;
    public string $lastName;
    public string $phone;
    public bool $status;
    public bool $teacher;
    public int $created;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName,
        string $phone,
        bool $status,
        bool $teacher,
        int $created
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->status = $status;
        $this->teacher = $teacher;
        $this->created = $created;
    }
}