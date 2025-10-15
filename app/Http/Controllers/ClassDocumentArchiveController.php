<?php

namespace App\Http\Controllers;

use App\Models\ClassDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassDocumentArchiveController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if (! $user?->nim) {
            return redirect()
                ->route('profile.complete')
                ->with('status', 'profile-required');
        }

        $documents = ClassDocument::query()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('dashboard.documents', [
            'user' => $user,
            'documents' => $documents,
            'pageTitle' => 'Dokumen Kelas',
        ]);
    }
}
