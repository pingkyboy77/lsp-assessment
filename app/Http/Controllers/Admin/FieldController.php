<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Field::query(); // <-- query builder, bukan get()

            // Apply filters
            if ($request->filled('status_filter')) {
                $query->where('is_active', $request->status_filter);
            }

            if ($request->filled('kbbli_filter')) {
                $query->where('kbbli_bidang', 'like', "%{$request->kbbli_filter}%");
            }

            return datatables()
                ->of($query) // langsung pakai query builder
                ->addIndexColumn()
                ->addColumn('kode_bidang_badge', function ($field) {
                    return '<span class="">' . $field->kode_bidang . '</span>';
                })
                ->addColumn('code_2_badge', function ($field) {
                    return '<span class="">' . $field->code_2 . '</span>';
                })
                ->addColumn('bidang_display', function ($field) {
                    $html = '<strong>' . $field->bidang . '</strong>';
                    if ($field->kode_web) {
                        $html .= '<br><small class="text-muted">Web: ' . $field->kode_web . '</small>';
                    }
                    return $html;
                })
                ->addColumn('status_badge', function ($field) {
                    return $field->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-danger">Tidak Aktif</span>';
                })
                ->addColumn('action', function ($field) {
                    $buttons = '<div class="btn-group" role="group">';
                    $buttons .= '<a href="' . route('admin.fields.edit', $field) . '" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>';

                    if ($field->is_active) {
                        $buttons .= '<form action="' . route('admin.fields.toggle-status', $field) . '" method="POST" class="d-inline">' . csrf_field() . method_field('PATCH') . '<button type="submit" class="btn btn-sm btn-outline-success" title="Nonaktifkan"><i class="bi bi-toggle-on"></i></button></form>';
                    } else {
                        $buttons .= '<form action="' . route('admin.fields.toggle-status', $field) . '" method="POST" class="d-inline">' . csrf_field() . method_field('PATCH') . '<button type="submit" class="btn btn-sm btn-outline-secondary" title="Aktifkan"><i class="bi bi-toggle-off"></i></button></form>';
                    }

                    $buttons .= '<form action="' . route('admin.fields.destroy', $field) . '" method="POST" class="d-inline" onsubmit="return confirm(\'Yakin ingin menghapus bidang ini?\')">' . csrf_field() . method_field('DELETE') . '<button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>';

                    $buttons .= '</div>';

                    return $buttons;
                })
                ->rawColumns(['kode_bidang_badge', 'code_2_badge', 'bidang_display', 'status_badge', 'action'])
                ->make(true);
        }

        return view('admin.fields.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.fields.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_bidang' => 'required|string|max:20|unique:fields,kode_bidang',
            'code_2' => 'required|string|max:10|unique:fields,code_2',
            'bidang' => 'required|string|max:255',
            'bidang_ing' => 'nullable|string|max:255',
            'kbbli_bidang' => 'nullable|string|max:20',
            'kode_web' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            Field::create($request->all());

            return redirect()->route('admin.fields.index')->with('success', 'Bidang berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menyimpan data.')->withInput();
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Field $field)
    {
        return view('admin.fields.edit', compact('field'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Field $field)
    {
        $validator = Validator::make($request->all(), [
            'kode_bidang' => 'required|string|max:20|unique:fields,kode_bidang,' . $field->id,
            'code_2' => 'required|string|max:10|unique:fields,code_2,' . $field->id,
            'bidang' => 'required|string|max:255',
            'bidang_ing' => 'nullable|string|max:255',
            'kbbli_bidang' => 'nullable|string|max:20',
            'kode_web' => 'nullable|string|max:20',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $field->update($request->all());

            return redirect()->route('admin.fields.index')->with('success', 'Bidang berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memperbarui data.')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Field $field)
    {
        try {
            // Check if field has certification schemes
            if ($field->certificationSchemes()->count() > 0) {
                return redirect()->back()->with('error', 'Bidang tidak dapat dihapus karena masih memiliki skema sertifikasi.');
            }

            $field->delete();

            return redirect()->route('admin.fields.index')->with('success', 'Bidang berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Field $field)
    {
        try {
            $field->update(['is_active' => !$field->is_active]);

            $status = $field->is_active ? 'diaktifkan' : 'dinonaktifkan';
            return redirect()
                ->back()
                ->with('success', "Bidang berhasil {$status}.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengubah status.');
        }
    }

    /**
     * Get fields for API/AJAX
     */
    public function getFields(Request $request)
    {
        $query = Field::active();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bidang', 'like', "%{$search}%")->orWhere('code_2', 'like', "%{$search}%");
            });
        }

        $fields = $query->select('id', 'code_2', 'bidang', 'full_name')->orderBy('bidang')->get();

        return response()->json($fields);
    }
}
