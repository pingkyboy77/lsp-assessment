<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\LembagaPelatihan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LembagaPelatihanController extends Controller
{
    public function index(Request $request)
{
    if ($request->ajax()) {
        $data = LembagaPelatihan::with(['creator', 'updater'])->latest();

        return datatables()->of($data)
            ->addIndexColumn()
            ->addColumn('creator', function ($row) {
                return $row->creator ? $row->creator->name : '-';
            })
            ->addColumn('updater', function ($row) {
                return $row->updater ? $row->updater->name : '-';
            })
            ->addColumn('action', function ($row) {
                $editUrl = route('admin.lembaga.edit', $row->id);
                $deleteUrl = route('admin.lembaga.destroy', $row->id);
                return '
                    <a href="'.$editUrl.'" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil-square"></i></a>
                    <form action="'.$deleteUrl.'" method="POST" style="display:inline;">
                        '.csrf_field().method_field('DELETE').'
                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Are you sure?\')"><i class="bi bi-trash"></i></button>
                    </form>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    return view('admin.lembaga.index');
}


    public function create()
    {
        return view('admin.lembaga.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:lembaga_pelatihan,name|max:255',
        ]);

        // Generate ID pendek
        $id = 'LP-' . strtoupper(Str::random(6));

        LembagaPelatihan::create([
            'id' => $id,
            'name' => $request->name,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga Pelatihan created successfully');
    }

    public function edit(LembagaPelatihan $lembaga)
    {
        return view('admin.lembaga.edit', compact('lembaga'));
    }

    public function update(Request $request, LembagaPelatihan $lembaga)
    {
        $request->validate([
            'name' => 'required|max:255|unique:lembaga_pelatihan,name,' . $lembaga->id,
        ]);

        $lembaga->update([
            'name' => $request->name,
            'updated_by' => Auth::id(),
        ]);

        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga Pelatihan updated successfully');
    }

    public function destroy(LembagaPelatihan $lembaga)
    {
        $lembaga->delete();
        return redirect()->route('admin.lembaga.index')->with('success', 'Lembaga Pelatihan deleted successfully');
    }
}
