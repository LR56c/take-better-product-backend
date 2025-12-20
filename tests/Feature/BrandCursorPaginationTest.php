<?php

namespace Tests\Feature;

use App\Models\Brand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Src\Brands\Domain\BrandRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Tests\TestCase;

class BrandCursorPaginationTest extends TestCase
{
    use RefreshDatabase;

    private BrandRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app->make(BrandRepository::class);
    }

    public function test_it_can_paginate_brands_using_cursor()
    {
        $brands = Brand::factory()->count(15)->sequence(fn ($sequence) => [
            'created_at' => now()->addMinutes($sequence->index),
        ])->create();

        $sortedBrands = $brands->sortByDesc('created_at')->values();

        $criteriaPage1 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC'
        );
        $page1 = $this->repository->search($criteriaPage1);

        $this->assertCount(5, $page1);
        $this->assertEquals($sortedBrands[0]->id, $page1[0]->id);
        $this->assertEquals($sortedBrands[4]->id, $page1[4]->id);

        $cursor = $page1->last()->id;
        $criteriaPage2 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor
        );
        $page2 = $this->repository->search($criteriaPage2);

        $this->assertCount(5, $page2);
        $this->assertEquals($sortedBrands[5]->id, $page2[0]->id);
        $this->assertEquals($sortedBrands[9]->id, $page2[4]->id);


        $cursor2 = $page2->last()->id;
        $criteriaPage3 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor2
        );
        $page3 = $this->repository->search($criteriaPage3);


        $this->assertCount(5, $page3);
        $this->assertEquals($sortedBrands[10]->id, $page3[0]->id);
        $this->assertEquals($sortedBrands[14]->id, $page3[4]->id);


        $cursor3 = $page3->last()->id;
        $criteriaPage4 = new Criteria(
            limit: 5,
            orderBy: 'created_at',
            orderType: 'DESC',
            cursor: $cursor3
        );
        $page4 = $this->repository->search($criteriaPage4);

        $this->assertCount(0, $page4);
    }

    public function test_it_handles_sorting_by_name_with_cursor()
    {

        $names = ['Apple', 'Banana', 'Cherry', 'Date', 'Elderberry'];
        foreach ($names as $name) {
            Brand::factory()->create(['name' => $name]);
        }


        $criteria1 = new Criteria(
            limit: 2,
            orderBy: 'name',
            orderType: 'ASC'
        );
        $page1 = $this->repository->search($criteria1);

        $this->assertCount(2, $page1);
        $this->assertEquals('Apple', $page1[0]->name);
        $this->assertEquals('Banana', $page1[1]->name);


        $cursor = $page1->last()->id;
        $criteria2 = new Criteria(
            limit: 2,
            orderBy: 'name',
            orderType: 'ASC',
            cursor: $cursor
        );
        $page2 = $this->repository->search($criteria2);

        $this->assertCount(2, $page2);
        $this->assertEquals('Cherry', $page2[0]->name);
        $this->assertEquals('Date', $page2[1]->name);
    }
}
