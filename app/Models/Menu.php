<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'route_name',
        'parent_id',
        'order',
        'is_active',
        'target',
    ];

    public function parent()
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order', 'asc');
    }

    public function getFormattedUrl($country = 'in')
    {
        if (!empty($this->route_name) && Route::has($this->route_name)) {
            try {
                $routeObj = Route::getRoutes()->getByName($this->route_name);
                if ($routeObj && in_array('country', $routeObj->parameterNames())) {
                    return route($this->route_name, ['country' => $country]);
                }
                return route($this->route_name);
            } catch (\Exception $e) {
                return url($this->url);
            }
        }

        if (str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://') || $this->url === '#') {
            return $this->url;
        }

        return url($this->url);
    }
}
