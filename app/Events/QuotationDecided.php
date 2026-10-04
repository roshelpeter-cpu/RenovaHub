<?php

namespace App\Events;

use App\Models\Quotation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuotationDecided
{
    use Dispatchable, SerializesModels;

    public function __construct(public Quotation $quotation, public string $decision) {}
}
