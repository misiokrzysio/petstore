<?php

namespace App\Http\Controllers;

use App\DTO\PetDTO;
use App\DTO\StorePetDTO;
use App\Enums\PetStatus;
use App\Http\Requests\StorePetRequest;
use App\Services\PetService;
use Closure;
use Exception;
use Illuminate\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;

class PetController extends Controller
{
    /**
     * @param PetService $petService
     */
    public function __construct(
        protected readonly PetService $petService
    )
    {

    }

    /**
     * pets' list page
     *
     * @param Request $request
     * @return mixed
     * @throws Exception
     */
    public function index(Request $request): mixed
    {
        $request->merge(['status' => $request->input('status', 'available')]);

        $request->validate([
            'status' => ['required', Rule::enum(PetStatus::class)],
        ]);

        $pets = $this->petService->getPets(PetStatus::tryFrom($request->status));

        return view('pet.list')->with(['pets' => $this->paginateArray($pets, $request)]);
    }

    /**
     * create pet page
     *
     * @return Closure|Container|mixed|object|null
     */
    public function create(): mixed
    {
        return view('pet.create');
    }

    /**
     * request for store pet - for use by form
     *
     * @param StorePetRequest $request
     * @return mixed
     */
    public function store(StorePetRequest $request): mixed
    {
        $request = $request->validated();

        try {
            $this->petService->storePet(new StorePetDTO($request['name'], PetStatus::tryFrom($request['status'])));

            Session::flash('message', "Zwierzak {$request['name']} został dodany!");
        } catch(Exception $exception) {
            Session::flash('message', $exception->getMessage());
        }

        return redirect()->back();
    }

    /**
     * page of pet and form for update
     *
     * @param int $petId
     * @return Closure|Container|mixed|object|null
     */
    public function show(int $petId): mixed
    {
        try {
            $pet = $this->petService->getPet($petId);
        } catch (Exception $exception) {
            abort(404, $exception->getMessage());
        }

        return view('pet.show', ['pet' => $pet]);
    }

    /**
     * update request - use by form
     *
     * @param int $petId
     * @param StorePetRequest $request
     * @return Closure|Container|mixed|object|null
     */
    public function update(int $petId, StorePetRequest $request): mixed
    {
        $request = $request->validated();
        $petDTO = new PetDTO($petId, $request['name'], PetStatus::tryFrom($request['status']));

        try {
            $pet = $this->petService->updatePet($petDTO);

            Session::flash('message', 'Zaktualizowano!');
        } catch(Exception $exception) {
            Session::flash('message', $exception->getMessage());
        }

        return view('pet.show', ['pet' => $pet ?? $petDTO]);
    }

    /**
     * @param int $petId
     * @return mixed
     * @throws Exception
     */
    public function destroy(int $petId): mixed
    {
        if($this->petService->destroyPet($petId)) $message = "Usunięto zwierzaka o ID = $petId";
        else $message = "Nie udało usunąć się zwierzaka o ID = $petId";

        Session::flash('message', $message);

        return redirect()->back();
    }
}
