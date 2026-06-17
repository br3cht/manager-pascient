<?php

namespace App\Services;

use App\DTO\Patient\PatientIndexInput;
use App\DTO\Patient\PatientInput;
use App\Models\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PatientService
{
    public function __construct(
        private PatientRepository $patientRepository
    ) {}

    public function index(PatientIndexInput $input)
    {
        return $this->patientRepository->index($input);
    }

    public function store(PatientInput $input)
    {
        $this->cpfIsStored($input->cpf);
        $this->cnsIsStored($input->cns);

        $patient = $this->patientRepository->store($input);

        Log::channel('daily')->info('Paciente '.$patient->name.' Adicionado com Sucesso');
    }

    public function update(PatientInput $input, Patient $patient): void
    {
        $this->cpfIsStored($input->cpf, $patient->id);
        $this->cnsIsStored($input->cns, $patient->id);

        $this->patientRepository->update($input, $patient);

        Log::channel('daily')->info('Paciente '.$patient->id.' Atualizado com Sucesso');
    }

    public function delete(Patient $patient): void
    {
        $patientId = $patient->id;

        $this->patientRepository->delete($patient);

        Log::channel('daily')->info('Paciente '.$patientId.' deletado com Sucesso');
    }

    public function cpfIsStored(string $cpf, ?int $ignorePatientId = null): bool
    {
        if ($this->patientRepository->cpfIsStored($cpf, $ignorePatientId)) {
            throw ValidationException::withMessages([
                'cpf' => ['Este CPF já está cadastrado.'],
            ]);
        }

        return false;
    }

    public function cnsIsStored(string $cns, ?int $ignorePatientId = null): bool
    {
        if ($this->patientRepository->cnsIsStored($cns, $ignorePatientId)) {
            throw ValidationException::withMessages([
                'cns' => ['Este CNS já está cadastrado.'],
            ]);
        }

        return false;
    }
}
