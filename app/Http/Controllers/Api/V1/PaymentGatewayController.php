<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentGatewayStoreRequest;
use App\Http\Requests\PaymentGatewayUpdateRequest;
use App\Http\Resources\V1\PaymentGatewayResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use MoveOn\Subscription\Models\PaymentGateway;
use MoveOn\Subscription\Requests\PaymentGatewayRequest;
use MoveOn\Subscription\Service\PaymentGatewayService;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class PaymentGatewayController extends Controller
{
    /**
     * List of payments gateway
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $gateways = PaymentGatewayService::make()->list($request);

        return PaymentGatewayResource::collection($gateways["result"])->additional([
            "filters" => $gateways["filters"],
            "success" => true,
            "message" => "Gateway fetched successfully",
        ]);
    }

    /**
     * Show specific payment gateway
     * @param PaymentGateway $payment_gateway
     * @return PaymentGatewayResource
     */
    public function show(PaymentGateway $payment_gateway): PaymentGatewayResource
    {
        $paymentGateway = (new PaymentGatewayResource($payment_gateway));
        return ($paymentGateway)->additional([
            "message" => "Gateway fetched successfully",
            "success" => true,
        ]);
    }

    /**
     * Store new payment gateway
     * @param PaymentGatewayStoreRequest $request
     * @return PaymentGatewayResource
     */
    public function store(PaymentGatewayStoreRequest $request): PaymentGatewayResource
    {
        $form = $this->__getRequest($request->validated());

        return (new PaymentGatewayResource(PaymentGatewayService::make()->create($form)))->additional([
            "success" => true,
            "message" => "Gateway created successfully",
        ]);
    }

    /**
     * Update payment gateway
     * @param PaymentGatewayUpdateRequest $request
     * @param PaymentGateway $payment_gateway
     * @return JsonResponse
     */
    public function update(PaymentGatewayUpdateRequest $request, PaymentGateway $payment_gateway): JsonResponse
    {
        $form = $this->__getRequest($request->validated());

        if (PaymentGatewayService::make()->update($payment_gateway, $form)) {
            return response()->json([
                "message" => "Gateway updated successfully",
                "success" => true,
            ], ResponseAlias::HTTP_OK);
        }

        return response()->json([
            "message" => "Gateway update failed",
            "success" => false,
        ], ResponseAlias::HTTP_UNPROCESSABLE_ENTITY);

    }

    /**
     * Initialize payment gateway request object
     * @param array $form
     * @return PaymentGatewayRequest
     */
    private function __getRequest(array $form): PaymentGatewayRequest
    {
        return new PaymentGatewayRequest(
            name: $form["name"],
            slug: $form["slug"],
            gateway_type: $form["gateway_type"],
            fee_type: $form["fee_type"],
            fee: $form["fee"],
            logo: $form["logo"],
            url: $form["url"],
            is_active: $form["is_active"] ?? true,
            customer_visible: $form["customer_visible"] ?? true,
        );
    }
}
