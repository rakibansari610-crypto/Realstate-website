<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'project_id', 'developer_id', 'page_index', 'count', 'category', 'code', 'status', 'ref_no',
        'community', 'property_name', 'builtup_area', 'plot_area', 'bedrooms',
        'primary_unit_view', 'primary_unit_view_id', 'secondary_unit_view', 'secondary_unit_view_id',
        'unit_model', 'floor_no', 'handover_date', 'contract_a_expiry', 'agent',
        'contact_number', 'remarks', 'country_name', 'state_name', 'city_name',
        'district_name', 'marketing_usp', 'financing_company', 'parking',
        'sub_type', 'sell_price', 'sub_community', 'property_overview', 'local_area_amenities_desc',
        'document_web', 'no_of_bathrooms', 'ret_unit_category', 'pdf_brochure_link',
        'agent_id', 'property_id', 'property_ownership_desc', 'country_id', 'state_id',
        'city_id', 'district_id', 'community_id', 'sub_community_id', 'agent_rera_no',
        'pro_google_coordinates', 'bdm_pkg', 'salesman_email', 'last_updated',
        'listing_date', 'expiry_date', 'recommended_properties', 'marketing_title',
        'marketing_options', 'agent_photo', 'arabic_title', 'arabic_description',
        'mandate', 'currency_abr', 'area_measurement', 'rera_str_no', 'furnish_status',
        'listing_type', 'permit_number', 'plan_type', 'unit_number', 'street', 'owner', 'listing_agent', 'furnished', 'size', 'view', 'parking_spaces', 'parking_number', 'parking_type', 'unit_fitting', 'unit_type', 'currency_id', 'selling_price', 'building_amenities', 'created_by', 'updated_by', 'images',  'is_for_sale',
        'is_featured', 'is_for_rent',
        'rent_price',
        'rent_period',
        'latitude',
        'longitude',
        'unit_for',
        'india_term',
        'uae_term',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'slug',
        'is_auto_slug',
        'locality',
        'data_source',

    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }

    protected $casts = [
        'images' => 'array',
        'is_for_sale' => 'boolean',
        'is_featured' => 'boolean',
        'selling_price' => 'float',
        'is_featured' => 'boolean',
        'selling_price' => 'float',
        'rent_price' => 'float',

    ];

    public function unitType()
    {
        return $this->belongsTo(Taxonomy::class, 'unit_type')
            ->where('module', 'unit_type');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Currency relation
    public function currency()
    {
        return $this->belongsTo(Taxonomy::class, 'currency_id')
            ->where('module', 'currency');
    }

    // Price formatter
    public function getFormattedPriceAttribute()
    {
        if (! $this->selling_price) {
            return 'N/A';
        }

        $price = (float) preg_replace('/[^0-9.]/', '', $this->selling_price);

        return number_format($price);
    }

    // ✅ FINAL Currency Symbol Logic
    public function getCurrencySymbolAttribute()
    {
        // 1️⃣ Direct symbol saved (BEST)
        if (! empty($this->currency_abr)) {
            return $this->currency_abr;
        }

        // 2️⃣ If currency is parent (Office), take its child ($)
        if ($this->currency && $this->currency->children()->exists()) {
            return optional(
                $this->currency->children()
                    ->where('module', 'currency')
                    ->orderBy('priority')
                    ->first()
            )->name;
        }

        // 3️⃣ If currency itself is symbol
        if ($this->currency) {
            return $this->currency->name;
        }

        return '';
    }

    public function imagesRelation()
    {
        return $this->hasMany(UnitImage::class);
    }

    public function unitImages()
    {
        return $this->hasMany(\App\Models\UnitImage::class)->orderBy('sort_order');
    }

    public function fittings()
    {
        return $this->hasMany(\App\Models\UnitFitting::class);
    }
}
