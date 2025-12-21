<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStoreRequest;
use App\Http\Requests\UpdateStoreRequest;
use App\Http\Resources\StoreResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Stores\Application\GetStore;
use Src\Stores\Application\SearchStores;
use Src\Stores\Application\CreateStore;
use Src\Stores\Application\UpdateStore;
use Src\Stores\Application\SyncStoreCategories;
use Src\Stores\Domain\Exceptions\StoreNotFound;
use Src\Shared\Domain\Criteria\Criteria;
use InvalidArgumentException;

/**
 * @OA\Tag(
 *     name="Stores",
 *     description="API Endpoints of Stores"
 * )
 */
class StoreController extends Controller
{
    public function __construct(
        private readonly GetStore $getStore,
        private readonly SearchStores $searchStores,
        private readonly CreateStore $createStore,
        private readonly UpdateStore $updateStore,
        private readonly SyncStoreCategories $syncStoreCategories
    ) {}

    /**
     * @OA\Get(
     *      path="/stores",
     *      operationId="getStoresList",
     *      tags={"Stores"},
     *      summary="Get list of stores",
     *      description="Returns list of stores with cursor pagination",
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Number of items per page",
     *          required=false,
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *      @OA\Parameter(
     *          name="cursor",
     *          in="query",
     *          description="Cursor for pagination (ID of the last item)",
     *          required=false,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Field to sort by",
     *          required=false,
     *          @OA\Schema(type="string", default="created_at")
     *      ),
     *      @OA\Parameter(
     *          name="sort_type",
     *          in="query",
     *          description="Sort direction (asc or desc)",
     *          required=false,
     *          @OA\Schema(type="string", default="desc")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/StoreResource")),
     *              @OA\Property(property="meta", type="object",
     *                  @OA\Property(property="cursor", type="string"),
     *                  @OA\Property(property="limit", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $criteria = new Criteria(
                filters: $request->except(['limit', 'cursor', 'sort_by', 'sort_type']),
                orderBy: $request->get('sort_by', 'created_at'),
                orderType: $request->get('sort_type', 'desc'),
                limit: (int) $request->get('limit', 10),
                cursor: $request->get('cursor')
            );

            $result = $this->searchStores->execute($criteria);

            return response()->json([
                'data' => StoreResource::collection($result->items()),
                'meta' => [
                    'cursor' => $result->items()->last()?->id,
                    'limit' => $criteria->limit(),
                    'total' => $result->total(),
                ]
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *      path="/stores/{id}",
     *      operationId="getStoreById",
     *      tags={"Stores"},
     *      summary="Get store information",
     *      description="Returns store data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Store id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/StoreResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $store = $this->getStore->execute($id);
            return response()->json(['data' => new StoreResource($store)]);
        } catch (StoreNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/stores",
     *      operationId="storeStore",
     *      tags={"Stores"},
     *      summary="Store new store",
     *      description="Returns store data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreStoreRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/StoreResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreStoreRequest $request): JsonResponse
    {
        $store = $this->createStore->execute($request->validated());
        return response()->json(['data' => new StoreResource($store)], 201);
    }

    /**
     * @OA\Put(
     *      path="/stores/{id}",
     *      operationId="updateStore",
     *      tags={"Stores"},
     *      summary="Update existing store",
     *      description="Returns updated store data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Store id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateStoreRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/StoreResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(UpdateStoreRequest $request, string $id): JsonResponse
    {
        try {
            $store = $this->updateStore->execute($id, $request->validated());
            return response()->json(['data' => new StoreResource($store)]);
        } catch (StoreNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/stores/{id}/categories",
     *      operationId="syncStoreCategories",
     *      tags={"Stores"},
     *      summary="Sync store categories",
     *      description="Syncs categories for a store",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Store id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              @OA\Property(property="categories", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="category_id", type="string", format="uuid"),
     *                      @OA\Property(property="url", type="string", format="url"),
     *                      @OA\Property(property="is_active", type="boolean")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Categories synced successfully")
     *          )
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function syncCategories(Request $request, string $id): JsonResponse
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*.category_id' => 'required|uuid|exists:categories,id',
            'categories.*.url' => 'required|url',
            'categories.*.is_active' => 'boolean',
        ]);

        try {
            $this->syncStoreCategories->execute($id, $request->input('categories'));
            return response()->json(['message' => 'Categories synced successfully']);
        } catch (StoreNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
