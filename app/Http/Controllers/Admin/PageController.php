<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Check if it's DataTables AJAX request
        if ($request->ajax()) {
            return $this->getDataTablesData($request);
        }

        // Get unique groups for filter
        $groups = Page::getGroups();

        // Get roles for form
        $roles = Role::all();

        return view('admin.pages.index', compact('groups', 'roles'));
    }

    /**
     * Get data for DataTables server-side processing
     */
    private function getDataTablesData(Request $request)
    {
        $query = Page::query();

        // Apply filters
        if ($request->filled('group_filter')) {
            $query->where('group', $request->group_filter);
        }

        if ($request->filled('status_filter')) {
            $query->where('is_active', $request->status_filter == 'active');
        }

        // Search functionality
        if ($request->filled('search.value')) {
            $searchValue = $request->input('search.value');
            $query->where(function ($q) use ($searchValue) {
                $q->where('name', 'like', '%' . $searchValue . '%')
                    ->orWhere('route_name', 'like', '%' . $searchValue . '%')
                    ->orWhere('description', 'like', '%' . $searchValue . '%');
            });
        }

        // Get total count before pagination
        $totalRecords = Page::count();
        $filteredRecords = $query->count();

        // Ordering
        $orderColumnIndex = $request->input('order.0.column', 1);
        $orderDirection = $request->input('order.0.dir', 'asc');

        $columns = ['id', 'name', 'route_name', 'group', 'allowed_roles', 'is_active', 'sort_order'];
        $orderColumn = $columns[$orderColumnIndex] ?? 'name';

        $query->orderBy($orderColumn, $orderDirection);

        // Pagination
        $start = (int) $request->input('start', 0);
        $length = (int) $request->input('length', 10);

        $pages = $query->skip($start)->take($length)->get();

        // Format data for DataTables
        $data = $pages->map(function ($page) {
            return [
                'id' => $page->id,
                'checkbox' => '<input type="checkbox" name="page_ids[]" value="' . $page->id . '" class="form-check-input page-checkbox">',
                'name' => $this->formatNameColumn($page),
                'route_name' => '<code class="small">' . $page->route_name . '</code>',
                'group' => $this->formatGroupColumn($page),
                'roles' => $this->formatRolesColumn($page),
                'status' => $this->formatStatusColumn($page),
                'sort_order' => '<span class="badge bg-light text-dark">' . $page->sort_order . '</span>',
                'actions' => $this->formatActionsColumn($page)
            ];
        });

        return response()->json([
            'draw' => (int) $request->input('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    private function formatNameColumn($page)
    {
        $html = '<div class="d-flex align-items-center">';
        if ($page->icon) {
            $html .= '<i class="' . $page->icon . ' me-2"></i>';
        }
        $html .= '<div>';
        $html .= '<span class="fw-medium">' . $page->name . '</span>';
        if ($page->description) {
            $html .= '<br><small class="text-muted">' . \Str::limit($page->description, 50) . '</small>';
        }
        if ($page->is_sidebar_menu) {
            $html .= '<span class="badge bg-info ms-1">Sidebar</span>';
        }
        $html .= '</div></div>';
        return $html;
    }

    private function formatGroupColumn($page)
    {
        if ($page->group) {
            return '<span class="badge bg-secondary">' . $page->group . '</span>';
        }
        return '<span class="text-muted">-</span>';
    }

    private function formatRolesColumn($page)
    {
        if ($page->allowed_roles && count($page->allowed_roles) > 0) {
            $html = '';
            $roles = array_slice($page->allowed_roles, 0, 2);
            foreach ($roles as $role) {
                $html .= '<span class="badge bg-primary me-1">' . $role . '</span>';
            }
            if (count($page->allowed_roles) > 2) {
                $html .= '<span class="badge bg-light text-dark">+' . (count($page->allowed_roles) - 2) . '</span>';
            }
            return $html;
        }
        return '<span class="badge bg-success">All Users</span>';
    }

    private function formatStatusColumn($page)
    {
        return '<form method="POST" action="' . route('admin.pages.toggle-status', $page) . '" class="d-inline">
                    ' . csrf_field() . '
                    <button type="submit" class="btn btn-sm p-0 border-0 bg-transparent">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" ' .
            ($page->is_active ? 'checked' : '') . ' onchange="this.form.submit()">
                        </div>
                    </button>
                </form>';
    }

    private function formatActionsColumn($page)
    {
        $html = '<div class="btn-group btn-group-sm">';
        $html .= '<a href="' . route('admin.pages.show', $page) . '" class="btn btn-outline-info btn-sm" title="Lihat"><i class="bi bi-eye"></i></a>';
        $html .= '<a href="' . route('admin.pages.edit', $page) . '" class="btn btn-outline-warning btn-sm" title="Edit"><i class="bi bi-pencil"></i></a>';

        $protectedRoutes = ['admin.dashboard', 'asesi.dashboard', 'admin.pages.index'];
        if (!in_array($page->route_name, $protectedRoutes)) {
            $html .= '<form method="POST" action="' . route('admin.pages.destroy', $page) . '" class="d-inline">
                        ' . csrf_field() . method_field('DELETE') . '
                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus" 
                                onclick="return confirm(\'Yakin ingin menghapus halaman ini?\')">
                            <i class="bi bi-trash"></i>
                        </button>
                      </form>';
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $groups = Page::getGroups();

        return view('admin.pages.create', compact('roles', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route_name' => 'required|string|max:255|unique:pages,route_name',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'exists:roles,name',
            'parent_route' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Convert boolean checkboxes
        $data['is_active'] = $request->boolean('is_active');
        $data['is_sidebar_menu'] = $request->boolean('is_sidebar_menu');

        Page::create($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Page $page)
    {
        return view('admin.pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Page $page)
    {
        $roles = Role::all();
        $groups = Page::getGroups();

        return view('admin.pages.edit', compact('page', 'roles', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Page $page)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'route_name' => 'required|string|max:255|unique:pages,route_name,' . $page->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'group' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'allowed_roles' => 'nullable|array',
            'allowed_roles.*' => 'exists:roles,name',
            'parent_route' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Convert boolean checkboxes
        $data['is_active'] = $request->boolean('is_active');
        $data['is_sidebar_menu'] = $request->boolean('is_sidebar_menu');

        $page->update($data);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Page $page)
    {
        // Prevent deletion of important system pages
        $protectedRoutes = [
            'admin.dashboard',
            'asesi.dashboard',
            'admin.pages.index'
        ];

        if (in_array($page->route_name, $protectedRoutes)) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Halaman sistem tidak dapat dihapus.');
        }

        $page->delete();

        return redirect()->route('admin.pages.index')
            ->with('success', 'Halaman berhasil dihapus.');
    }

    /**
     * Toggle page status
     */
    public function toggleStatus(Page $page)
    {
        $page->update(['is_active' => !$page->is_active]);

        $status = $page->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->back()->with('success', "Halaman berhasil {$status}.");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'page_ids' => 'required|array',
            'page_ids.*' => 'exists:pages,id'
        ]);

        $pages = Page::whereIn('id', $request->page_ids)->get();

        // Protect system pages
        $protectedRoutes = [
            'admin.dashboard',
            'asesi.dashboard',
            'admin.pages.index'
        ];

        switch ($request->action) {
            case 'activate':
                Page::whereIn('id', $request->page_ids)->update(['is_active' => true]);
                $message = count($request->page_ids) . ' halaman berhasil diaktifkan.';
                break;

            case 'deactivate':
                Page::whereIn('id', $request->page_ids)->update(['is_active' => false]);
                $message = count($request->page_ids) . ' halaman berhasil dinonaktifkan.';
                break;

            case 'delete':
                $protectedPages = $pages->whereIn('route_name', $protectedRoutes);
                if ($protectedPages->count() > 0) {
                    return redirect()->back()
                        ->with('error', 'Tidak dapat menghapus halaman sistem.');
                }

                Page::whereIn('id', $request->page_ids)->delete();
                $message = count($request->page_ids) . ' halaman berhasil dihapus.';
                break;
        }

        return redirect()->route('admin.pages.index')->with('success', $message);
    }

    /**
     * Reorder pages
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'pages' => 'required|array',
            'pages.*.id' => 'required|exists:pages,id',
            'pages.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->pages as $pageData) {
            Page::where('id', $pageData['id'])
                ->update(['sort_order' => $pageData['sort_order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Urutan halaman berhasil diupdate.'
        ]);
    }

    /**
     * Export pages configuration
     */
    public function export()
    {
        $pages = Page::all();

        $filename = 'pages_config_' . date('Y-m-d_H-i-s') . '.json';

        return response()->json($pages, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ], JSON_PRETTY_PRINT);
    }

    /**
     * Import pages configuration
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $content = file_get_contents($request->file('file')->path());
        $pages = json_decode($content, true);

        if (!$pages) {
            return redirect()->back()->with('error', 'File JSON tidak valid.');
        }

        foreach ($pages as $pageData) {
            unset($pageData['id'], $pageData['created_at'], $pageData['updated_at']);

            Page::updateOrCreate(
                ['route_name' => $pageData['route_name']],
                $pageData
            );
        }

        return redirect()->route('admin.pages.index')
            ->with('success', 'Konfigurasi halaman berhasil diimport.');
    }
}
