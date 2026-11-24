<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentModerationController extends Controller
{
    /** L'admin voit les informations du document, jamais son contenu. */
    public function index(Request $request)
    {
        $documents = Document::with('user', 'vehicle')
            ->when(array_key_exists($request->query('type'), Document::TYPES),
                fn ($q) => $q->where('type', $request->query('type')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $like = '%' . $request->query('q') . '%';
                $query->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $like)->orWhere('phone', 'like', $like)));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.documents.index', compact('documents'));
    }

    public function destroy(Document $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document supprimé.');
    }
}