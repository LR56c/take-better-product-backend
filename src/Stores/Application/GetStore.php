<?php

namespace Src\Stores\Application;

use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreRepository;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Shared\Domain\ValueObjects\UUIDError;
use InvalidArgumentException;

class GetStore
{
    public function __construct(
        private readonly StoreRepository $repository
    ) {}

    public function execute(string $id): Store
    {
        $storeId = ValidUUID::from($id);

        if ($storeId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The store id <%s> is invalid', $id));
        }

        $store = $this->repository->find($storeId);

        if (null === $store) {
            throw new StoreNotFound($storeId);
        }

        return $store;
    }
}
