<?php

namespace App\DTO\Patient;

use App\Enum\GenderEnum;
use Illuminate\Contracts\Support\Arrayable;

class PatientInput implements Arrayable
{
    public function __construct(
        public readonly string $name,
        public readonly string $cpf,
        public readonly string $cns,
        public readonly string $birthDate,
        public readonly GenderEnum $gender,
        public readonly ?string $phone,
        public readonly int $addressId
    ) {}

    public static function fromArray(array $data)
    {
        return new self(
            name: $data['name'],
            cpf: $data['cpf'],
            cns: $data['cns'],
            birthDate: $data['birth_date'],
            gender: GenderEnum::from($data['gender']),
            phone: $data['phone'] ?? null,
            addressId: $data['address_id']
        );
    }

    public function toArray()
    {
        return [
            'name' => $this->name,
            'cpf' => $this->cpf,
            'cns' => $this->cns,
            'birth_date' => $this->birthDate,
            'gender' => $this->gender->value,
            'phone' => $this->phone,
            'address_id' => $this->addressId,
        ];
    }
}
