<?php

namespace Tests\Unit;

use App\DTO\PetDTO;
use App\DTO\StorePetDTO;
use App\Enums\PetStatus;
use App\Services\PetService;
use Exception;
use Faker\Generator;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PetUnitTest extends TestCase
{
    protected PetService $petService;
    private Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->petService = new PetService();
        $this->faker = fake('pl_PL');
    }

    public function test_get_pets()
    {
        $petsArr = [];
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        for($i = 1; $i < $this->faker->randomDigit(); $i++){
            $petsArr[] = [
                'id' => $i,
                'name' => $this->faker->word(),
                'status' => $petStatus->value
            ];
        }

        Http::fake([
            '*' => Http::response($petsArr),
        ]);

        $result = $this->petService->getPets($petStatus);
        $this->assertCount(count($petsArr), $result);

        foreach($result as $key => $element){
            $this->assertInstanceOf(PetDTO::class, $element);
            $this->assertEquals($petsArr[$key]['name'], $element->name);
            $this->assertEquals($petStatus, $element->status);
        }
    }

    public function test_store_pet()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        Http::fake([
            '*' => Http::response([
                'id' => $id,
                'name' => $name,
                'status' => $petStatus->value,
            ]),
        ]);

        $dto = new StorePetDTO($name, $petStatus);
        $result = $this->petService->storePet($dto);

        $this->assertInstanceOf(PetDTO::class, $result);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($petStatus, $result->status);
    }

    public function test_get_pet()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        Http::fake([
            '*' => Http::response([
                'id' => $id,
                'name' => $name,
                'status' => $petStatus->value,
            ]),
        ]);

        $result = $this->petService->getPet($id);

        $this->assertInstanceOf(PetDTO::class, $result);
        $this->assertEquals($id, $result->id);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($petStatus, $result->status);
    }

    public function test_update_pet()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        Http::fake([
            '*' => Http::response([
                'id' => $id,
                'name' => $name,
                'status' => $petStatus->value,
            ]),
        ]);

        $pet = new PetDTO($id, $name, $petStatus);
        $result = $this->petService->updatePet($pet);

        $this->assertInstanceOf(PetDTO::class, $result);
        $this->assertEquals($id, $result->id);
        $this->assertEquals($name, $result->name);
        $this->assertEquals($petStatus, $result->status);
    }

    public function test_destroy_pet()
    {
        Http::fake([
            '*' => Http::response([]),
        ]);

        $result = $this->petService->destroyPet($this->faker->randomNumber());

        $this->assertTrue($result);
    }

    public function test_destroy_pet_fails()
    {
        Http::fake([
            '*' => Http::response([], 400),
        ]);

        $result = $this->petService->destroyPet($this->faker->randomNumber());

        $this->assertFalse($result);
    }

    public function testSendRequestThrowsExceptionOnError()
    {
        Http::fake([
            '*' => Http::response(['message' => 'Error occurred'], 400),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error occurred');

        $this->petService->getPet($this->faker->randomNumber());
    }
}
