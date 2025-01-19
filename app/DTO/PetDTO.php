<?php

namespace App\DTO;

use App\Enums\PetStatus;

class PetDTO {
    public int $id;
    public string $name;
    public PetStatus $status;

    public function __construct(int $id, string $name, PetStatus $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->status = $status;
    }
}
