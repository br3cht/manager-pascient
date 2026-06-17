<?php

namespace App\Repositories;

use App\DTO\Address\AddressIndexInput;
use App\DTO\Address\AddressInput;
use App\Models\Address;
use Illuminate\Pagination\LengthAwarePaginator;

class AddressRepository
{
    public function index(AddressIndexInput $input): LengthAwarePaginator
    {
        return Address::query()
            ->when($input->state, function ($query) use ($input) {
                $query->where('state', strtoupper($input->state));
            })
            ->when($input->search, function ($query) use ($input) {
                $query->where(function ($query) use ($input) {
                    $query
                        ->where('street', 'like', "%{$input->search}%")
                        ->orWhere('zip_code', 'like', "%{$input->search}%")
                        ->orWhere('neighborhood', 'like', "%{$input->search}%")
                        ->orWhere('city', 'like', "%{$input->search}%")
                        ->orWhere('state', 'like', "%{$input->search}%");
                });
            })
            ->orderBy($input->sortBy, $input->sortDir)
            ->paginate(
                perPage: $input->perPage,
                page: $input->page
            );
    }

    public function store(AddressInput $input): Address
    {
        return Address::create($input->toArray());
    }

    public function update(AddressInput $input, Address $address)
    {
        $address->update($input->toArray());
    }

    public function delete(Address $address)
    {
        $address->delete();
    }
}
