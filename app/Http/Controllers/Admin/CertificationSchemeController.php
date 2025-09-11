<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CertificationScheme;
use App\Models\Field;
use App\Models\RequirementTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;

class CertificationSchemeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CertificationScheme::with('field');

            // Apply filters
            if ($request->filled('status_filter')) {
                $query->where('is_active', $request->status_filter);
            }

            if ($request->filled('field_filter')) {
                $query->where('code_2', $request->field_filter);
            }

            if ($request->filled('jenjang_filter')) {
                $query->where('jenjang', $request->jenjang_filter);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('code_1_badge', function ($row) {
                    return '<span class="text-dark fw-bold">' . $row->code_1 . '</span>';
                })
                ->addColumn('nama_display', function ($row) {
                    $html = '<div class="fw-bold text-dark">' . $row->nama . '</div>';
                    if ($row->skema_ing) {
                        $html .= '<small class="text-muted">' . $row->skema_ing . '</small>';
                    }
                    return $html;
                })
                ->addColumn('field_display', function ($row) {
                    if ($row->field) {
                        return '<div class="fw-semibold">' . $row->field->bidang . '</div>' .
                               '<small class="text-muted">Kode: ' . $row->code_2 . '</small>';
                    }
                    return '<span class="text-muted">-</span>';
                })
                ->addColumn('jenjang_badge', function ($row) {
                    $colors = [
                        'Utama' => 'danger',
                        'Madya' => 'warning',
                        'Menengah' => 'info',
                    ];
                    $color = $colors[$row->jenjang] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . $row->jenjang . '</span>';
                })
                ->addColumn('status_badge', function ($row) {
                    $badgeClass = $row->is_active ? 'bg-success' : 'bg-danger';
                    $statusText = $row->is_active ? 'Aktif' : 'Tidak Aktif';
                    $icon = $row->is_active ? 'bi-check-circle' : 'bi-x-circle';
                    
                    return '<span class="badge ' . $badgeClass . '">' .
                           '<i class="bi ' . $icon . ' me-1"></i>' . $statusText .
                           '</span>';
                })
                ->addColumn('action', function ($row) {
                    $statusAction = $row->is_active 
                        ? '<a href="' . route('admin.certification-schemes.toggle-status', $row->id) . '" 
                             class="btn btn-outline-success btn-xs toggle-status me-1" 
                             data-active="1" title="Nonaktifkan">
                             <i class="bi bi-toggle-on"></i>
                           </a>'
                        : '<a href="' . route('admin.certification-schemes.toggle-status', $row->id) . '" 
                             class="btn btn-outline-secondary btn-xs toggle-status me-1" 
                             data-active="0" title="Aktifkan">
                             <i class="bi bi-toggle-off"></i>
                           </a>';

                    return '
                    <div class="btn-group" role="group">
                        <!-- Requirements Button -->
                        <a href="' . route('admin.certification-schemes.requirements', $row->id) . '" 
                           class="btn btn-outline-success btn-xs me-1" title="Requirements/Persyaratan">
                            <i class="bi bi-file-earmark-check"></i>
                        </a>
                        
                        <!-- Unit Kompetensi Button -->
                        <a href="' . route('admin.schemes.unit-kompetensi.index', $row->id) . '" 
                           class="btn btn-outline-info btn-xs me-1" title="Unit Kompetensi">
                            <i class="bi bi-list-check"></i>
                        </a>
                        
                        <!-- Kelompok Kerja Button -->
                        <a href="' . route('admin.schemes.kelompok-kerja.index', $row->id) . '" 
                           class="btn btn-outline-secondary btn-xs me-1" title="Kelompok Kerja">
                            <i class="bi bi-people"></i>
                        </a>
                        
                        <!-- View Button -->
                        <a href="' . route('admin.certification-schemes.show', $row->id) . '" 
                           class="btn btn-outline-primary btn-xs me-1" title="Detail">
                            <i class="bi bi-eye"></i>
                        </a>
                        
                        <!-- Edit Button -->
                        <a href="' . route('admin.certification-schemes.edit', $row->id) . '" 
                           class="btn btn-outline-warning btn-xs me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        
                        <!-- Status Toggle -->
                        ' . $statusAction . '
                        
                        <!-- Delete Button -->
                        <form id="delete-form-' . $row->id . '" action="' . route('admin.certification-schemes.destroy', $row->id) . '" 
              method="POST" style="display: none;">
            ' . csrf_field() . '
            ' . method_field('DELETE') . '
        </form>
        <button type="button" class="btn btn-outline-danger btn-xs delete-btn" 
                data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->nama, ENT_QUOTES) . '" title="Hapus">
            <i class="bi bi-trash"></i>
        </button>
                    </div>
                    ';
                })
                ->rawColumns(['code_1_badge', 'nama_display', 'field_display', 'jenjang_badge', 'status_badge', 'action'])
                ->make(true);
        }

        $fields = Field::orderBy('bidang')->get();
        return view('admin.certification-schemes.index', compact('fields'));
    }

    public function create()
{
    $fields = Field::orderBy('bidang')->get();
    $templates = $this->prepareTemplatesForView();
    
    return view('admin.certification-schemes.create', compact('fields', 'templates'));
}

    public function store(Request $request)
    {
        $request->validate([
            'code_1' => 'required|string|max:50|unique:certification_schemes,code_1',
            'nama' => 'required|string|max:255',
            'skema_ing' => 'nullable|string|max:255',
            'code_2' => 'required|exists:fields,id',
            'jenjang' => 'required|in:Utama,Madya,Menengah',
            'is_active' => 'boolean'
        ]);

        try {
            $certificationScheme = CertificationScheme::create([
                'code_1' => $request->code_1,
                'nama' => $request->nama,
                'skema_ing' => $request->skema_ing,
                'code_2' => $request->code_2,
                'jenjang' => $request->jenjang,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()
                ->route('admin.certification-schemes.index')
                ->with('success', 'Skema sertifikasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            Log::error('Error creating certification scheme: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function show(CertificationScheme $certificationScheme)
{
    $scheme = $certificationScheme->load([
        'field',
        'unitKompetensis.elemenKompetensis.kriteriaKerjas',
        'kelompokKerjas.buktiPortofolios',
        'requirementTemplate',
        'requirementTemplates.items' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }
    ]);

    // Calculate statistics
    $scheme->requirement_templates_count = $scheme->requirementTemplates->count() + ($scheme->requirementTemplate ? 1 : 0);
    $scheme->unit_kompetensi_count = $scheme->unitKompetensis->count();
    $scheme->kelompok_kerja_count = $scheme->kelompokKerjas->count();
    // $scheme->total_kriteria_count = DB::table('kriteria_kerjas')
    // ->join('elemen_kompetensis', 'kriteria_kerjas.elemen_kompetensi_id', '=', 'elemen_kompetensis.id')
    // ->join('unit_kompetensis', 'elemen_kompetensis.unit_kompetensi_id', '=', 'unit_kompetensis.id')
    // ->where('unit_kompetensis.scheme_id', $scheme->id)
    // ->count();
    // Add this line for kriteria count
    $scheme->total_kriteria_count = $scheme->unitKompetensis->sum(function($unit) {
        return $unit->elemenKompetensis->sum(function($elemen) {
            return $elemen->kriteriaKerjas->count();
        });
    });
    $scheme->total_bukti_portofolio_count = $scheme->kelompokKerjas->sum(function($kelompok) {
        return $kelompok->buktiPortofolios->count();
    });

    // Add jenjang color for badge
    $jenjangColors = [
        'Utama' => 'danger',
        'Madya' => 'warning', 
        'Menengah' => 'info'
    ];
    $scheme->jenjang_color = $jenjangColors[$scheme->jenjang] ?? 'secondary';

    return view('admin.certification-schemes.show', compact('scheme'));
}

    public function edit(CertificationScheme $certificationScheme)
{
    $fields = Field::orderBy('bidang')->get();
    
    // Load existing templates for the certification scheme
    $certificationScheme->load([
        'requirementTemplates' => function($query) {
            $query->withPivot('is_active', 'sort_order')->orderByPivot('sort_order');
        },
        'requirementTemplates.items' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }
    ]);
    
    $templates = $this->prepareTemplatesForView();
    
    return view('admin.certification-schemes.edit', compact('certificationScheme', 'fields', 'templates'));
}

private function prepareTemplatesForView()
{
    $templates = RequirementTemplate::with(['items' => function($query) {
        $query->where('is_active', true)->orderBy('sort_order');
    }])->where('is_active', true)->orderBy('name')->get();

    // Add template count and display properties to each template
    foreach ($templates as $template) {
        $template->items_count = $template->items->count();
        
        // Add type display for better readability
        switch ($template->requirement_type) {
            case 'all_required':
                $template->type_display = 'Semua Wajib';
                break;
            case 'choose_one':
                $template->type_display = 'Pilih Satu';
                break;
            case 'choose_min':
                $template->type_display = 'Pilih Minimal';
                break;
            default:
                $template->type_display = 'Tidak Diketahui';
        }

        // Add requirement description for better understanding
        switch ($template->requirement_type) {
            case 'all_required':
                $template->requirement_description = 'User harus mengupload semua ' . $template->items_count . ' dokumen';
                break;
            case 'choose_one':
                $template->requirement_description = 'User pilih 1 dari ' . $template->items_count . ' dokumen';
                break;
            case 'choose_min':
                $template->requirement_description = 'User pilih minimal beberapa dari ' . $template->items_count . ' dokumen';
                break;
            default:
                $template->requirement_description = 'Tipe requirement tidak diketahui';
        }
    }
    
    return $templates;
}

    public function update(Request $request, CertificationScheme $certificationScheme)
    {
        $request->validate([
            'code_1' => 'required|string|max:50|unique:certification_schemes,code_1,' . $certificationScheme->id,
            'nama' => 'required|string|max:255',
            'skema_ing' => 'nullable|string|max:255',
            'code_2' => 'required|exists:fields,id',
            'jenjang' => 'required|in:Utama,Madya,Menengah',
            'is_active' => 'boolean'
        ]);

        try {
            $certificationScheme->update([
                'code_1' => $request->code_1,
                'nama' => $request->nama,
                'skema_ing' => $request->skema_ing,
                'code_2' => $request->code_2,
                'jenjang' => $request->jenjang,
                'is_active' => $request->has('is_active') ? true : false,
            ]);

            return redirect()
                ->route('admin.certification-schemes.index')
                ->with('success', 'Skema sertifikasi berhasil diperbarui.');
        } catch (\Exception $e) {
            Log::error('Error updating certification scheme: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(CertificationScheme $certificationScheme)
    {
        try {
            // Check if scheme has related data
            $hasRelatedData = $certificationScheme->unitKompetensis()->exists() || 
                            $certificationScheme->kelompokKerjas()->exists() ||
                            $certificationScheme->requirementTemplates()->exists();

            if ($hasRelatedData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus skema sertifikasi karena masih memiliki data terkait (Unit Kompetensi, Kelompok Kerja, atau Template Persyaratan).'
                ], 422);
            }

            $schemeName = $certificationScheme->nama;
            $certificationScheme->delete();

            // return response()->json([
            //     'success' => true,
            //     'message' => "Skema sertifikasi '{$schemeName}' berhasil dihapus."
            // ]);
            return redirect()
                ->route('admin.certification-schemes.index')
                ->with('success', 'Skema sertifikasi berhasil diha[us.');
        } catch (\Exception $e) {
            Log::error('Error deleting certification scheme: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }

    public function toggleStatus(CertificationScheme $certificationScheme)
    {
        try {
            $certificationScheme->update([
                'is_active' => !$certificationScheme->is_active
            ]);

            $statusText = $certificationScheme->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return response()->json([
                'success' => true,
                'message' => "Status skema sertifikasi berhasil {$statusText}.",
                'is_active' => $certificationScheme->is_active
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling certification scheme status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status.'
            ], 500);
        }
    }

    public function requirements(CertificationScheme $certificationScheme)
    {
        $certificationScheme->load([
            'field',
            'requirementTemplate',
            'requirementTemplates' => function($query) {
                $query->withPivot('is_active', 'sort_order')->orderByPivot('sort_order');
            },
            'requirementTemplates.items' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }
        ]);

        // Get all available templates for the manage modal
        $templates = RequirementTemplate::with(['items' => function($query) {
            $query->where('is_active', true)->orderBy('sort_order');
        }])->where('is_active', true)->orderBy('name')->get();

        // Add template count to each template for better UX
        foreach ($templates as $template) {
            $template->items_count = $template->items->count();
            
            // Add type display for better readability
            switch ($template->requirement_type) {
                case 'all_required':
                    $template->type_display = 'Semua Wajib';
                    break;
                case 'choose_one':
                    $template->type_display = 'Pilih Satu';
                    break;
                case 'choose_min':
                    $template->type_display = 'Pilih Minimal';
                    break;
                default:
                    $template->type_display = 'Tidak Diketahui';
            }

            // Add requirement description for better understanding
            switch ($template->requirement_type) {
                case 'all_required':
                    $template->requirement_description = 'User harus mengupload semua ' . $template->items_count . ' dokumen';
                    break;
                case 'choose_one':
                    $template->requirement_description = 'User pilih 1 dari ' . $template->items_count . ' dokumen';
                    break;
                case 'choose_min':
                    $template->requirement_description = 'User pilih minimal beberapa dari ' . $template->items_count . ' dokumen';
                    break;
                default:
                    $template->requirement_description = 'Tipe requirement tidak diketahui';
            }
        }

        // Calculate statistics for the scheme
        $certificationScheme->requirement_templates_count = $certificationScheme->requirementTemplates->count();
        $certificationScheme->total_required_documents = $this->calculateTotalRequiredDocuments($certificationScheme);

        return view('admin.certification-schemes.requirements', compact('certificationScheme', 'templates'));
    }

    public function updateRequirements(Request $request, CertificationScheme $certificationScheme)
    {
        $request->validate([
            'requirement_template_id' => 'nullable|exists:requirement_templates,id',
            'requirement_templates' => 'nullable|array',
            'requirement_templates.*' => 'exists:requirement_templates,id'
        ]);

        try {
            DB::beginTransaction();

            // Update single template (backward compatibility)
            $certificationScheme->update([
                'requirement_template_id' => $request->requirement_template_id
            ]);

            // Update multiple templates
            if ($request->has('requirement_templates')) {
                $templateData = [];
                foreach ($request->requirement_templates as $index => $templateId) {
                    $templateData[$templateId] = [
                        'is_active' => true,
                        'sort_order' => $index + 1,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $certificationScheme->requirementTemplates()->sync($templateData);
            } else {
                // If no templates selected, remove all
                $certificationScheme->requirementTemplates()->detach();
            }

            DB::commit();

            return redirect()
                ->route('admin.certification-schemes.requirements', $certificationScheme)
                ->with('success', 'Template persyaratan berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating requirements: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui template persyaratan.');
        }
    }

    public function resetRequirements(CertificationScheme $certificationScheme)
    {
        try {
            DB::beginTransaction();

            // Reset single template (backward compatibility)
            $certificationScheme->update([
                'requirement_template_id' => null
            ]);

            // Remove all multiple templates
            $certificationScheme->requirementTemplates()->detach();

            DB::commit();

            return redirect()
                ->route('admin.certification-schemes.requirements', $certificationScheme)
                ->with('success', 'Semua template persyaratan berhasil direset.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error resetting requirements: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'Terjadi kesalahan saat mereset template persyaratan.');
        }
    }

    public function getTemplateDetails(RequirementTemplate $template)
    {
        try {
            $template->load(['items' => function($query) {
                $query->where('is_active', true)->orderBy('sort_order');
            }]);

            // Add display properties
            switch ($template->requirement_type) {
                case 'all_required':
                    $template->type_display = 'Semua Wajib';
                    $template->requirement_description = 'User harus mengupload semua ' . $template->items->count() . ' dokumen';
                    break;
                case 'choose_one':
                    $template->type_display = 'Pilih Satu';
                    $template->requirement_description = 'User pilih 1 dari ' . $template->items->count() . ' dokumen';
                    break;
                case 'choose_min':
                    $template->type_display = 'Pilih Minimal';
                    $template->requirement_description = 'User pilih minimal beberapa dari ' . $template->items->count() . ' dokumen';
                    break;
                default:
                    $template->type_display = 'Tidak Diketahui';
                    $template->requirement_description = 'Tipe requirement tidak diketahui';
            }

            $template->items_count = $template->items->count();

            return response()->json([
                'success' => true,
                'template' => $template
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting template details: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil detail template.'
            ], 500);
        }
    }

    private function calculateTotalRequiredDocuments(CertificationScheme $scheme)
    {
        $total = 0;

        // Add from backward compatibility template
        if ($scheme->requirementTemplate) {
            switch ($scheme->requirementTemplate->requirement_type) {
                case 'all_required':
                    $total += $scheme->requirementTemplate->activeItems->count();
                    break;
                case 'choose_one':
                    $total += 1;
                    break;
                case 'choose_min':
                    // This would depend on the minimum required, for now assume 1
                    $total += 1;
                    break;
            }
        }

        // Add from multiple templates
        foreach ($scheme->requirementTemplates as $template) {
            switch ($template->requirement_type) {
                case 'all_required':
                    $total += $template->activeItems->count();
                    break;
                case 'choose_one':
                    $total += 1;
                    break;
                case 'choose_min':
                    // This would depend on the minimum required, for now assume 1
                    $total += 1;
                    break;
            }
        }

        return $total;
    }
}