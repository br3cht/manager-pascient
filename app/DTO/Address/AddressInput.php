<?php

namespace App\DTO\Address;

use Illuminate\Contracts\Support\Arrayable;

class AddressInput implements Arrayable
{
    public function __construct(
        public readonly string $street,
        public readonly string $zipCode,
        public readonly string $neighborhood,
        public readonly string $city,
        public readonly string $state
    ) {}

    public static function fromArray(array $data)
    {
        return new self(
            state: $data['state'],
            zipCode: $data['zip_code'],
            neighborhood: $data['neighborhood'],
            city: $data['city'],
            street: $data['street']
        );
    }

    public function toArray()
    {
        return [
            'street' => $this->street,
            'zip_code' => $this->zipCode,
            'neighborhood' => $this->neighborhood,
            'city' => $this->city,
            'state' => $this->state,
        ];
    }
}
