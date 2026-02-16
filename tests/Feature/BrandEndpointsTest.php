<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Domain\BrandRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Tests\TestCase;

class BrandEndpointsTest extends TestCase
{
    use RefreshDatabase;

    private BrandRepository $repository;
    private User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(BrandRepository::class);
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    public function test_it_can_paginate_brands_using_cursor()
    {
        // Arrange
        $brands = Brand::factory()->count(15)->sequence(fn ($sequence) => [
            'created_at' => now()->addMinutes($sequence->index),
        ])->create();

        $sortedBrands = $brands->sortByDesc('created_at')->values();

        // Act
        $criteriaPage1 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC'
        );
        $result1 = $this->repository->search($criteriaPage1);
        $page1 = $result1->items();

        // Assert
        $this->assertCount(5, $page1);
        $this->assertEquals($sortedBrands[0]->id, $page1[0]->id);
        $this->assertEquals($sortedBrands[4]->id, $page1[4]->id);

        // Act
        $cursor = $page1->last()->id;
        $criteriaPage2 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor
        );
        $result2 = $this->repository->search($criteriaPage2);
        $page2 = $result2->items();

        // Assert
        $this->assertCount(5, $page2);
        $this->assertEquals($sortedBrands[5]->id, $page2[0]->id);
        $this->assertEquals($sortedBrands[9]->id, $page2[4]->id);

        // Act
        $cursor2 = $page2->last()->id;
        $criteriaPage3 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor2
        );
        $result3 = $this->repository->search($criteriaPage3);
        $page3 = $result3->items();

        // Assert
        $this->assertCount(5, $page3);
        $this->assertEquals($sortedBrands[10]->id, $page3[0]->id);
        $this->assertEquals($sortedBrands[14]->id, $page3[4]->id);

        // Act
        $cursor3 = $page3->last()->id;
        $criteriaPage4 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor3
        );
        $result4 = $this->repository->search($criteriaPage4);
        $page4 = $result4->items();

        $this->assertCount(0, $page4);
    }

    public function test_it_handles_sorting_by_name_with_cursor()
    {
        // Arrange
        $names = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry'];
        foreach ($names as $name) {
            Brand::factory()->create(['name' => $name]);
        }

        // Act
        $criteria1 = new Criteria(
            limit: 2,
            orderBy: 'name',
            orderType: 'ASC'
        );
        $result1 = $this->repository->search($criteria1);
        $page1 = $result1->items();

        $this->assertCount(2, $page1);
        $this->assertEquals('Apple', $page1[0]->name);
        $this->assertEquals('Banana', $page1[1]->name);

        // Act
        $cursor = $page1->last()->id;
        $criteria2 = new Criteria(
            limit: 2,
            orderBy: 'name',
            orderType: 'ASC',
            cursor: $cursor
        );
        $result2 = $this->repository->search($criteria2);
        $page2 = $result2->items();

        $this->assertCount(2, $page2);
        $this->assertEquals('Cherry', $page2[0]->name);
        $this->assertEquals('Date', $page2[1]->name);
    }

    public function test_admin_can_create_brand()
    {
        $data = ['name' => 'New Brand'];

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->postJson('/api/brands', $data);

        $response->assertStatus(201)
                 ->assertJsonPath('data.name', 'New Brand');

        $this->assertDatabaseHas('brands', ['name' => 'New Brand']);
    }

    public function test_admin_can_update_brand()
    {
        $brand = Brand::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->adminUser, 'admin')
                         ->putJson("/api/brands/{$brand->id}", ['name' => 'Updated Name']);

        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Name');

        $this->assertDatabaseHas('brands', ['name' => 'Updated Name']);
    }
}
