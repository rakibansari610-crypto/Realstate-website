<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UnitImageController extends Controller
{
    public function store(Request $request, Unit $unit)
    {
        try {
            $request->validate([
                'images' => 'required|array',
                'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            ]);

            $maxSort = (int) $unit->unitImages()->max('sort_order');

            DB::beginTransaction();

            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('units', 'public');
                $url = asset('storage/'.$path);

                UnitImage::create([
                    'unit_id' => $unit->id,
                    'source' => 'manual',
                    'source_unit_code' => $unit->code,
                    'image_type' => 'Image',
                    'title' => null,
                    'is_marketing_image' => false,
                    'image_remarks' => null,
                    'image_url' => $url,
                    'image_hash' => sha1($url),
                    'sort_order' => $maxSort + $index + 1,
                ]);
            }

            $firstImage = $unit->unitImages()
                ->orderBy('is_marketing_image', 'desc')
                ->orderBy('sort_order')
                ->first();

            if ($firstImage) {
                $unit->update([
                    'thumbnail' => $firstImage->image_url,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Images uploaded successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Unit image upload failed', [
                'unit_id' => $unit->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to upload images.');
        }
    }

    public function update(Request $request, Unit $unit, UnitImage $image)
    {
        try {
            abort_unless($image->unit_id === $unit->id, 404);

            $data = $request->validate([
                'title' => 'nullable|string|max:255',
                'image_remarks' => 'nullable|string',
                'sort_order' => 'nullable|integer|min:0',
                'is_marketing_image' => 'nullable|boolean',
            ]);

            DB::beginTransaction();

            if ($request->boolean('is_marketing_image')) {
                UnitImage::where('unit_id', $unit->id)->update(['is_marketing_image' => 0]);
                $data['is_marketing_image'] = 1;
            } else {
                $data['is_marketing_image'] = 0;
            }

            $image->update($data);

            $featured = $unit->unitImages()
                ->orderBy('is_marketing_image', 'desc')
                ->orderBy('sort_order')
                ->first();

            $unit->update([
                'thumbnail' => $featured?->image_url,
            ]);

            DB::commit();

            return back()->with('success', 'Image updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Unit image update failed', [
                'unit_id' => $unit->id,
                'image_id' => $image->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Failed to update image.');
        }
    }

    public function destroy(Unit $unit, UnitImage $image)
    {
        try {
            abort_unless($image->unit_id === $unit->id, 404);

            DB::beginTransaction();

            if ($image->source === 'manual' && str_contains($image->image_url, '/storage/')) {
                $relativePath = str_replace(asset('storage/').'/', '', $image->image_url);
                Storage::disk('public')->delete($relativePath);
            }

            $image->delete();

            $featured = $unit->unitImages()
                ->orderBy('is_marketing_image', 'desc')
                ->orderBy('sort_order')
                ->first();

            $unit->update([
                'thumbnail' => $featured?->image_url,
            ]);

            DB::commit();

            return back()->with('success', 'Image deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Unit image delete failed', [
                'unit_id' => $unit->id,
                'image_id' => $image->id,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete image.');
        }
    }
}
