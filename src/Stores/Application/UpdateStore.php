<?php

namespace Src\Stores\Application;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreRepository;

class UpdateStore
{
    public function __construct(
        private readonly StoreRepository $repository
    ) {}

    public function execute(string $id, array $data): Store
    {
        $storeId = ValidUUID::from($id);

        if ($storeId instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The store id <%s> is invalid', $id));
        }

        $store = $this->repository->find($storeId);

        if (null === $store) {
            throw new StoreNotFound($storeId);
        }

        $data = array_filter($data, fn($value) => $value !== null);
        $store->fill($data);

        $this->repository->save($store);

        return $store;
    }
}
