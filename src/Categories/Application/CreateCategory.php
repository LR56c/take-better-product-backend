<?php

namespace Src\Categories\Application;

use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;

class CreateCategory
{
    public function __construct(
        private readonly CategoryRepository $repository
    ) {}

    public function execute(array $data): Category
    {
        $category = new Category();
        $category->fill($data);

        $this->repository->save($category);

        return $category;
    }
}
