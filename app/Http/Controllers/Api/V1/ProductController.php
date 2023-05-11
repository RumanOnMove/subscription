<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\PaymentGatewaySlugEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\V1\ProductResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Models\Product;
use MoveOn\Subscription\Requests\ProductStoreDTORequest;
use MoveOn\Subscription\Requests\ProductUpdateDTORequest;
use MoveOn\Subscription\Service\ProductService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ProductController extends Controller
{
    /**
     * List of products
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = ProductService::make()->listProduct($request);

        return ProductResource::collection($products["result"])->additional(
            [
                "success" => true,
                "message" => "Product fetched successfully",
                "filters" => $products["filters"],
            ]
        );
    }


    /**
     * Store new product
     * @param ProductStoreRequest $request
     * @return ProductResource
     * @throws Exception
     */
    public function store(ProductStoreRequest $request): ProductResource
    {
        $paymentGateway = PaymentGateway::findOrFail($request->get('gateway_id'));
        $form = $request->validated();
        unset($form["gateway_id"]);

        if($paymentGateway->slug == PaymentGatewaySlugEnum::SHOPIFY()){
            $product = Product::create(
                [
                    "type" => "SERVICE",
                    ...$request->validated(),
                ]
            );

            return (new ProductResource($product))->additional(
                [
                    "success" => true,
                    "message" => "Shopify Product created successfully",
                ]
            );
        }

        $product = ProductService::make()->createProduct(
            $paymentGateway,
            new ProductStoreDTORequest(...$form)
        );
        return (new ProductResource($product))->additional(
            [
                "success" => true,
                "message" => "Product created successfully",
            ]
        );
    }

    /**
     * Update product
     * @param ProductUpdateRequest $request
     * @param Product $product
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ProductUpdateRequest $request, Product $product): JsonResponse
    {
        $paymentGateway = $product->gateway;
        $form = $request->validated();
        unset($form["gateway_id"]);

        if($paymentGateway->slug == PaymentGatewaySlugEnum::SHOPIFY()){
            $product->update(
                [
                    "type" => "SERVICE",
                    ...$request->validated(),
                ]
            );

            return response()->json([
                "success" => true,
                "message" => "Product updated successfully",
            ], ResponseAlias::HTTP_OK);
        }

        if (ProductService::make()->updateProduct($product, new ProductUpdateDTORequest(...$form))) {
            return response()->json([
                "success" => true,
                "message" => "Product updated successfully",
            ], ResponseAlias::HTTP_OK);
        }

        return response()->json([
            "success" => false,
            "message" => "Product update failed",
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);
    }
}
