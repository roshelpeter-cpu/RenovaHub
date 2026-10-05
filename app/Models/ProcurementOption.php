<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementOption extends Model
{
    protected $fillable = [
        'procurement_proposal_id',
        'supplier_price_id',
        'construction_firm_quotation_id',
    ];

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(ProcurementProposal::class, 'procurement_proposal_id');
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(SupplierPrice::class, 'supplier_price_id');
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(ConstructionFirmQuotation::class, 'construction_firm_quotation_id');
    }
}
