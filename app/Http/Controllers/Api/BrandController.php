<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Http\Resources\BrandResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Brands\Application\GetBrand;
use Src\Brands\Application\SearchBrands;
use Src\Brands\Application\CreateBrand;
use Src\Brands\Application\UpdateBrand;
use Src\Brands\Domain\Exceptions\BrandNotFound;
use Src\Shared\Domain\Criteria\Criteria;
use InvalidArgumentException;

/**
 * @OA\Tag(
 *     name="Brands",
 *     description="API Endpoints of Brands"
 * )
 */
class BrandController extends Controller
{
    public function __construct(
        private readonly GetBrand $getBrand,
        private readonly SearchBrands $searchBrands,
        private readonly CreateBrand $createBrand,
        private readonly UpdateBrand $updateBrand
    ) {}

    /**
     * @OA\Get(
     *      path="/brands",
     *      operationId="getBrandsList",
     *      tags={"Brands"},
     *      summary="Get list of brands",
     *      description="Returns list of brands with cursor pagination",
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
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/BrandResource")),
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

            $result = $this->searchBrands->execute($criteria);

            return response()->json([
                'data' => BrandResource::collection($result->items()),
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
     *      path="/brands/{id}",
     *      operationId="getBrandById",
     *      tags={"Brands"},
     *      summary="Get brand information",
     *      description="Returns brand data",
     *      @OA\Parameter(
     *          name="id",
     *          description="Brand id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/BrandResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $brand = $this->getBrand->execute($id);
            return response()->json(['data' => new BrandResource($brand)]);
        } catch (BrandNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/brands",
     *      operationId="storeBrand",
     *      tags={"Brands"},
     *      summary="Store new brand",
     *      description="Returns brand data",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/StoreBrandRequest")
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/BrandResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $this->createBrand->execute($request->validated('name'));

        return response()->json(['data' => new BrandResource($brand)], 201);
    }

    /**
     * @OA\Put(
     *      path="/brands/{id}",
     *      operationId="updateBrand",
     *      tags={"Brands"},
     *      summary="Update existing brand",
     *      description="Returns updated brand data",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          description="Brand id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(ref="#/components/schemas/UpdateBrandRequest")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/BrandResource")
     *      ),
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(UpdateBrandRequest $request, string $id): JsonResponse
    {
        try {
            $brand = $this->updateBrand->execute($id, $request->validated('name'));
            return response()->json(['data' => new BrandResource($brand)]);
        } catch (BrandNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
