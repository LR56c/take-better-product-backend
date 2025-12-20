<?php

namespace Src\Brands\Application;

use Src\Brands\Domain\Brand;
use Src\Brands\Domain\Contracts\BrandRepository;
use Src\Brands\Domain\Exceptions\BrandNotFound;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\ValueObjects\UUIDError;
use InvalidArgumentException;

class UpdateBrand
{
    public function __construct(
        private readonly BrandRepository $repository
    ) {}

    public function execute(string $id, ?string $name = null): Brand
    {
        $brandId = ValidUUID::from($id);

        if ($brandId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The brand id <%s> is invalid', $id));
        }

        $brand = $this->repository->find($brandId);

        if (null === $brand) {
            throw new BrandNotFound($brandId);
        }

        if ($name !== null) {
            $brand->name = $name;
        }

        $this->repository->save($brand);

        return $brand;
    }
}
