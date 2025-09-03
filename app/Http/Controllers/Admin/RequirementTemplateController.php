<?php

// app/Http/Controllers/Admin/RequirementTemplateController.php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\RequirementItem;
use Illuminate\Support\Facades\DB;
use App\Models\RequirementTemplate;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class RequirementTemplateController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = RequirementTemplate::withCount(['items', 'certificationSchemes']);

            // Apply filters
            if ($request->filled('status_filter')) {
                $query->where('is_active', $request->status_filter);
            }

            if ($request->filled('used_filter')) {
                if ($request->used_filter == '1') {
                    $query->has('certificationSchemes');
                } else {
                    $query->doesntHave('certificationSchemes');
                }
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('description_display', function ($row) {
                    return $row->description ? '<span class="text-muted">' . \Illuminate\Support\Str::limit($row->description, 50) . '</span>' : '<span class="text-muted fst-italic">Tidak ada deskripsi</span>';
                })
                ->addColumn('items_count_display', function ($row) {
                    return '<span class="badge bg-info">' . $row->items_count . '</span>';
                })
                ->addColumn('schemes_count_display', function ($row) {
                    return '<span class="badge bg-primary">' . $row->certification_schemes_count . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    return $row->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>';
                })
                ->addColumn('action', function ($row) {
                    $actions = '<div class="btn-group btn-group-sm" role="group">';
                    $actions .= '<button type="button" class="btn btn-outline-info btn-sm preview-template" data-id="' . $row->id . '" title="Preview"><i class="bi bi-eye"></i></button>';
                    $actions .= '<a href="' . route('admin.requirements.edit', $row->id) . '" class="btn btn-outline-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>';
                    $toggleIcon = $row->is_active ? 'bi-toggle-on text-success' : 'bi-toggle-off text-secondary';
                    $toggleTitle = $row->is_active ? 'Nonaktifkan' : 'Aktifkan';
                    $actions .= '<a href="' . route('admin.requirements.toggle-status', $row->id) . '" class="btn btn-outline-secondary btn-sm toggle-status" data-active="' . $row->is_active . '" title="' . $toggleTitle . '"><i class="bi ' . $toggleIcon . '"></i></a>';
                    if ($row->certification_schemes_count == 0) {
                        $actions .= '<a href="' . route('admin.requirements.destroy', $row->id) . '" class="btn btn-outline-danger btn-sm delete-template" title="Hapus"><i class="bi bi-trash"></i></a>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['description_display', 'items_count_display', 'schemes_count_display', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.requirements.index');
    }

    public function create()
    {
        return view('admin.requirements.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirement_type' => 'required|in:all_required,choose_one,choose_min',
            'min_required' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.document_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.type' => 'required|in:file_upload,text_input,checkbox,select,number',
            'items.*.is_required' => 'boolean',
            'items.*.validation_rules' => 'nullable|array',
            'items.*.options' => 'nullable|array',
        ]);

        // Additional validation for choose_min type
        if ($request->requirement_type === 'choose_min') {
            $request->validate([
                'min_required' => 'required|integer|min:1|max:' . count($request->items),
            ], [
                'min_required.required' => 'Minimal dokumen wajib diisi untuk tipe "Pilih minimal beberapa dokumen".',
                'min_required.max' => 'Minimal dokumen tidak boleh lebih dari jumlah dokumen yang ada.',
            ]);
        }

        DB::transaction(function () use ($request) {
            $template = RequirementTemplate::create([
                'name' => $request->name,
                'description' => $request->description,
                'requirement_type' => $request->requirement_type,
                'min_required' => $request->requirement_type === 'choose_min' ? $request->min_required : null,
                'is_active' => true,
            ]);

            foreach ($request->items as $index => $item) {
                RequirementItem::create([
                    'template_id' => $template->id,
                    'document_name' => $item['document_name'],
                    'description' => $item['description'] ?? null,
                    'type' => $item['type'],
                    'validation_rules' => $item['validation_rules'] ?? null,
                    'options' => $item['options'] ?? null,
                    'is_required' => $item['is_required'] ?? true,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.requirements.index')->with('success', 'Template persyaratan berhasil dibuat');
    }

    public function show(Request $request, $id)
    {
        $template = RequirementTemplate::with([
            'items' => function ($query) {
                $query->orderBy('sort_order');
            },
            'certificationSchemes' => function ($query) {
                $query->with('field');
            }
        ])->findOrFail($id);

        // Jika request AJAX (untuk preview modal), return partial view
        if ($request->ajax()) {
            return view('admin.requirements.partials.preview', compact('template'));
        }

        // Jika request biasa, return full page
        return view('admin.requirements.show', compact('template'));
    }

    public function edit($id)
    {
        $template = RequirementTemplate::with([
            'items' => function ($query) {
                $query->orderBy('sort_order');
            },
        ])->findOrFail($id);

        return view('admin.requirements.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $requirementTemplate = RequirementTemplate::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'requirement_type' => 'required|in:all_required,choose_one,choose_min',
            'min_required' => 'nullable|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.document_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.type' => 'required|in:file_upload,text_input,checkbox,select,number',
            'items.*.is_required' => 'boolean',
            'items.*.validation_rules' => 'nullable|array',
            'items.*.options' => 'nullable|array',
        ]);

        // Additional validation for choose_min type
        if ($request->requirement_type === 'choose_min') {
            $request->validate([
                'min_required' => 'required|integer|min:1|max:' . count($request->items),
            ], [
                'min_required.required' => 'Minimal dokumen wajib diisi untuk tipe "Pilih minimal beberapa dokumen".',
                'min_required.max' => 'Minimal dokumen tidak boleh lebih dari jumlah dokumen yang ada.',
            ]);
        }

        DB::transaction(function () use ($request, $requirementTemplate) {
            $requirementTemplate->update([
                'name' => $request->name,
                'description' => $request->description,
                'requirement_type' => $request->requirement_type,
                'min_required' => $request->requirement_type === 'choose_min' ? $request->min_required : null,
            ]);

            // Hapus item lama
            $requirementTemplate->items()->delete();

            // Buat item baru
            foreach ($request->items as $index => $item) {
                RequirementItem::create([
                    'template_id' => $requirementTemplate->id,
                    'document_name' => $item['document_name'],
                    'description' => $item['description'] ?? null,
                    'type' => $item['type'],
                    'validation_rules' => $item['validation_rules'] ?? null,
                    'options' => $item['options'] ?? null,
                    'is_required' => $item['is_required'] ?? true,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.requirements.index')->with('success', 'Template persyaratan berhasil diupdate');
    }

    public function destroy($id)
    {
        $requirementTemplate = RequirementTemplate::findOrFail($id);
        
        // Cek apakah template sedang digunakan
        if ($requirementTemplate->certificationSchemes()->count() > 0) {
            return back()->with('error', 'Template tidak dapat dihapus karena sedang digunakan');
        }

        $requirementTemplate->delete();

        return redirect()->route('admin.requirements.index')->with('success', 'Template persyaratan berhasil dihapus');
    }

    public function toggleStatus($id)
{
    $requirementTemplate = RequirementTemplate::findOrFail($id);

    $requirementTemplate->update([
        'is_active' => !$requirementTemplate->is_active,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Status template berhasil diubah',
        'is_active' => $requirementTemplate->is_active,
    ]);
}


    public function getTemplate($id)
    {
        $template = RequirementTemplate::with(['activeItems'])->findOrFail($id);
        
        return response()->json([
            'template' => $template,
        ]);
    }

    public function getActiveTemplates()
    {
        $templates = RequirementTemplate::where('is_active', true)
            ->withCount('items')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'requirement_type', 'min_required']);

        return response()->json([
            'templates' => $templates
        ]);
    }

    public function apiIndex()
    {
        $templates = RequirementTemplate::where('is_active', true)
            ->withCount('items')
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'requirement_type', 'min_required']);

        return response()->json($templates);
    }
}