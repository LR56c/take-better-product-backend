<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Users\Application\SearchUsers;

/**
 * @OA\Tag(
 *     name="User",
 *     description="User Profile and Management Endpoints"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        private readonly SearchUsers $searchUsers
    ) {}

    /**
     * @OA\Get(
     *      path="/user",
     *      operationId="getUserProfile",
     *      tags={"User"},
     *      summary="Get current user profile",
     *      description="Returns the authenticated user's data",
     *      security={{"bearerAuth":{}}},
     *
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="id", type="string", format="uuid"),
     *              @OA\Property(property="email", type="string", format="email"),
     *              @OA\Property(property="role", type="string")
     *          )
     *      ),
     *
     *      @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user());
    }

    /**
     * @OA\Get(
     *      path="/users",
     *      operationId="getUsersList",
     *      tags={"User"},
     *      summary="Get list of users",
     *      description="Returns list of users with cursor pagination (Admin only)",
     *      security={{"bearerAuth":{}}},
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
     *              @OA\Property(property="data", type="array", @OA\Items(
     *                  @OA\Property(property="id", type="string", format="uuid"),
     *                  @OA\Property(property="email", type="string", format="email"),
     *                  @OA\Property(property="role", type="string")
     *              )),
     *              @OA\Property(property="meta", type="object",
     *                  @OA\Property(property="cursor", type="string"),
     *                  @OA\Property(property="limit", type="integer"),
     *                  @OA\Property(property="total", type="integer")
     *              )
     *          )
     *      ),
     *
     *      @OA\Response(response=400, description="Bad Request"),
     *      @OA\Response(response=403, description="Forbidden")
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

            $result = $this->searchUsers->execute($criteria);

            return response()->json([
                'data' => $result->items(),
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
}
