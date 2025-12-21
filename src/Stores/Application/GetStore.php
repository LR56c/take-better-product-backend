<?php

namespace Src\Stores\Application;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreRepository;

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

        if ($store === null) {
            throw new StoreNotFound($storeId);
        }

        return $store;
    }
}
