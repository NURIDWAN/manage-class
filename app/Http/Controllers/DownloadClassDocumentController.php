<?php

namespace App\Http\Controllers;

use App\Models\ClassDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadClassDocumentController extends Controller
{
    public function __invoke(Request $request, ClassDocument $document): StreamedResponse
    {
        $user = $request->user();

        if (! $user?->nim) {
            abort(403);
        }

        if (! $document->file_path) {
            abort(404);
        }

        $disk = Storage::disk('public');

        if (! $disk->exists($document->file_path)) {
            abort(404);
        }

        $extension = pathinfo($document->file_path, PATHINFO_EXTENSION);
        $fileName = Str::slug($document->title ?? 'dokumen-kelas');
        $downloadName = $extension ? "{$fileName}.{$extension}" : $fileName;

        return $disk->download($document->file_path, $downloadName);
    }
}
