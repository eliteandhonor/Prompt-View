<?php

namespace App\Http\Controllers;

use App\Models\Prompt;
use Illuminate\Http\Request;

class PromptController extends Controller
{
    // Display a listing of the resource.
    public function index(Request $request)
    {
        // Always define $search as a string (default: '')
        $search = (string) $request->input('search', '');

        $query = Prompt::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        $prompts = $query->orderByDesc('id')->paginate(10)->withQueryString();
        $total = $query->count();

        // Build promptArray for JS (for dashboard initialization)
        $promptArray = [];
        foreach ($prompts as $p) {
            $promptArray[] = [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'created_at' => $p->created_at ? $p->created_at->toDateTimeString() : '',
                'show_url' => route('prompts.show', $p->id),
                'edit_url' => route('prompts.edit', $p->id),
                'delete_url' => route('prompts.destroy', $p->id) . ($search !== '' ? '?search=' . urlencode($search) : ''),
                'csrf' => csrf_token(),
            ];
        }

        // Always define $toast with all expected keys and safe defaults
        $toast = [
            'success' => [
                'show' => session()->has('success'),
                'message' => session('success', ''),
            ],
            'error' => [
                'show' => session()->has('error'),
                'message' => session('error', ''),
            ],
        ];
        // Ensure all keys exist (in case session is missing)
        foreach (['success', 'error'] as $type) {
            if (!isset($toast[$type]['show'])) $toast[$type]['show'] = false;
            if (!isset($toast[$type]['message'])) $toast[$type]['message'] = '';
        }

        // Always define $importModalOpen: from session or request (if set), else false
        $importModalOpen = false;
        if ($request->has('importModalOpen')) {
            $importModalOpen = (bool) $request->input('importModalOpen');
        } elseif (session()->has('importModalOpen')) {
            $importModalOpen = (bool) session('importModalOpen');
        }

        // Always define $promptArray as array
        if (!is_array($promptArray)) {
            $promptArray = [];
        }

        return view('prompts.index', [
            'prompts' => $prompts,
            'search' => $search,
            'total' => $total,
            'promptArray' => $promptArray,
            'toast' => $toast,
            'importModalOpen' => $importModalOpen,
        ]);
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('prompts.create');
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        try {
            $prompt = Prompt::create($validated);
            return redirect()->route('prompts.index')->with('success', 'Prompt created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('prompts.index')->with('error', 'Failed to create prompt: ' . $e->getMessage());
        }
    }

    // Display the specified resource.
    public function show(Prompt $prompt)
    {
        return view('prompts.show', compact('prompt'));
    }

    // Show the form for editing the specified resource.
    public function edit(Prompt $prompt)
    {
        return view('prompts.edit', compact('prompt'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Prompt $prompt)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);
        $prompt->update($validated);
        return redirect()->route('prompts.show', $prompt)->with('success', 'Prompt updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Prompt $prompt)
    {
        try {
            $prompt->delete();
            return redirect()->route('prompts.index')->with('success', 'Prompt deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('prompts.index')->with('error', 'Failed to delete prompt: ' . $e->getMessage());
        }
    }
    /**
     * Handle upload/import of prompts from CSV or JSON.
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,json',
        ]);

        $file = $request->file('import_file');
        $ext = strtolower($file->getClientOriginalExtension());
        $imported = 0;
        $errors = [];
        $createdPrompts = [];

        try {
            if ($ext === 'csv' || $file->getMimeType() === 'text/csv' || $file->getMimeType() === 'text/plain') {
                // CSV handling
                $handle = fopen($file->getRealPath(), 'r');
                if ($handle === false) {
                    $msg = 'Unable to open uploaded CSV file.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 400);
                    }
                    return redirect()->route('prompts.index')->with('error', $msg);
                }
                $header = fgetcsv($handle);
                if (!$header || count($header) < 2 || strtolower(trim($header[0])) !== 'title' || strtolower(trim($header[1])) !== 'description') {
                    fclose($handle);
                    $msg = 'CSV must have headers: title,description.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return redirect()->route('prompts.index')->with('error', $msg);
                }
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) < 2) continue;
                    $title = trim($row[0]);
                    $description = trim($row[1]);
                    if ($title === '' || $description === '') {
                        $errors[] = "Missing title or description in a row.";
                        continue;
                    }
                    $prompt = Prompt::create(['title' => $title, 'description' => $description]);
                    $createdPrompts[] = [
                        'id' => $prompt->id,
                        'title' => $prompt->title,
                        'description' => $prompt->description,
                        'created_at' => $prompt->created_at ? $prompt->created_at->toDateTimeString() : '',
                        'show_url' => route('prompts.show', $prompt->id),
                        'edit_url' => route('prompts.edit', $prompt->id),
                        'delete_url' => route('prompts.destroy', $prompt->id),
                        'csrf' => csrf_token(),
                    ];
                    $imported++;
                }
                fclose($handle);
            } elseif ($ext === 'json' || $file->getMimeType() === 'application/json') {
                $json = file_get_contents($file->getRealPath());
                $data = json_decode($json, true);
                if (!is_array($data)) {
                    $msg = 'Uploaded JSON is not a valid array.';
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json(['success' => false, 'message' => $msg], 422);
                    }
                    return redirect()->route('prompts.index')->with('error', $msg);
                }
                foreach ($data as $idx => $item) {
                    if (!is_array($item) || !isset($item['title'], $item['description'])) {
                        $errors[] = "Missing title or description in JSON object at index $idx.";
                        continue;
                    }
                    $title = trim($item['title']);
                    $description = trim($item['description']);
                    if ($title === '' || $description === '') {
                        $errors[] = "Empty title or description in JSON object at index $idx.";
                        continue;
                    }
                    $prompt = Prompt::create(['title' => $title, 'description' => $description]);
                    $createdPrompts[] = [
                        'id' => $prompt->id,
                        'title' => $prompt->title,
                        'description' => $prompt->description,
                        'created_at' => $prompt->created_at ? $prompt->created_at->toDateTimeString() : '',
                        'show_url' => route('prompts.show', $prompt->id),
                        'edit_url' => route('prompts.edit', $prompt->id),
                        'delete_url' => route('prompts.destroy', $prompt->id),
                        'csrf' => csrf_token(),
                    ];
                    $imported++;
                }
            } else {
                $msg = 'Unsupported file type. Please upload CSV or JSON.';
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => $msg], 415);
                }
                return redirect()->route('prompts.index')->with('error', $msg);
            }
        } catch (\Exception $e) {
            $msg = 'Import failed: ' . $e->getMessage();
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return redirect()->route('prompts.index')->with('error', $msg);
        }

        if ($imported === 0) {
            $msg = 'No prompts were imported.';
            if ($errors) $msg .= ' Errors: ' . implode(' ', $errors);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg, 'errors' => $errors], 400);
            }
            return redirect()->route('prompts.index')->with('error', $msg);
        }

        $msg = "$imported prompts imported successfully.";
        if ($errors) $msg .= ' Some rows/objects were skipped: ' . implode(' ', $errors);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'imported' => $imported,
                'errors' => $errors,
                'prompts' => $createdPrompts
            ]);
        }

        return redirect()->route('prompts.index')->with('success', $msg);
    }

    /**
     * Export all prompts as a downloadable CSV file.
     */
    public function exportCsv()
    {
        $prompts = Prompt::all(['title', 'description']);
        $filename = 'prompts_export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($prompts) {
            $out = fopen('php://output', 'w');
            // Write CSV header
            fputcsv($out, ['title', 'description']);
            // Write each prompt
            foreach ($prompts as $prompt) {
                fputcsv($out, [$prompt->title, $prompt->description]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export all prompts as a downloadable JSON file.
     */
    public function exportJson()
    {
        $prompts = Prompt::all(['title', 'description']);
        $filename = 'prompts_export_' . date('Ymd_His') . '.json';

        return response()->json(
            $prompts,
            200,
            [
                'Content-Type' => 'application/json',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );
    }
}