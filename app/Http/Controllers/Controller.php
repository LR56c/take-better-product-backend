<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Take Better Product API",
 *      description="API documentation for Take Better Product Backend",
 *
 *      @OA\Contact(
 *          email="admin@takebetterproduct.com"
 *      ),
 *
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 *      description="Enter token in format (Bearer <token>)"
 * )
 *
 * @OA\Schema(
 *     schema="BrandResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="StoreBrandRequest",
 *     type="object",
 *     required={"name"},
 *
 *     @OA\Property(property="name", type="string", maxLength=255)
 * )
 *
 * @OA\Schema(
 *     schema="UpdateBrandRequest",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string", maxLength=255)
 * )
 *
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="external_id", type="string"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="description", type="string", nullable=true),
 *     @OA\Property(property="url", type="string", format="url"),
 *     @OA\Property(property="price", type="object",
 *         @OA\Property(property="amount", type="number", format="float"),
 *         @OA\Property(property="currency", type="string")
 *     ),
 *     @OA\Property(property="store", ref="#/components/schemas/StoreResource"),
 *     @OA\Property(property="brand", ref="#/components/schemas/BrandResource"),
 *     @OA\Property(property="category", ref="#/components/schemas/CategoryResource"),
 *     @OA\Property(property="images", type="array", @OA\Items(ref="#/components/schemas/ProductImageResource")),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 *
 * @OA\Schema(
 *     schema="ProductImageResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="image_url", type="string", format="url"),
 *     @OA\Property(property="main", type="boolean")
 * )
 *
 * @OA\Schema(
 *     schema="StoreProductRequest",
 *     type="object",
 *     required={"store_id", "title", "price", "url", "external_id"},
 *
 *     @OA\Property(property="store_id", type="string", format="uuid"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="url", type="string", format="url"),
 *     @OA\Property(property="external_id", type="string"),
 *     @OA\Property(property="brand_id", type="string", format="uuid", nullable=true),
 *     @OA\Property(property="images", type="array", @OA\Items(type="object",
 *         @OA\Property(property="image_url", type="string", format="url"),
 *         @OA\Property(property="main", type="boolean")
 *     ))
 * )
 *
 * @OA\Schema(
 *     schema="UpdateProductRequest",
 *     type="object",
 *
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="price", type="number")
 * )
 *
 * @OA\Schema(
 *     schema="SyncProductRequest",
 *     type="object",
 *     required={"store_id", "external_id", "title", "price", "url", "currency"},
 *
 *     @OA\Property(property="store_id", type="string", format="uuid"),
 *     @OA\Property(property="external_id", type="string"),
 *     @OA\Property(property="title", type="string"),
 *     @OA\Property(property="price", type="number"),
 *     @OA\Property(property="url", type="string", format="url"),
 *     @OA\Property(property="currency", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="StoreResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="CountryResource",
 *     type="object",
 *
 *     @OA\Property(property="id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="StoreCountryRequest",
 *     type="object",
 *     required={"name", "code", "currency"},
 *
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="code", type="string"),
 *     @OA\Property(property="currency", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateCountryRequest",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="StoreCategoryRequest",
 *     type="object",
 *     required={"name", "slug"},
 *
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="slug", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateCategoryRequest",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="StoreStoreRequest",
 *     type="object",
 *     required={"country_id", "name", "type"},
 *
 *     @OA\Property(property="country_id", type="string", format="uuid"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="type", type="string")
 * )
 *
 * @OA\Schema(
 *     schema="UpdateStoreRequest",
 *     type="object",
 *
 *     @OA\Property(property="name", type="string")
 * )
 */
abstract class Controller
{
    //
}
