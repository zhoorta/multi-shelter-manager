<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetClinicalPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClinicalPhotoController extends Controller
{
    /**
     * Serve one clinical photo from the private disk: the thumbnail by
     * default, the original with ?size=full, as a download with ?download=1.
     * The pet binding is scoped to the user's current shelter, so photos of
     * another shelter answer 404 (viewers may look, like the rest of a pet's
     * health data).
     */
    public function __invoke(Request $request, Pet $pet, PetClinicalPhoto $clinicalPhoto): StreamedResponse
    {
        abort_unless(! Auth::user()->is_admin, 403);
        abort_unless($clinicalPhoto->pet_id === $pet->id, 404);

        $wantsOriginal = $request->boolean('download') || $request->query('size') === 'full';
        $path = ! $wantsOriginal && $clinicalPhoto->thumb_path !== null ? $clinicalPhoto->thumb_path : $clinicalPhoto->image_path;

        abort_unless(Storage::disk('local')->exists($path), 404);

        if ($request->boolean('download')) {
            $name = Str::slug($pet->ref.' '.$clinicalPhoto->label.' '.$clinicalPhoto->taken_at->toDateString());

            return Storage::disk('local')->download($path, $name.'.'.pathinfo($path, PATHINFO_EXTENSION));
        }

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, max-age=3600']);
    }
}
