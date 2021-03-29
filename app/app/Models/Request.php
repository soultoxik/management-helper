<?php


namespace App\Models;


class Request
{
    private int $id;
    private string $statusID;

    public function __construct(int $id, string $statusID)
    {

        $this->id = $id;
        $this->statusID = $statusID;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getStatusID(): string
    {
        return $this->statusID;
    }

    public function setStatusID(string $statusID): void
    {
        $this->statusID = $statusID;
    }

}