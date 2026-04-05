<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpsertCompanyRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class CompanyController extends Controller
{
    /**
     * Create or update a company by EDRPOU.
     * If a company with the same EDRPOU exists — update it.
     * If name + address match an existing record — return 409 Conflict.
     */
    public function upsert(UpsertCompanyRequest $request): JsonResponse
    {
        $data = $request->validated();

        $company = Company::where('edrpou', $data['edrpou'])->first();

        if (!$company) {
            $company = Company::create($data);

            return response()->json([
                'status' => 'created',
                'company_id' => $company->id,
                'version' => $company->currentVersionNumber(),
            ], 201);
        }

        $company->fill($data);

        if (!$company->isDirty()) {
            return response()->json([
                'status' => 'duplicate',
                'company_id' => $company->id,
                'version' => $company->currentVersionNumber(),
            ], 200);
        }

        $company->save();

        return response()->json([
            'message' => 'Company created.',
            'company_id' => $company->id,
            'version' => $company->currentVersionNumber(),
        ], 201);
    }

    /**
     * Get all versions of a company by EDRPOU.
     */
    public function versions(string $edrpou): JsonResponse
    {
        $company = Company::where('edrpou', $edrpou)->first();

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json([
            'company_id' => $company->id,
            'edrpou' => $company->edrpou,
            'versions' => $company->versions,
        ], 200);
    }
}
