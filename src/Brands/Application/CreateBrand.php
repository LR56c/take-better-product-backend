<?php

namespace Src\Brands\Application;

use Src\Brands\Domain\Brand;
use Src\Brands\Domain\BrandRepository;

class CreateBrand
{
    public function __construct(
        private readonly BrandRepository $repository
    ) {}

    public function execute(string $name): Brand
    {
        $brand = new Brand;
        $brand->name = $name;

        $this->repository->save($brand);

        return $brand;
    }
}
