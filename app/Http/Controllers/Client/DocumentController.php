<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $documents = $user->documents()->with('vehicle')
            ->when(array_key_exists($request->query('type'), Document::TYPES),
                fn ($q) => $q->where('type', $request->query('type')))
            ->when($request->filled('vehicle'),
                fn ($q) => $q->where('vehicle_id', $request->query('vehicle')))
            ->latest()
            ->get();

        return view('client.documents.index', [
            'documents' => $documents,
            'vehicles'  => $user->vehicles()->orderBy('brand')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('client.documents.form', [
            'document' => new Document(['vehicle_id' => $request->query('vehicle')]),
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->rules() + [
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], $this->messages());

        $file = $request->file('file');

        $request->user()->documents()->create([
            'vehicle_id'    => $data['vehicle_id'] ?? null,
            'title'         => $data['title'],
            'type'          => $data['type'],
            'expires_at'    => $data['expires_at'] ?? null,
            'file_path'     => $file->store('documents/' . $request->user()->id, 'local'),
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getMimeType() ?: 'application/octet-stream',
            'size'          => $file->getSize(),
        ]);

        return redirect()->route('client.documents.index')->with('success', 'Document ajouté au coffre-fort.');
    }

    public function edit(Request $request, Document $document)
    {
        $this->authorizeOwner($document);

        return view('client.documents.form', [
            'document' => $document,
            'vehicles' => $request->user()->vehicles()->orderBy('brand')->get(),
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $this->authorizeOwner($document);

        $data = $request->validate($this->rules(), $this->messages());
        $document->update([
            'vehicle_id' => $data['vehicle_id'] ?? null,
            'title'      => $data['title'],
            'type'       => $data['type'],
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('client.documents.index')->with('success', 'Document mis à jour.');
    }

    /** Consultation dans le navigateur. */
    public function view(Document $document)
    {
        $this->authorizeOwner($document);

        return Storage::disk('local')->response(
            $document->file_path,
            $document->original_name,
            ['Content-Type' => $document->mime, 'X-Content-Type-Options' => 'nosniff']
        );
    }

    public function download(Document $document)
    {
        $this->authorizeOwner($document);

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(Document $document)
    {
        $this->authorizeOwner($document);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return redirect()->route('client.documents.index')->with('success', 'Document supprimé.');
    }

    private function authorizeOwner(Document $document): void
    {
        abort_unless($document->user_id === auth()->id(), 403);
    }

    private function rules(): array
    {
        return [
            'title'      => ['required', 'string', 'max:120'],
            'type'       => ['required', Rule::in(array_keys(Document::TYPES))],
            'vehicle_id' => ['nullable', Rule::exists('vehicles', 'id')->where('user_id', auth()->id())],
            'expires_at' => ['nullable', 'date'],
        ];
    }

    private function messages(): array
    {
        return [
            'file.required'  => 'Choisissez un fichier.',
            'file.mimes'     => 'Formats acceptés : PDF, JPG ou PNG.',
            'file.max'       => 'Le fichier ne doit pas dépasser 5 Mo.',
            'file.uploaded'  => "Échec de l'envoi : fichier trop lourd (5 Mo maximum).",
            'vehicle_id.exists' => 'Véhicule invalide.',
        ];
    }
}