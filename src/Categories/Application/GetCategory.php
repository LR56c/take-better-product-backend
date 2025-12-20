<?php

namespace Src\Categories\Application;

use InvalidArgumentException;
use Src\Categories\Domain\Category;
use Src\Categories\Domain\CategoryRepository;
use Src\Categories\Domain\Exceptions\CategoryNotFound;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class GetCategory
{
    public function __construct(
        private readonly CategoryRepository $repository
    ) {}

    public function execute(string $id): Category
    {
        $categoryId = ValidUUID::from($id);

        if ($categoryId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The category id <%s> is invalid', $id));
        }

        $category = $this->repository->find($categoryId);

        if (null === $category) {
            throw new CategoryNotFound($categoryId);
        }

        return $category;
    }
}
