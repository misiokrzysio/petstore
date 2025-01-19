<?php

namespace App\Services;

use App\DTO\PetDTO;
use App\DTO\StorePetDTO;
use App\Enums\PetStatus;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pet service - everything action of pets' api
 */
class PetService {

    /**
     * get Pets by status
     *
     * @param PetStatus $petStatus
     * @return array
     * @throws Exception
     */
    public function getPets(PetStatus $petStatus): array
    {
        $petsArr = [];

        $res = $this->sendRequest('GET', '/findByStatus', [
            'status' => $petStatus->value
        ]);

        $pets = $res->json();

        if(!empty($pets)){
            $petsArr = array_filter(array_map(fn($pet) => $this->transformToPetDTO($pet), $pets)); //transform to DTO and remove wrong elements

            usort($petsArr, function($a, $b){
                return $a->id <=> $b->id;
            });
        }

        return $petsArr;
    }

    /**
     * store new Pet
     *
     * @param StorePetDTO $storePetDTO
     * @return PetDTO
     * @throws Exception
     */
    public function storePet(StorePetDTO $storePetDTO): PetDTO
    {
        Log::info('Creating new pet!', (array) $storePetDTO);

        $res = $this->sendRequest('POST', '', [
            'name' => $storePetDTO->name,
            'status' => $storePetDTO->status->value
        ]);

        return $this->transformToPetDTO($res->json());
    }

    /**
     * get Pet
     *
     * @param int $petId
     * @return PetDTO
     * @throws Exception
     */
    public function getPet(int $petId): PetDTO
    {
        $res = $this->sendRequest('GET', "/$petId");

        return $this->transformToPetDTO($res->json());
    }

    /**
     * update Pet
     *
     * @param PetDTO $pet
     * @return PetDTO
     * @throws Exception
     */
    public function updatePet(PetDTO $pet): PetDTO
    {
        Log::info('Updating pet!', (array) $pet);

        $res = $this->sendRequest('PUT', '', (array)$pet);

        return $this->transformToPetDTO($res->json());
    }

    /**
     * destroy Pet
     *
     * @param int $petId
     * @return bool
     * @throws Exception
     */
    public function destroyPet(int $petId): bool
    {
        Log::info("Deleting pet with id = $petId");

        $res = $this->sendRequest('DELETE', "/$petId");

        if($res->successful()) return true;

        return false;
    }

    /**
     * send request to pet api
     *
     * @param string $method
     * @param string $path
     * @param array $params
     * @return Response
     * @throws Exception
     */
    private function sendRequest(string $method, string $path, array $params = []): Response
    {
        $url = env('PETSTORE_API').'/pet'.$path;
        $res = Http::{$method}($url, $params);

        if($res->successful()) Log::debug("[$method][{$res->status()}] $url", ['request' => $params, 'response' => $res->body()]);
        else {
            Log::error("[$method][{$res->status()}] $url", ['request' => $params, 'response' => $res->body()]);

            if(array_key_exists('message', $res->json())) throw new Exception($res->json()['message']);
        }

        return $res;
    }

    /**
     * transform array from response to PetDTO, skip wrong elements from response
     *
     * @param array $pet
     * @return PetDTO|null
     */
    private function transformToPetDTO(array $pet): ?PetDTO
    {
        if(!array_key_exists('id', $pet) || !array_key_exists('name', $pet) || !array_key_exists('status', $pet)) return null;

        return new PetDTO(
            $pet['id'],
            $pet['name'],
            PetStatus::tryFrom($pet['status']) ?? PetStatus::UNDEFINED
        );
    }
}
