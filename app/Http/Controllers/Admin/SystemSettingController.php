<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SystemSettingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $settings = SystemSetting::with(['creator', 'updater'])
                ->select(['id', 'key', 'value', 'type', 'group', 'description', 'is_active', 'created_by', 'updated_at']);

            return DataTables::of($settings)
                ->addIndexColumn()
                ->addColumn('type_badge', function ($setting) {
                    return $setting->type_badge;
                })
                ->addColumn('status_badge', function ($setting) {
                    return $setting->status_badge;
                })
                ->addColumn('formatted_value', function ($setting) {
                    $value = $setting->value;
                    if (strlen($value) > 50) {
                        return '<span title="' . htmlspecialchars($value) . '">' 
                            . htmlspecialchars(substr($value, 0, 50)) . '...</span>';
                    }
                    return htmlspecialchars($value);
                })
                ->addColumn('action', function ($setting) {
                    $editBtn = '<a href="' . route('admin.system-settings.edit', $setting->id) . '" 
                        class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>';
                    
                    $deleteBtn = '<button type="button" class="btn btn-sm btn-outline-danger" 
                        onclick="deleteSetting(' . $setting->id . ')" title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>';
                    
                    return $editBtn . $deleteBtn;
                })
                ->rawColumns(['type_badge', 'status_badge', 'formatted_value', 'action'])
                ->make(true);
        }

        return view('admin.system-settings.index');
    }

    public function create()
    {
        $groups = SystemSetting::distinct()->pluck('group')->filter();
        return view('admin.system-settings.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate(SystemSetting::validationRules());

        $setting = SystemSetting::create([
            'key' => $request->key,
            'value' => $this->formatValue($request->value, $request->type),
            'type' => $request->type,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.system-settings.index')
            ->with('success', 'System setting created successfully.');
    }

    public function edit(SystemSetting $systemSetting)
    {
        $groups = SystemSetting::distinct()->pluck('group')->filter();
        return view('admin.system-settings.edit', compact('systemSetting', 'groups'));
    }

    public function update(Request $request, SystemSetting $systemSetting)
    {
        $request->validate(SystemSetting::validationRules($systemSetting->id));

        $systemSetting->update([
            'key' => $request->key,
            'value' => $this->formatValue($request->value, $request->type),
            'type' => $request->type,
            'description' => $request->description,
            'group' => $request->group,
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('admin.system-settings.index')
            ->with('success', 'System setting updated successfully.');
    }

    public function destroy(SystemSetting $systemSetting)
    {
        $systemSetting->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'System setting deleted successfully.'
        ]);
    }

    private function formatValue($value, $type)
    {
        switch ($type) {
            case 'json':
                return is_string($value) ? $value : json_encode($value);
            case 'boolean':
                return $value ? '1' : '0';
            default:
                return $value;
        }
    }
}
