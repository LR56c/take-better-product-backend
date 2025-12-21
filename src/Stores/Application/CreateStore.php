<?php

namespace Src\Stores\Application;

use Src\Stores\Domain\Store;
use Src\Stores\Domain\StoreRepository;

class CreateStore
{
    public function __construct(
        private readonly StoreRepository $repository
    ) {}

    public function execute(array $data): Store
    {
        $store = new Store;
        $store->fill($data);

        $this->repository->save($store);

        return $store;
    }
}
