<?php

namespace App\Repositories;

use App\DTO\Patient\PatientIndexInput;
use App\DTO\Patient\PatientInput;
use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;

class PatientRepository
{
    public function index(PatientIndexInput $input): LengthAwarePaginator
    {
        return Patient::query()
            ->with('address')
            ->when($input->gender, function ($query) use ($input) {
                $query->where('gender', $input->gender);
            })
            ->when($input->search, function ($query) use ($input) {
                $query->where(function ($query) use ($input) {
                    $query
                        ->where('name', 'like', "%{$input->search}%")
                        ->orWhere('cpf', 'like', "%{$input->search}%")
                        ->orWhere('cns', 'like', "%{$input->search}%")
                        ->orWhere('phone', 'like', "%{$input->search}%");
                });
            })
            ->orderBy($input->sortBy, $input->sortDir)
            ->paginate(
                perPage: $input->perPage,
                page: $input->page
            );
    }

    public function store(PatientInput $input): Patient
    {
        return Patient::create($input->toArray());
    }

    public function update(PatientInput $input, Patient $patient): void
    {
        $patient->update($input->toArray());
    }

    public function delete(Patient $patient)
    {
        $patient->delete();
    }

    public function cpfIsStored(string $cpf, ?int $ignorePatientId = null): bool
    {
        return Patient::query()
            ->where('cpf', $cpf)
            ->when($ignorePatientId, function ($query) use ($ignorePatientId) {
                $query->whereKeyNot($ignorePatientId);
            })
            ->exists();
    }

    public function cnsIsStored(string $cns, ?int $ignorePatientId = null): bool
    {
        return Patient::query()
            ->where('cns', $cns)
            ->when($ignorePatientId, function ($query) use ($ignorePatientId) {
                $query->whereKeyNot($ignorePatientId);
            })
            ->exists();
    }
}
