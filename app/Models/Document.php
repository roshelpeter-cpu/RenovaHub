<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    protected $fillable = [
        'project_id',
        'uploaded_by',
        'name',
        'description',
        'original_name',
        'category',
        'disk',
        'path',
        'mime',
        'size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sizeLabel(): string
    {
        $size = (int) $this->size;

        if ($size < 1024) {
            return $size.' B';
        }

        if ($size < 1048576) {
            return round($size / 1024).' KB';
        }

        return number_format($size / 1048576, 1).' MB';
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    /**
     * Stored keys stay stable for older uploads. Labels are what the filter shows.
     *
     * @return array<string, string>
     */
    public static function categories(): array
    {
        return [
            'design' => 'Design',
            'contracts' => 'Contract',
            'quotation' => 'Quotation',
            'invoices' => 'Invoice',
            'payment' => 'Payment',
            'materials' => 'Materials',
            'site_photos' => 'Site Photos',
            'technical' => 'Technical',
            'inspection' => 'Inspection',
            'warranty' => 'Warranty',
            'floor_plans' => 'Floor Plans',
            'final_design' => 'Final Design',
            'receipts' => 'Receipts',
            'construction' => 'Construction',
            'other' => 'Other',
        ];
    }

    /**
     * Designers manage design packages. Financial and construction files stay with the contractor.
     *
     * @return list<string>
     */
    public static function designerCategories(): array
    {
        return ['design', 'floor_plans', 'technical', 'materials', 'final_design', 'other'];
    }

    public function categoryLabel(): string
    {
        return self::categories()[$this->category] ?? str($this->category)->replace('_', ' ')->title()->toString();
    }
}
