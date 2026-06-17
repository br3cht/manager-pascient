<?php

namespace App\Http\Controllers;

use App\DTO\Address\AddressIndexInput;
use App\DTO\Address\AddressInput;
use App\Http\Requests\Address\AddressIndexRequest;
use App\Http\Requests\Address\AddressStoreRequest;
use App\Http\Requests\Address\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(
        public readonly AddressService $addressService
    ) {}

    public function index(AddressIndexRequest $request)
    {
        $dataRequest = $request->validated();
        $input = AddressIndexInput::fromArray($dataRequest);

        return AddressResource::collection(
            $this->addressService->index($input)
        );
    }

    public function store(AddressStoreRequest $request)
    {
        $dataRequest = $request->validated();
        $input = AddressInput::fromArray($dataRequest);
        $this->addressService->store($input);

        return response()->json(data: ['message' => 'Endereco cadastrado com sucesso']);
    }

    public function update(AddressUpdateRequest $request, Address $address)
    {
        $dataRequest = $request->validated();
        $input = AddressInput::fromArray($dataRequest);

        $this->addressService->update($input, $address);

        return response()->json(data: ['message' => 'Endereco atualizado com sucesso']);
    }

    public function destroy(Address $address)
    {
        $this->addressService->delete($address);

        return response()->json(data: ['message' => 'Endereco deletado com sucesso']);
    }
}
