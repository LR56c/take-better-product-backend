<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\SyncProductRequest;
use App\Http\Resources\ProductResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Products\Application\GetProduct;
use Src\Products\Application\SearchProducts;
use Src\Products\Application\CreateProduct;
use Src\Products\Application\UpdateProduct;
use Src\Products\Application\SyncProduct;
use Src\Products\Domain\Exceptions\ProductNotFound;
use Src\Shared\Domain\Criteria\Criteria;
use InvalidArgumentException;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints of Products"
 * )
 */
class ProductController extends Controller
{
    public function __construct(
        private readonly GetProduct $getProduct,
        private readonly SearchProducts $searchProducts,
        private readonly CreateProduct $createProduct,
        private readonly UpdateProduct $updateProduct,
        private readonly SyncProduct $syncProduct
    ) {}

    /**
     * @OA\Get(
     *      path="/products",
     *      operationId="getProductsList",
     *      tags={"Products"},
     *      summary="Get list of products",
     *      description="Returns list of products with cursor pagination",
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
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ProductResource")),
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

            $result = $this->searchProducts->execute($criteria);

            return response()->json([
                'data' => ProductResource::collection($result->items()),
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
     *      path="/products/{id}",
     *      operationId="getProductById",
     *      tags={"Products"},
     *      summary="Get product information",
     *      description="Returns product data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $product = $this->getProduct->execute($id);
            // Load relations for detailed view
            $product->load(['store', 'brand', 'category', 'images']);
            return response()->json(['data' => new ProductResource($product)]);
        } catch (ProductNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/products",
     *      operationId="storeProduct",
     *      tags={"Products"},
     *      summary="Store new product",
     *      description="Returns product data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreProductRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->createProduct->execute($request->validated());
        // Load relations after creation
        $product->load(['store', 'brand', 'category', 'images']);
        return response()->json(['data' => new ProductResource($product)], 201);
    }

    /**
     * @OA\Put(
     *      path="/products/{id}",
     *      operationId="updateProduct",
     *      tags={"Products"},
     *      summary="Update existing product",
     *      description="Returns updated product data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Product id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateProductRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(UpdateProductRequest $request, string $id): JsonResponse
    {
        try {
            $product = $this->updateProduct->execute($id, $request->validated());
            // Load relations after update
            $product->load(['store', 'brand', 'category', 'images']);
            return response()->json(['data' => new ProductResource($product)]);
        } catch (ProductNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/products/sync",
     *      operationId="syncProduct",
     *      tags={"Products"},
     *      summary="Sync product (Upsert)",
     *      description="Creates or updates a product based on store_id and external_id. Records price history if changed.",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/SyncProductRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/ProductResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function sync(SyncProductRequest $request): JsonResponse
    {
        try {
            $product = $this->syncProduct->execute($request->validated());
            return response()->json(['data' => new ProductResource($product)]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
