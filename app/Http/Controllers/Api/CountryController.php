<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCountryRequest;
use App\Http\Requests\UpdateCountryRequest;
use App\Http\Resources\CountryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Src\Countries\Application\CreateCountry;
use Src\Countries\Application\GetCountry;
use Src\Countries\Application\SearchCountries;
use Src\Countries\Application\UpdateCountry;
use Src\Countries\Domain\Exceptions\CountryNotFound;
use Src\Shared\Domain\Criteria\Criteria;

/**
 * @OA\Tag(
 *     name="Countries",
 *     description="API Endpoints of Countries"
 * )
 */
class CountryController extends Controller
{
    public function __construct(
        private readonly GetCountry $getCountry,
        private readonly SearchCountries $searchCountries,
        private readonly CreateCountry $createCountry,
        private readonly UpdateCountry $updateCountry
    ) {}

    /**
     * @OA\Get(
     *      path="/countries",
     *      operationId="getCountriesList",
     *      tags={"Countries"},
     *      summary="Get list of countries",
     *      description="Returns list of countries with cursor pagination",
     *
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          description="Number of items per page",
     *          required=false,
     *
     *          @OA\Schema(type="integer", default=10)
     *      ),
     *
     *      @OA\Parameter(
     *          name="cursor",
     *          in="query",
     *          description="Cursor for pagination (ID of the last item)",
     *          required=false,
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          description="Field to sort by",
     *          required=false,
     *
     *          @OA\Schema(type="string", default="created_at")
     *      ),
     *
     *      @OA\Parameter(
     *          name="sort_type",
     *          in="query",
     *          description="Sort direction (asc or desc)",
     *          required=false,
     *
     *          @OA\Schema(type="string", default="desc")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/CountryResource")),
     *              @OA\Property(property="meta", type="object",
     *                  @OA\Property(property="cursor", type="string"),
     *                  @OA\Property(property="limit", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          )
     *      ),
     *
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

            $result = $this->searchCountries->execute($criteria);

            return response()->json([
                'data' => CountryResource::collection($result->items()),
                'meta' => [
                    'cursor' => $result->items()->last()?->id,
                    'limit' => $criteria->limit(),
                    'total' => $result->total(),
                ],
            ]);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Get(
     *      path="/countries/{id}",
     *      operationId="getCountryById",
     *      tags={"Countries"},
     *      summary="Get country information",
     *      description="Returns country data",
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Country id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *      ),
     *
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        try {
            $country = $this->getCountry->execute($id);

            return response()->json(['data' => new CountryResource($country)]);
        } catch (CountryNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *      path="/countries",
     *      operationId="storeCountry",
     *      tags={"Countries"},
     *      summary="Store new country",
     *      description="Returns country data",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/StoreCountryRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *      ),
     *
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(StoreCountryRequest $request): JsonResponse
    {
        $country = $this->createCountry->execute($request->validated());

        return response()->json(['data' => new CountryResource($country)], 201);
    }

    /**
     * @OA\Put(
     *      path="/countries/{id}",
     *      operationId="updateCountry",
     *      tags={"Countries"},
     *      summary="Update existing country",
     *      description="Returns updated country data",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *          name="id",
     *          description="Country id",
     *          required=true,
     *          in="path",
     *
     *          @OA\Schema(type="string")
     *      ),
     *
     *      @OA\RequestBody(
     *          required=true,
     *
     *          @OA\JsonContent(ref="#/components/schemas/UpdateCountryRequest")
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(ref="#/components/schemas/CountryResource")
     *      ),
     *
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     *      @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function update(UpdateCountryRequest $request, string $id): JsonResponse
    {
        try {
            $country = $this->updateCountry->execute($id, $request->validated());

            return response()->json(['data' => new CountryResource($country)]);
        } catch (CountryNotFound $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
