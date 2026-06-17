<?php

namespace App\Services;

use App\DTO\Address\AddressIndexInput;
use App\DTO\Address\AddressInput;
use App\Repositories\AddressRepository;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $this->addressRepository->store($input);
    }
}
