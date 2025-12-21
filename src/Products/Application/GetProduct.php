<?php

namespace Src\Products\Application;

use Src\Products\Domain\ProductRepository;
use Src\Products\Domain\Exceptions\ProductNotFound;
use Src\Products\Domain\Product;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\ValueObjects\UUIDError;
use InvalidArgumentException;

class GetProduct
{
    public function __construct(
        private readonly ProductRepository $repository
    ) {}

    public function execute(string $id): Product
    {
        $productId = ValidUUID::from($id);

        if ($productId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The product id <%s> is invalid', $id));
        }

        $product = $this->repository->find($productId);

        if (null === $product) {
            throw new ProductNotFound($productId);
        }

        return $product;
    }
}
