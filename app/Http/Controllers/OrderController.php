<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidVoucherException;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Resources\Order as OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    private OrderService $orderService;

    /**
     * @param OrderService $orderService
     */
    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return OrderResource::collection($request->user()->orders()->orderBy('created_at', 'desc')->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderStoreRequest $request
     * @return JsonResponse
     */
    public function store(OrderStoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            $order = $this->orderService->createOrder($validated);

            return $this->sendResponse(new OrderResource($order), 201);

        } catch (InvalidVoucherException $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return $this->sendResponse(new OrderResource(Order::findOrFail($id)));
    }
}
