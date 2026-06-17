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

        Log::channel('daily')->info('Endereco '.$address->street.' Adicionado com Sucesso');
    }

    public function update(AddressInput $input, Address $address): void
    {
        $this->addressRepository->update($input, $address);

        Log::channel('daily')->info('Endereco '.$address->id.' Atualizado com Sucesso');
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

        Log::channel('daily')->info('Endereco '.$addressId.' deletado com Sucesso');
    }
}
