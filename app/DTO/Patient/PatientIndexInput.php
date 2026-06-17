<?php

namespace App\DTO\Patient;

class PatientIndexInput
{
    public function __construct(
        public readonly ?string $gender = null,
        public readonly string $sortBy = 'id',
        public readonly string $sortDir = 'asc',
        public readonly ?string $search = null,
        public readonly int $page = 1,
        public readonly int $perPage = 15,
    ) {}

    public static function fromArray(array $data)
    {
        return new self(
            gender: $data['gender'] ?? null,
            sortBy: $data['sort_by'] ?? 'id',
            sortDir: $data['sort_dir'] ?? 'asc',
            search: $data['search'] ?? null,
            page: $data['page'] ?? 1,
            perPage: $data['per_page'] ?? 15
        );
    }
}
