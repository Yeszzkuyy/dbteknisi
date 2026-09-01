<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProjectDocumentController extends Controller
{
    public function index(Project $project)
    {
        $this->authorize('view', $project);

        $documents = $project->documents()  // ← pakai relasi dari model Project
            ->with(['category', 'uploader'])
            ->latest()
            ->get();

        $categories = DocumentCategory::all();

        return view('project_documents.index', compact('project', 'documents', 'categories'));
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $request->validate([
            'file' => ['required', 'file', 'max:20480', \App\Rules\SecureFile::documents()],
            'document_category_id' => 'nullable|exists:document_categories,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $originalName = \App\Rules\SecureFile::sanitizeName($file->getClientOriginalName());
        $path = $file->storeAs(
            'documents/' . $project->id,
            Str::uuid() . '.' . strtolower($file->getClientOriginalExtension()),
            'private'
        );

        $project->documents()->create([
            'file_name' => $originalName,
            'file_path' => $path,
            'file_size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'notes' => $request->notes,
            'document_category_id' => $request->document_category_id,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()
            ->back()
            ->with('success', 'Dokumen berhasil diupload.');
    }

    public function preview(ProjectDocument $document)
    {
        $this->authorize('view', $document->project);

        $filePath = Storage::disk('private')->path($document->file_path);

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = $document->mime_type ?? mime_content_type($filePath);
        $extension = strtolower(pathinfo($document->file_name, PATHINFO_EXTENSION));

        // Untuk gambar, PDF, video, audio → tampilkan langsung di browser
        $inlineTypes = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'mp4', 'webm', 'ogg', 'mp3', 'wav'];

        if (in_array($extension, $inlineTypes)) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $document->file_name . '"'
            ]);
        }

        // Untuk Office (Word, Excel, PPT) → DOWNLOAD (karena Google Docs Viewer ga support IP lokal)
        $officeTypes = ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
        if (in_array($extension, $officeTypes)) {
            return Storage::disk('private')->download($document->file_path, $document->file_name);
        }

        // Default: download
        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    public function download(ProjectDocument $document)
    {
        $this->authorize('view', $document->project);

        // Cek apakah file ada di storage
        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->download($document->file_path, $document->file_name);
    }

    public function destroy(ProjectDocument $document)
    {
        $this->authorize('update', $document->project);

        // Hapus file dari storage kalau ada
        if ($document->file_path && Storage::disk('private')->exists($document->file_path)) {
            Storage::disk('private')->delete($document->file_path);
        }

        // Force delete (hapus permanen dari database)
        $document->forceDelete();

        return redirect()
            ->back()
            ->with('success', 'Dokumen berhasil dihapus.');
    }
}
