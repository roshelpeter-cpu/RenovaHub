<?php

namespace App\Services\Contractor;

use App\Models\Supplier;
use App\Models\SupplierPrice;
use App\Models\SupplierPriceRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public function __construct(private NotificationService $notifications) {}

    public function directory(?string $search, ?string $categorySlug, ?string $city, string $sort): Builder
    {
        $query = Supplier::query()->with(['categories', 'startingPrices']);

        if ($search) {
            $term = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function (Builder $inner) use ($term) {
                $inner->where('name', 'like', $term)
                    ->orWhere('location', 'like', $term)
                    ->orWhere('about', 'like', $term)
                    ->orWhereHas('categories', fn (Builder $category) => $category->where('name', 'like', $term))
                    ->orWhereHas('startingPrices', fn (Builder $price) => $price->where('label', 'like', $term));
            });
        }

        if ($categorySlug) {
            $query->whereHas('categories', fn (Builder $category) => $category->where('slug', $categorySlug));
        }

        if ($city) {
            $query->where('city', $city);
        }

        return match ($sort) {
            'rating' => $query->orderByDesc('rating'),
            'orders' => $query->orderByDesc('completed_orders'),
            default => $query->orderByDesc('rating')->orderBy('name'),
        };
    }

    /**
     * A supplier response is recorded by the supplier workflow, not typed by
     * the contractor as their own project quotation.
     *
     * @param  array{quoted_price: float|int|string, lead_time_days?: int|null, delivery?: string|null, warranty?: string|null, valid_until?: string|null, message?: string|null}  $offer
     */
    public function recordOffer(SupplierPriceRequest $request, array $offer): SupplierPrice
    {
        return DB::transaction(function () use ($request, $offer) {
            $price = $request->prices()->create([
                'supplier_id' => $request->supplier_id,
                'quoted_price' => round((float) $offer['quoted_price'], 2),
                'lead_time_days' => $offer['lead_time_days'] ?? null,
                'delivery' => $offer['delivery'] ?? null,
                'warranty' => $offer['warranty'] ?? null,
                'valid_until' => $offer['valid_until'] ?? null,
                'message' => $offer['message'] ?? null,
                'status' => SupplierPrice::STATUS_OFFERED,
            ]);

            $request->update(['status' => SupplierPriceRequest::STATUS_QUOTED]);

            $contractor = $request->contractor;

            if ($contractor) {
                $this->notifications->notify(
                    $contractor,
                    'Supplier price received',
                    $request->supplier->name.' quoted '.$price->money().' for '.$request->product.'.',
                    'suppliers',
                    route('contractor.prices.show', $price),
                );
            }

            return $price;
        });
    }

    public function requestRevision(User $contractor, SupplierPrice $price, ?string $note): SupplierPrice
    {
        return DB::transaction(function () use ($price, $note) {
            $price->update(['status' => SupplierPrice::STATUS_REVISION]);
            $price->request->update([
                'status' => SupplierPriceRequest::STATUS_REVISION,
                'notes' => trim(($price->request->notes ? $price->request->notes."\n" : '').($note ?: 'Revision requested.')),
            ]);

            return $price->fresh();
        });
    }
}
