<?php

namespace App\Livewire;

use App\Models\Supplier;
use App\Models\SupplierCategory;
use App\Services\Contractor\SupplierService;
use Livewire\Component;

class SupplierBrowser extends Component
{
    public string $search = '';

    public string $category = '';

    public string $location = '';

    public string $sort = 'relevant';

    public function render(SupplierService $suppliers)
    {
        return view('livewire.supplier-browser', [
            'suppliers' => $suppliers->directory(
                trim($this->search) ?: null,
                $this->category ?: null,
                $this->location ?: null,
                $this->sort,
            )->get(),
            'categories' => SupplierCategory::query()->orderBy('name')->get(),
            'cities' => Supplier::query()->whereNotNull('city')->distinct()->orderBy('city')->pluck('city'),
        ]);
    }
}
