<?php
// app/Models/Project.php
namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
class Project extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'org_id', 'developer_id', 'name', 'slug', 'rera_no', 'city', 'location', 'status', 'featured',
        'launch_date', 'completion_date',  'lat', 'lng', 'cover_image_path', 'active', 'category_id', 'sub_category_id', 'handover', 'payment_plan', 'project_type', 'area_size', 'handover_payment_plan', 'building_description', 'created_by', 'updated_by', 'country_id', 'construction_start_date', 'construction_end_date', 'sale_status', 'meta_title', 'meta_description', 'meta_keywords', 'locality', 'pincode', 'draft', 'sub_category', 'category_type',
        'leadrat_id', 'min_price', 'max_price', 'short_description', 'source',
    ];
    protected $casts = [
        'featured' => 'boolean',
    ];
    public function developer()
    {
        return $this->belongsTo(Developer::class);
    }
    public function units()
    {
        return $this->hasMany(ProjectUnit::class);
    }
    public function materials()
    {
        return $this->hasMany(ProjectMaterial::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id');
    }
    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image_path ? Storage::disk('public')->url($this->cover_image_path) : null;
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function coverMedia()
    {
        return $this->hasOne(
            ProjectMedia::class,
            'project_id',
            'id'
        )->where('collection', 'cover');
    }
    public function getConstructionStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
    public function getLaunchDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
    // Completion Date
    public function getCompletionDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
    // Construction End Date
    public function getConstructionEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d-m-Y') : null;
    }
}