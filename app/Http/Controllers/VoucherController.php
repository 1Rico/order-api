<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidVoucherException;
use App\Http\Requests\VoucherStoreRequest;
use App\Http\Requests\VoucherUpdateRequest;
use App\Http\Resources\Voucher as VoucherResource;
use App\Models\Voucher;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class VoucherController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $vouchers = match ($request->query('status')) {
            'active' => $request->user()->validVouchers(),
            'expired' => $request->user()->expiredVouchers(),
            default => $request->user()->vouchers(),
        };

        return VoucherResource::collection($vouchers->paginate());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param VoucherStoreRequest $request
     * @return JsonResponse
     */
    public function store(VoucherStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $validated['voucher_code'] = uniqid('VO', false);

        $voucher = $request->user()->vouchers()->create($validated);

        return $this->sendResponse(new VoucherResource($voucher), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return $this->sendResponse(new VoucherResource(Voucher::findOrFail($id)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param VoucherUpdateRequest $request
     * @param Voucher $voucher
     * @return Response|JsonResponse
     * @throws AuthorizationException
     */
    public function update(VoucherUpdateRequest $request, Voucher $voucher): Response|JsonResponse
    {
        $this->authorize('update', $voucher);

        $validated = $request->validated();

        if (!$voucher->isValid()) {
            return $this->sendError("Voucher is not valid and cannot be updated");
        }

        $voucher->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Voucher $voucher
     * @return Response
     * @throws AuthorizationException
     */
    public function destroy(Voucher $voucher): Response
    {
        $this->authorize('delete', $voucher);

        $voucher->delete();

        return response()->noContent();
    }
}
