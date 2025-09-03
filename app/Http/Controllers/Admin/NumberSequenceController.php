<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NumberSequence;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class NumberSequenceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $sequences = NumberSequence::select([
                'id', 'sequence_key', 'name', 'prefix', 'suffix', 'digits', 
                'current_number', 'format_template', 'is_active', 'updated_at'
            ]);

            return DataTables::of($sequences)
                ->addIndexColumn()
                ->addColumn('status_badge', function ($seq) {
                    return $seq->is_active 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('current_preview', function ($seq) {
                    try {
                        $preview = $seq->generatePreview(1);
                        return '<code class="bg-light px-2 py-1 rounded text-primary">' . $preview[0] . '</code>';
                    } catch (\Exception $e) {
                        return '<span class="text-muted">Error</span>';
                    }
                })
                ->addColumn('sequence_info', function ($seq) {
                    $info = '';
                    if ($seq->prefix) $info .= '<span class="badge bg-primary me-1">' . $seq->prefix . '</span>';
                    $info .= '<span class="badge bg-info me-1">' . $seq->digits . ' digits</span>';
                    if ($seq->suffix) $info .= '<span class="badge bg-secondary">' . $seq->suffix . '</span>';
                    return $info;
                })
                ->addColumn('action', function ($seq) {
                    $testBtn = '<button type="button" class="btn btn-sm btn-outline-info me-1" 
                        onclick="testSequence(' . $seq->id . ')" title="Test Generate">
                        <i class="bi bi-play"></i>
                    </button>';
                    
                    $editBtn = '<a href="' . route('admin.number-sequences.edit', $seq->id) . '" 
                        class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
                    
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="deleteSequence(' . $seq->id . ')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>';
                    
                    return $testBtn . $editBtn . $deleteBtn;
                })
                ->rawColumns(['status_badge', 'current_preview', 'sequence_info', 'action'])
                ->make(true);
        }

        return view('admin.number-sequences.index');
    }

    public function create()
    {
        return view('admin.number-sequences.create');
    }

    public function store(Request $request)
    {
        $request->validate(NumberSequence::validationRules());

        $sequence = NumberSequence::create($request->all());

        // Generate sample output
        $samples = $sequence->generatePreview(3);
        $sequence->update(['sample_output' => $samples]);

        return redirect()->route('admin.number-sequences.index')
            ->with('success', 'Number sequence created successfully.');
    }

    public function edit(NumberSequence $numberSequence)
    {
        return view('admin.number-sequences.edit', compact('numberSequence'));
    }

    public function update(Request $request, NumberSequence $numberSequence)
    {
        $request->validate(NumberSequence::validationRules($numberSequence->id));

        $numberSequence->update($request->all());

        // Update sample output
        $samples = $numberSequence->generatePreview(3);
        $numberSequence->update(['sample_output' => $samples]);

        return redirect()->route('admin.number-sequences.index')
            ->with('success', 'Number sequence updated successfully.');
    }

    public function destroy(NumberSequence $numberSequence)
    {
        $numberSequence->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Number sequence deleted successfully.'
        ]);
    }

    public function test(NumberSequence $numberSequence)
    {
        try {
            $previews = $numberSequence->generatePreview(5);
            
            return response()->json([
                'success' => true,
                'previews' => $previews,
                'current_number' => $numberSequence->current_number,
                'next_number' => $numberSequence->current_number + 1
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function generate(Request $request)
    {
        $request->validate([
            'sequence_key' => 'required|string|exists:number_sequences,sequence_key'
        ]);

        try {
            $number = NumberSequence::generate($request->sequence_key);
            
            return response()->json([
                'success' => true,
                'generated_number' => $number
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
