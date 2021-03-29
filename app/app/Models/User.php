<?php


namespace App\Models;

class User
{

    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $phone;
    private bool $enabled;
    private bool $teacher;
    private int $created;

    public function __construct(
        int $id,
        string $email,
        string $firstName,
        string $lastName,
        string $phone,
        bool $enabled,
        bool $teacher,
        int $created
    )
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
        $this->enabled = $enabled;
        $this->teacher = $teacher;
        $this->created = $created;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function isTeacher(): bool
    {
        return $this->teacher;
    }

    public function setTeacher(bool $teacher): void
    {
        $this->teacher = $teacher;
    }

    public function getCreated(): int
    {
        return $this->created;
    }

    public function setCreated(int $created): void
    {
        $this->created = $created;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }


}