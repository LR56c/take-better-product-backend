<?php

namespace Src\Brands\Application;

use InvalidArgumentException;
use Src\Brands\Domain\Brand;
use Src\Brands\Domain\BrandRepository;
use Src\Brands\Domain\Exceptions\BrandNotFound;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;

class GetBrand
{
    public function __construct(
        private readonly BrandRepository $repository
    ) {}

    public function execute(string $id): Brand
    {
        $brandId = ValidUUID::from($id);

        if ($brandId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The brand id <%s> is invalid', $id));
        }

        $brand = $this->repository->find($brandId);

        if ($brand === null) {
            throw new BrandNotFound($brandId);
        }

        return $brand;
    }
}
