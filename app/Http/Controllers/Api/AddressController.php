<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\DTO\Address\AddressIndexInput;
use App\DTO\Address\AddressInput;
use App\Http\Requests\IndexAddressRequest;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\AddressService;

class AddressController extends Controller
{
    public function __construct(
        public readonly AddressService $addressService
    ) {}

    public function index(IndexAddressRequest $request)
    {
        $dataRequest = $request->validated();
        $input = AddressIndexInput::fromArray($dataRequest);

        return AddressResource::collection(
            $this->addressService->index($input)
        );
    }

    public function store(StoreAddressRequest $request)
    {
        $dataRequest = $request->validated();
        $input = AddressInput::fromArray($dataRequest);
        $this->addressService->store($input);

        return response()->json(data: ['message' => 'Endereco cadastrado com sucesso']);
    }

    public function show(Address $address)
    {
        return new AddressResource($address);
    }

    public function update(UpdateAddressRequest $request, Address $address)
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
