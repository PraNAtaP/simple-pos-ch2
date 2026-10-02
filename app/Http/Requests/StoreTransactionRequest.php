<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $items = $this->input('items', []);

            foreach ($items as $index => $item) {
                $product = Product::find($item['product_id'] ?? null);

                if ($product && isset($item['qty']) && $item['qty'] > $product->stock) {
                    $validator->errors()->add(
                        "items.{$index}.qty",
                        "Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}, diminta: {$item['qty']}."
                    );
                }
            }
        });
    }
}