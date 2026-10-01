<?php

namespace App\Http\Requests\Customer\Booking;

use App\Rules\BeforeDepartureDate;
use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method_id' => ['required', 'uuid', 'exists:payment_methods,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'payment_type' => ['required', 'string', 'in:dp,settlement'],
            'payment_date' => [
                'required',
                'date',
                new BeforeDepartureDate($this->route('id')),
            ],
            'transfer_proof' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $bookingId = $this->route('id');
            if (! $bookingId) {
                return;
            }

            $booking = \App\Models\Booking::with(['transaction.quotation', 'transaction.payments'])->find($bookingId);
            if (! $booking || ! $booking->transaction) {
                return;
            }

            $transaction = $booking->transaction;
            $amount = (float) $this->input('amount');
            $paymentType = $this->input('payment_type');
            
            $dpFromQuotation = (float) ($transaction->quotation->dp_amount ?? 0);
            $dpFallback      = round((float) $transaction->total_amount * 0.3);
            \Log::info("UPLOAD PAYMENT DIAGNOSTIC:", [
                'input_amount'        => $amount,
                'input_payment_type'  => $paymentType,
                'input_payment_date'  => $this->input('payment_date'),
                'departure_date'      => $booking->departure_date->format('Y-m-d'),
                'quotation_loaded'    => $transaction->quotation ? 'yes' : 'null',
                'dp_amount_quotation' => $dpFromQuotation,
                'dp_amount_fallback'  => $dpFallback,
                'remaining_amount'    => (float) $transaction->total_amount,
            ]);

            if ($transaction->remaining_amount <= 0 || $transaction->status === 'fully_paid') {
                $validator->errors()->add('amount', 'Tagihan untuk pemesanan ini sudah lunas.');
                return;
            }

            // Check existing payments
            $payments = $transaction->payments;
            $hasDp = $payments->whereIn('status', ['pending', 'verified'])->where('payment_type', 'dp')->count() > 0;
            $hasSettlement = $payments->whereIn('status', ['pending', 'verified'])->where('payment_type', 'settlement')->count() > 0;

            if ($hasSettlement) {
                $validator->errors()->add('payment_type', 'Pembayaran pelunasan sudah diunggah dan sedang dalam proses verifikasi atau telah disetujui.');
                return;
            }

            if ($paymentType === 'dp') {
                if ($hasDp) {
                    $validator->errors()->add('payment_type', 'Pembayaran uang muka (DP) sudah dilakukan atau sedang menunggu verifikasi. Silakan pilih kategori Pelunasan.');
                    return;
                }

                $dpAmountFromQuotation = (float) ($transaction->quotation->dp_amount ?? 0);
                // Fallback ke 30% total jika dp_amount belum diset di quotation
                $requiredDp = $dpAmountFromQuotation > 0
                    ? $dpAmountFromQuotation
                    : round((float) $transaction->total_amount * 0.3);

                if ($requiredDp <= 0) {
                    $validator->errors()->add('amount', 'Jumlah DP tidak dapat ditentukan. Silakan hubungi admin.');
                    return;
                }

                if (abs($amount - $requiredDp) > 0.01) {
                    $validator->errors()->add(
                        'amount',
                        'Jumlah pembayaran uang muka (DP) harus tepat sebesar Rp ' . number_format($requiredDp, 0, ',', '.') . '.'
                    );
                    return;
                }
            }

            if ($paymentType === 'settlement') {
                $remaining = (float) $transaction->remaining_amount;
                if (abs($amount - $remaining) > 0.01) {
                    $validator->errors()->add(
                        'amount',
                        'Jumlah pembayaran pelunasan harus tepat sebesar sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').'
                    );
                    return;
                }
            }

            if ($amount > (float) $transaction->remaining_amount) {
                $validator->errors()->add(
                    'amount',
                    'Jumlah pembayaran (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($transaction->remaining_amount, 0, ',', '.') . ').'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'payment_method_id.required' => 'Metode pembayaran wajib dipilih.',
            'payment_method_id.exists' => 'Metode pembayaran tidak valid.',
            'amount.required' => 'Jumlah pembayaran wajib diisi.',
            'amount.numeric' => 'Jumlah pembayaran harus berupa angka.',
            'amount.min' => 'Jumlah pembayaran minimal Rp 1.',
            'payment_type.required' => 'Tipe pembayaran wajib dipilih.',
            'payment_type.in' => 'Tipe pembayaran harus berupa uang muka (dp) atau pelunasan (settlement).',
            'payment_date.required' => 'Tanggal pembayaran wajib diisi.',
            'payment_date.date' => 'Tanggal pembayaran harus berupa tanggal yang valid.',
            'transfer_proof.required' => 'Bukti transfer wajib diunggah.',
            'transfer_proof.image' => 'Bukti transfer harus berupa gambar.',
            'transfer_proof.mimes' => 'Bukti transfer harus berformat: jpeg, png, atau jpg.',
            'transfer_proof.max' => 'Ukuran bukti transfer maksimal 2 MB.',
        ];
    }
}
