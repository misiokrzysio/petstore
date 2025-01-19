<?php

namespace App\DTO;

use App\Enums\PetStatus;

class StorePetDTO {
    public string $name;
    public PetStatus $status;

    public function __construct(string $name, PetStatus $status)
    {
        $this->name = $name;
        $this->status = $status;
    }
}
