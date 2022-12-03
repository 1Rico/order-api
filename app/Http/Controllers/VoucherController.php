<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherStoreRequest;
use App\Http\Resources\Voucher as VoucherResource;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class VoucherController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function index(Request $request)
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
     * @return Response
     */
    public function store(VoucherStoreRequest $request)
    {
        $validated = $request->validated();

        $validated['voucher_code'] = uniqid('V', true);

        $voucher = $request->user()->vouchers()->create($validated);

        return $this->sendResponse(new VoucherResource($voucher), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return Response
     */
    public function show(int $id)
    {
        return $this->sendResponse(new VoucherResource(Voucher::findOrFail($id)));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher  $voucher
     * @return Response
     */
    public function update(Request $request, Voucher $voucher)
    {
        $this->authorize('update', $voucher);

        $validated = $request->validate([
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'discount_amount' => 'required|integer|min:1',
            'voucher_code' => 'required|unique:vouchers|max:30'
        ]);

        $voucher->update($validated);

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return Response
     */
    public function destroy(Voucher $voucher)
    {
        $this->authorize('delete', $voucher);

        $voucher->delete();

        return response()->noContent();
    }
}
