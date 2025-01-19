<?php

namespace Tests\Feature;

use App\DTO\PetDTO;
use App\DTO\StorePetDTO;
use App\Enums\PetStatus;
use App\Services\PetService;
use Faker\Generator;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class PetFeatureTest extends TestCase
{
    protected $petService;
    protected Generator $faker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faker = fake('pl_PL');

        $this->petService = Mockery::mock(PetService::class);
        $this->app->instance(PetService::class, $this->petService);
    }

    public function test_index()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        $this->petService
            ->shouldReceive('getPets')
            ->once()
            ->with($petStatus)
            ->andReturn([new PetDTO($id, $name, $petStatus)]);

        $response = $this->get(route('pet.list', ['status' => $petStatus->value]));

        $response->assertStatus(200)
            ->assertViewHas('pets');
    }

    public function test_create()
    {
        $response = $this->get(route('pet.create'));

        $response->assertStatus(200)
            ->assertViewIs('pet.create');
    }

    public function test_store()
    {
        $this->petService
            ->shouldReceive('storePet')
            ->once()
            ->with(Mockery::type(StorePetDTO::class))
            ->andReturn();

        $name = $this->faker->text();

        $response = $this->post(route('pet.store'), [
            'name' => $name,
            'status' => $this->faker->randomElement(PetStatus::cases())->value,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('message', "Zwierzak $name został dodany!");
    }

    public function test_store_fails()
    {
        $error = $this->faker->text();

        $this->petService
            ->shouldReceive('storePet')
            ->once()
            ->andThrow(new \Exception($error));

        $response = $this->post(route('pet.store'), [
            'name' => $this->faker->text(),
            'status' => $this->faker->randomElement(PetStatus::cases())->value,
        ]);

        $response->assertRedirect()
            ->assertSessionHas('message', $error);
    }

    public function test_show()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        $this->petService
            ->shouldReceive('getPet')
            ->once()
            ->with($id)
            ->andReturn(new PetDTO($id, $name, $petStatus));

        $response = $this->get(route('pet.show', $id));

        $response->assertStatus(200)
            ->assertViewHas('pet')
            ->assertSeeText($name);
    }

    public function test_show_not_found()
    {
        $id = $this->faker->randomNumber();

        $this->petService
            ->shouldReceive('getPet')
            ->once()
            ->with($id)
            ->andThrow(new \Exception('Pet not found'));

        $response = $this->get(route('pet.show', $id));

        $response->assertStatus(404);
    }

    public function test_update()
    {
        $id = $this->faker->randomNumber();
        $name = $this->faker->word();
        $petStatus = $this->faker->randomElement(PetStatus::cases());

        $this->petService
            ->shouldReceive('updatePet')
            ->once()
            ->with(Mockery::type(PetDTO::class))
            ->andReturn(new PetDTO($id, $name, $petStatus));

        $response = $this->put(route('pet.update', $id), [
            'name' => $name,
            'status' => $petStatus->value,
        ]);

        $response->assertStatus(200)
            ->assertSessionHas('message', 'Zaktualizowano!');
    }

    public function testUpdateFailure()
    {
        $errorMsg = 'Nie udało się zaktualizować zwierzaka';
        $this->petService->shouldReceive('updatePet')
            ->once()
            ->andThrow(new \Exception($errorMsg));

        $petId = $this->faker->randomNumber();

        $requestData = [
            'name' => $this->faker->word(),
            'status' => PetStatus::PENDING->value,
        ];

        $response = $this->put(route('pet.update', $petId), $requestData);

        $response->assertStatus(200);
        $response->assertViewIs('pet.show');

        $this->assertEquals($errorMsg, Session::get('message'));
    }

    public function test_destroy()
    {
        $id = $this->faker->randomNumber();

        $this->petService
            ->shouldReceive('destroyPet')
            ->once()
            ->with($id)
            ->andReturn(true);

        $response = $this->delete(route('pet.destroy', $id));

        $response->assertRedirect()
            ->assertSessionHas('message', "Usunięto zwierzaka o ID = $id");
    }

    public function test_destroy_fails()
    {
        $id = $this->faker->randomNumber();

        $this->petService
            ->shouldReceive('destroyPet')
            ->once()
            ->with($id)
            ->andReturn(false);

        $response = $this->delete(route('pet.destroy', $id));

        $response->assertRedirect()
            ->assertSessionHas('message', "Nie udało usunąć się zwierzaka o ID = $id");
    }
}
