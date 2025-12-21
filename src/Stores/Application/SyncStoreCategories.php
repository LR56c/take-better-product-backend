<?php

namespace Src\Stores\Application;

use InvalidArgumentException;
use Src\Shared\Domain\ValueObjects\UUIDError;
use Src\Shared\Domain\ValueObjects\ValidUUID;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use Src\Stores\Domain\StoreRepository;

class SyncStoreCategories
{
    public function __construct(
        private readonly StoreRepository $repository
    ) {}

    /**
     * @param  array  $categoriesData  Example: [['category_id' => 'uuid', 'url' => 'http...', 'is_active' => true]]
     */
    public function execute(string $storeId, array $categoriesData): void
    {
        $id = ValidUUID::from($storeId);

        if ($id instanceof UUIDError) {
            throw new InvalidArgumentException(sprintf('The store id <%s> is invalid', $storeId));
        }

        $store = $this->repository->find($id);

        if ($store === null) {
            throw new StoreNotFound($id);
        }

        // Format data for sync
        $syncData = [];
        foreach ($categoriesData as $item) {
            $catId = $item['category_id'];
            unset($item['category_id']); // The rest are pivot attributes
            $syncData[$catId] = $item;
        }

        $store->categories()->sync($syncData);
    }
}
