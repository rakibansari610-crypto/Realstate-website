<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenAI\Laravel\Facades\OpenAI;

class StandMockupController extends Controller
{
    // Hardcode placement for stand1 (same as mask I made)
    // x=60..260, y=300..820  => width=200, height=520
    private array $placements = [
        1 => ['x' => 60, 'y' => 300, 'w' => 200, 'h' => 520], // stand1
        2 => ['x' => 60, 'y' => 60,  'w' => 300, 'h' => 120], // example stand2
    ];

    public function index()
    {
        $stands = config('stands');

        return view('stands.index', [
            'stands' => $stands,
            'mockupUrl' => session('mockupUrl'),
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'stand_id' => ['required', 'integer'],
            'logo' => ['required', 'file', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);

        $standId = (int) $request->stand_id;

        $stands = collect(config('stands'));
        $stand = $stands->firstWhere('id', $standId);
        if (! $stand) {
            return back()->withErrors(['stand_id' => 'Invalid stand selected.'])->withInput();
        }

        if (! isset($this->placements[$standId])) {
            return back()->withErrors(['stand_id' => 'No placement configured for this stand.'])->withInput();
        }

        // Store uploaded logo
        $logoPath = $request->file('logo')->store('logos', 'public');
        $logoAbs = Storage::disk('public')->path($logoPath);

        // 1) Create basic mockup by overlaying logo onto the stand template (GD)
        $compositedAbs = $this->overlayLogoOnStand(
            $stand['template'],
            $logoAbs,
            $this->placements[$standId]
        );

        // 2) OPTIONAL: Send to OpenAI to make it look like printed (blending, lighting)
        // If you want pure overlay only, comment this block and just save $compositedAbs.
        try {
            $prompt = 'Make the logo look realistically printed on the stand surface. '
                .'Keep the stand unchanged. Preserve the logo design and colors.';

            $result = OpenAI::images()->edit([
                'model' => 'gpt-image-1',
                'image' => fopen($compositedAbs, 'r'),
                'mask' => fopen($stand['mask'], 'r'),
                'prompt' => $prompt,
                'size' => '1024x1024',
            ]);

            $b64 = $result->data[0]->b64_json ?? null;
            if (! $b64) {
                throw new \RuntimeException('OpenAI did not return an image.');
            }

            $pngBinary = base64_decode($b64);
            $outPath = 'mockups/mockup_'.time().'_'.uniqid().'.png';
            Storage::disk('public')->put($outPath, $pngBinary);

            $mockupUrl = asset(Storage::url($outPath));

            return redirect()->route('stands.index')->with('mockupUrl', $mockupUrl);

        } catch (\Throwable $e) {
            // If OpenAI fails, still return the simple composited result
            $fallbackPath = 'mockups/mockup_simple_'.time().'_'.uniqid().'.png';
            Storage::disk('public')->put($fallbackPath, file_get_contents($compositedAbs));

            return redirect()->route('stands.index')->with('mockupUrl', Storage::url($fallbackPath));
        }
    }

    private function overlayLogoOnStand(string $standAbsPath, string $logoAbsPath, array $p): string
    {
        // Load stand (template)
        $stand = $this->loadImageAny($standAbsPath);
        if (! $stand) {
            throw new \RuntimeException("Cannot load stand image: $standAbsPath");
        }

        // Enable alpha for PNG
        imagealphablending($stand, true);
        imagesavealpha($stand, true);

        // Load logo
        $logo = $this->loadImageAny($logoAbsPath);
        if (! $logo) {
            throw new \RuntimeException("Cannot load logo image: $logoAbsPath");
        }

        // Fit logo into target box while preserving aspect ratio
        $targetW = (int) $p['w'];
        $targetH = (int) $p['h'];

        $srcW = imagesx($logo);
        $srcH = imagesy($logo);

        $scale = min($targetW / $srcW, $targetH / $srcH);
        $newW = max(1, (int) round($srcW * $scale));
        $newH = max(1, (int) round($srcH * $scale));

        $resized = imagecreatetruecolor($newW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);

        // Transparent background
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefilledrectangle($resized, 0, 0, $newW, $newH, $transparent);

        imagecopyresampled($resized, $logo, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);

        // Center logo inside placement box
        $dstX = (int) $p['x'] + (int) floor(($targetW - $newW) / 2);
        $dstY = (int) $p['y'] + (int) floor(($targetH - $newH) / 2);

        // Paste with alpha
        imagecopy($stand, $resized, $dstX, $dstY, 0, 0, $newW, $newH);

        // Save composited temp PNG
        $tmp = storage_path('app/tmp');
        if (! is_dir($tmp)) {
            mkdir($tmp, 0775, true);
        }

        $outAbs = $tmp.'/composited_'.time().'_'.uniqid().'.png';
        imagepng($stand, $outAbs);

        // Cleanup
        imagedestroy($stand);
        imagedestroy($logo);
        imagedestroy($resized);

        return $outAbs;
    }

    private function loadImageAny(string $path)
    {
        $info = getimagesize($path);
        if (! $info) {
            return null;
        }

        return match ($info[2]) {
            IMAGETYPE_PNG => imagecreatefrompng($path),
            IMAGETYPE_JPEG => imagecreatefromjpeg($path),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? imagecreatefromwebp($path) : null,
            default => null,
        };
    }
}
