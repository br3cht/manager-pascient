<?php

namespace App\Services;

use App\DTO\Address\AddressIndexInput;
use App\DTO\Address\AddressInput;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AddressService
{
    public function __construct(
        private AddressRepository $addressRepository
    ) {}

    public function index(AddressIndexInput $input): LengthAwarePaginator
    {
        return $this->addressRepository->index($input);
    }

    public function store(AddressInput $input): void
    {
        $address = $this->addressRepository->store($input);

        Log::channel('daily')->info('Endereco criado com sucesso', [
            'action' => 'created',
            'entity' => 'address',
            'entity_id' => $address->id,
            'street' => $address->street,
            'zip_code' => $address->zip_code,
            'city' => $address->city,
            'state' => $address->state,
        ]);
    }

    public function update(AddressInput $input, Address $address): void
    {
        $this->addressRepository->update($input, $address);

        Log::channel('daily')->info('Endereco atualizado com sucesso', [
            'action' => 'updated',
            'entity' => 'address',
            'entity_id' => $address->id,
            'changes' => $input->toArray(),
        ]);
    }

    public function delete(Address $address): void
    {
        if ($address->patients()->exists()) {
            throw ValidationException::withMessages([
                'address' => ['Este endereço não pode ser deletado porque está vinculado a um paciente.'],
            ]);
        }

        $addressId = $address->id;

        $this->addressRepository->delete($address);

        Log::channel('daily')->info('Endereco deletado com sucesso', [
            'action' => 'deleted',
            'entity' => 'address',
            'entity_id' => $addressId,
        ]);
    }
}
