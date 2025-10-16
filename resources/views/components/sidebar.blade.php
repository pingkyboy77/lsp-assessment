{{-- resources/views/components/sidebar.blade.php --}}

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-large">
        <img src="{{ asset('images/logo-small.png') }}" alt="Logo Kecil" class="logo-small">
    </div>

    <div class="sidebar-nav">
        <ul class="nav nav-pills flex-column">
            @php
                $userRoles = auth()->user()->getRoleNames()->toArray();
                $groupedPages = collect();
                
                // Get pages for each role
                foreach($userRoles as $role) {
                    $rolePages = \App\Models\Page::getForRole($role);
                    $groupedPages = $groupedPages->mergeRecursive($rolePages);
                }
                
                // Remove duplicates and sort pages within groups
                $groupedPages = $groupedPages->map(function($pages) {
                    return $pages->unique('id')->sortBy('sort_order');
                });
                
                // Separate main group and other groups
                $mainPages = $groupedPages->get('main', collect());
                $otherGroups = $groupedPages->except(['main', '']);
                
                // Sort groups alphabetically
                $sortedGroups = $otherGroups->sortKeys();
                
                // Merge back: main first, then alphabetically sorted groups
                $groupedPages = collect(['main' => $mainPages])->merge($sortedGroups);
            @endphp

            @foreach($groupedPages as $groupName => $pages)
                {{-- Main pages (no group or main group) --}}
                @if($groupName === 'main' || empty($groupName))
                    @foreach($pages as $page)
                        <li class="nav-item">
                            <a href="{{ route($page->route_name) }}"
                                class="nav-link {{ $page->isCurrentRoute() ? 'active' : '' }}"
                                data-tooltip="{{ $page->name }}">
                                @if($page->icon)
                                    <i class="{{ $page->icon }}"></i>
                                @endif
                                <span>{{ $page->name }}</span>
                            </a>
                        </li>
                    @endforeach
                @else
                    {{-- Accordion Group --}}
                    <li class="nav-item accordion-group mt-2">
                        {{-- Group Header (Clickable) --}}
                        <button class="nav-link accordion-toggle collapsed" 
                                type="button" 
                                data-bs-toggle="collapse" 
                                data-bs-target="#group-{{ \Str::slug($groupName) }}"
                                aria-expanded="false"
                                data-tooltip="{{ $groupName }}">
                            <i class="bi bi-folder accordion-icon"></i>
                            <span class="accordion-title">{{ $groupName }}</span>
                            <i class="bi bi-chevron-down chevron-icon ms-auto"></i>
                        </button>
                        
                        {{-- Collapsible Group Content --}}
                        <div class="collapse" id="group-{{ \Str::slug($groupName) }}">
                            <ul class="nav nav-pills flex-column accordion-content">
                                @foreach($pages as $page)
                                    <li class="nav-item">
                                        <a href="{{ route($page->route_name) }}"
                                            class="nav-link {{ $page->isCurrentRoute() ? 'active' : '' }}"
                                            data-tooltip="{{ $page->name }}">
                                            @if($page->icon)
                                                <i class="{{ $page->icon }}"></i>
                                            @endif
                                            <span>{{ $page->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>

<style>
    /* Sidebar container fix */
    .sidebar {
        overflow-x: hidden !important;
        overflow-y: auto;
    }

    .sidebar-nav {
        overflow-x: hidden !important;
        width: 100%;
    }

    .sidebar .nav {
        overflow-x: hidden !important;
        width: 100%;
    }

    /* Allow text wrapping in sidebar */
    .sidebar .nav-link {
        white-space: normal !important;
        word-wrap: break-word;
        word-break: break-word;
        display: flex;
        align-items: flex-start;
        gap: 10px;
        max-width: 100%;
        box-sizing: border-box;
        padding: 12px 15px;
        position: relative;
        background-color: transparent;
        color: #6c757d;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }

    .sidebar .nav-link:hover {
        background-color: rgba(0,0,0,0.05);
        color: #495057;
    }

    .sidebar .nav-link.active {
        background-color: #5a6c7d !important;
        color: #ffffff !important;
    }

    .sidebar .nav-link.active i,
    .sidebar .nav-link.active span {
        color: #ffffff !important;
    }

    .sidebar .nav-link span {
        white-space: normal !important;
        word-wrap: break-word;
        word-break: break-word;
        flex: 1;
        min-width: 0;
        line-height: 1.4;
    }

    .sidebar .nav-link i {
        flex-shrink: 0;
        width: 20px;
        text-align: center;
        margin-top: 2px;
    }

    /* Nav item fix */
    .sidebar .nav-item {
        max-width: 100%;
        overflow: hidden;
    }

    /* Accordion Group Styles */
    .accordion-group {
        margin-bottom: 0.5rem;
    }

    .accordion-toggle {
        background: none;
        border: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
        padding: 12px 15px;
        border-radius: 6px;
        transition: all 0.2s;
        color: #6c757d !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .accordion-toggle:hover {
        background-color: rgba(0,0,0,0.05);
        color: #6c757d !important;
    }

    /* Keep gray color even when expanded */
    .accordion-toggle:not(.collapsed) {
        background: none;
        color: #6c757d !important;
    }

    .accordion-toggle .chevron-icon {
        transition: transform 0.3s ease;
        font-size: 0.75rem;
        color: #6c757d;
        margin-left: auto;
        flex-shrink: 0;
    }

    .accordion-toggle:not(.collapsed) .chevron-icon {
        transform: rotate(180deg);
    }

    .accordion-icon {
        color: #6c757d !important;
        flex-shrink: 0;
        width: 20px;
        text-align: center;
    }

    .accordion-title {
        flex: 1;
        color: #6c757d !important;
        min-width: 0;
    }

    /* Accordion Content */
    .accordion-content {
        padding-left: 10px;
        margin-top: 0.25rem;
    }

    .accordion-content .nav-link {
        padding-left: 35px;
        font-size: 0.9rem;
        color: #6c757d !important;
    }

    .accordion-content .nav-link:hover {
        background-color: rgba(0,0,0,0.05);
        color: #6c757d !important;
    }

    .accordion-content .nav-link i {
        color: #6c757d !important;
    }

    .accordion-content .nav-link span {
        color: #6c757d !important;
    }

    /* Sidebar collapse handling for accordion */
    .sidebar.collapsed .accordion-toggle {
        justify-content: center;
        align-items: center;
        padding: 12px !important;
        gap: 0;
    }

    .sidebar.collapsed .accordion-toggle .accordion-title,
    .sidebar.collapsed .accordion-toggle .chevron-icon {
        display: none !important;
    }

    .sidebar.collapsed .accordion-content {
        display: none !important;
    }

    .sidebar.collapsed .accordion-icon {
        margin: 0 !important;
        width: 20px !important;
        text-align: center !important;
    }

    /* Sidebar collapse */
    .sidebar.collapsed .nav-link span {
        display: none;
    }

    .sidebar.collapsed .nav-link {
        justify-content: center !important;
        align-items: center !important;
        position: relative;
        padding: 12px !important;
        gap: 0 !important;
    }

    .sidebar.collapsed .nav-link i {
        margin: 0 !important;
        margin-top: 0 !important;
        width: 20px !important;
        text-align: center !important;
    }

    /* Ensure all collapsed items have same dimensions */
    .sidebar.collapsed .nav-item {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .sidebar.collapsed .accordion-group {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Logo handling */
    .sidebar .logo-small {
        display: none;
    }

    .sidebar.collapsed .logo-large {
        display: none;
    }

    .sidebar.collapsed .logo-small {
        display: block;
    }

    /* Tooltip CSS murni */
    .sidebar.collapsed .nav-link::after,
    .sidebar.collapsed .accordion-toggle::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 100%;
        top: 50%;
        transform: translateY(-50%);
        background: #333;
        color: #fff;
        padding: 6px 12px;
        border-radius: 4px;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.2s;
        font-size: 0.85rem;
        margin-left: 8px;
        z-index: 1000;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .sidebar.collapsed .nav-link:hover::after,
    .sidebar.collapsed .accordion-toggle:hover::after {
        opacity: 1;
    }

    /* Active state for accordion items */
    .accordion-content .nav-link.active {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        font-weight: 500;
    }

    /* Remove color change for accordion toggle even with active items */
    .accordion-toggle:has(+ .collapse .nav-link.active) {
        color: #6c757d !important;
    }

    .accordion-toggle:has(+ .collapse .nav-link.active):not(.collapsed) {
        background-color: transparent;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand accordion if it contains active page
    document.querySelectorAll('.accordion-content').forEach(content => {
        if (content.querySelector('.nav-link.active')) {
            const collapseElement = content.closest('.collapse');
            if (collapseElement) {
                const bsCollapse = new bootstrap.Collapse(collapseElement, {
                    toggle: false
                });
                bsCollapse.show();
                
                // Update toggle button state
                const toggleBtn = document.querySelector(`[data-bs-target="#${collapseElement.id}"]`);
                if (toggleBtn) {
                    toggleBtn.classList.remove('collapsed');
                    toggleBtn.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
});
</script>