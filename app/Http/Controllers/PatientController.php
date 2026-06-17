<?php

namespace App\Http\Controllers;

use App\DTO\Patient\PatientIndexInput;
use App\DTO\Patient\PatientInput;
use App\Http\Requests\Patient\PatientIndexRequest;
use App\Http\Requests\Patient\PatientStoreRequest;
use App\Http\Requests\Patient\PatientUpdateRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\PatientService;

class PatientController extends Controller
{
    public function __construct(
        public readonly PatientService $patientService
    ) {}

    public function index(PatientIndexRequest $request)
    {
        $dataRequest = $request->validated();
        $input = PatientIndexInput::fromArray($dataRequest);

        return PatientResource::collection(
            $this->patientService->index($input)
        );
    }

    public function store(PatientStoreRequest $request)
    {
        $dataRequest = $request->validated();
        $input = PatientInput::fromArray($dataRequest);

        $this->patientService->store($input);

        return response()->json(data: ['message' => 'Paciente cadastrado com sucesso']);
    }

    public function update(PatientUpdateRequest $request, Patient $patient)
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
