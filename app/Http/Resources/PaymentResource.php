<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'transaction_id' => $this->transaction_id,
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'payment_method_id' => $this->payment_method_id,
            'payment_method' => new PaymentMethodResource($this->whenLoaded('paymentMethod')),
            'amount' => $this->amount,
            'payment_type' => $this->payment_type,
            'payment_date' => $this->payment_date?->format('Y-m-d'),
            'transfer_proof' => $this->transfer_proof ? asset('storage/'.$this->transfer_proof) : null,
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'verified_by' => $this->verified_by,
            'verified_user' => new UserResource($this->whenLoaded('verifiedBy')),
            'verified_at' => $this->verified_at,
            'delete_at' => $this->delete_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
