<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\DTO\Patient\PatientIndexInput;
use App\DTO\Patient\PatientInput;
use App\Http\Requests\IndexPatientRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;

class PatientController extends Controller
{
    public function __construct(
        public readonly PatientService $patientService
    ) {}

    public function index(IndexPatientRequest $request)
    {
        $dataRequest = $request->validated();
        $input = PatientIndexInput::fromArray($dataRequest);

        return PatientResource::collection(
            $this->patientService->index($input)
        );
    }

    public function store(StorePatientRequest $request)
    {
        $dataRequest = $request->validated();
        $input = PatientInput::fromArray($dataRequest);

        $this->patientService->store($input);

        return response()->json(data: ['message' => 'Paciente cadastrado com sucesso']);
    }

    public function show(Patient $patient)
    {
        return new PatientResource($patient->load('address'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        $dataRequest = $request->validated();
        $input = PatientInput::fromArray($dataRequest);

        $this->patientService->update($input, $patient);

        return response()->json(data: ['message' => 'Paciente atualizado com sucesso']);
    }

    public function destroy(Patient $patient)
    {
        $this->patientService->delete($patient);

        return response()->json(data: ['message' => 'Paciente deletado com sucesso']);
    }
}
