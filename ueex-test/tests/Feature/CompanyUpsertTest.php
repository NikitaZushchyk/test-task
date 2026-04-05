<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Version;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyUpsertTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_new_company(): void
    {
        $payload = [
            'name'    => 'ТОВ Тестова Компанія',
            'edrpou'  => '1234567890',
            'address' => 'м. Київ, вул. Хрещатик, 1',
        ];

        $response = $this->postJson('/api/company', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure(['status', 'company_id', 'version'])
            ->assertJsonFragment(['status' => 'created', 'version' => 1]);

        $this->assertDatabaseHas('companies', [
            'edrpou' => '1234567890',
            'name'   => 'ТОВ Тестова Компанія',
        ]);

        $this->assertDatabaseHas('versions', [
            'versionable_type' => Company::class,
            'version'          => 1,
        ]);
    }

    public function test_create_returns_correct_version_number(): void
    {
        $response = $this->postJson('/api/company', [
            'name'    => 'Компанія А',
            'edrpou'  => '12345678',
            'address' => 'Адреса 1',
        ]);

        $response->assertStatus(201);
        $companyId = $response->json('company_id');

        $this->assertEquals(
            1,
            Version::where('versionable_id', $companyId)
                ->where('versionable_type', Company::class)
                ->max('version')
        );
    }

    public function test_returns_duplicate_when_data_is_identical(): void
    {
        $payload = [
            'name'    => 'Компанія без змін',
            'edrpou'  => '5555555555',
            'address' => 'Постійна адреса',
        ];

        Company::create($payload);

        $response = $this->postJson('/api/company', $payload);

        $response->assertStatus(200)
            ->assertJsonFragment(['status' => 'duplicate', 'version' => 1]);

        $this->assertEquals(
            1,
            Version::where('versionable_type', Company::class)
                ->where('versionable_id', Company::where('edrpou', '5555555555')->value('id'))
                ->count()
        );
    }

    public function test_duplicate_does_not_create_new_version(): void
    {
        $payload = [
            'name'    => 'Статична компанія',
            'edrpou'  => '6666666666',
            'address' => 'Незмінна вулиця',
        ];

        $company = Company::create($payload);

        $this->postJson('/api/company', $payload);
        $this->postJson('/api/company', $payload);

        $this->assertEquals(1, $company->fresh()->currentVersionNumber());
    }

    public function test_validation_fails_when_name_is_missing(): void
    {
        $this->postJson('/api/company', [
            'edrpou'  => '1234567890',
            'address' => 'Адреса',
        ])->assertStatus(422)->assertJsonValidationErrors(['name']);
    }

    public function test_validation_fails_when_edrpou_is_invalid_length(): void
    {
        $this->postJson('/api/company', [
            'name'    => 'Компанія',
            'edrpou'  => '12345678901',
            'address' => 'Адреса',
        ])->assertStatus(422)->assertJsonValidationErrors(['edrpou']);

        $this->postJson('/api/company', [
            'name'    => 'Компанія',
            'edrpou'  => '1234567',
            'address' => 'Адреса',
        ])->assertStatus(422)->assertJsonValidationErrors(['edrpou']);
    }

    public function test_versions_endpoint_returns_history(): void
    {
        $company = Company::create([
            'name'    => 'Версійна компанія',
            'edrpou'  => '7777777777',
            'address' => 'Адреса 1',
        ]);

        $this->postJson('/api/company', [
            'name'    => 'Версійна компанія',
            'edrpou'  => '7777777777',
            'address' => 'Адреса 2',
        ]);

        $response = $this->getJson("/api/company/7777777777/versions");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'company_id',
                'edrpou',
                'versions' => [['id', 'version', 'data', 'created_at']],
            ]);

        $this->assertCount(2, $response->json('versions'));
    }

    public function test_versions_endpoint_returns_404_for_unknown_edrpou(): void
    {
        $this->getJson('/api/company/0000000000/versions')->assertStatus(404);
    }
}
