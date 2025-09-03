<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-large">
        <img src="{{ asset('images/logo-small.png') }}" alt="Logo Kecil" class="logo-small">
    </div>

    <div class="sidebar-nav">
        <ul class="nav nav-pills flex-column">

            
            

            @role('superadmin')
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                   data-tooltip="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-group-header mt-3 mb-1 px-3 text-uppercase text-secondary small fw-bold">
                Admin Management
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}"
                   class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}"
                   data-tooltip="User Management">
                    <i class="bi bi-people"></i>
                    <span>User Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.roles.index') }}"
                   class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"
                   data-tooltip="Role Management">
                    <i class="bi bi-person-badge"></i>
                    <span>Role Management</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.number-sequences.index') }}"
                   class="nav-link {{ request()->routeIs('admin.number-sequences.*') ? 'active' : '' }}"
                   data-tooltip="System Settings">
                    <i class="bi bi-gear"></i>
                    <span>System Settings</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.lembaga.index') }}"
                   class="nav-link {{ request()->routeIs('admin.lembaga.*') ? 'active' : '' }}"
                   data-tooltip="Lembaga Pelatihan">
                    <i class="bi bi-building"></i>
                    <span>Lembaga Pelatihan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.fields.index') }}"
                   class="nav-link {{ request()->routeIs('admin.fields.*') ? 'active' : '' }}"
                   data-tooltip="Bidang Sertifikasi">
                    <i class="bi bi-diagram-3"></i>
                    <span>Bidang Sertifikasi</span>
                </a>
            </li>
             <li class="nav-item">
                <a href="{{ route('admin.requirements.index') }}"
                   class="nav-link {{ request()->routeIs('admin.requirements.*') ? 'active' : '' }}"
                   data-tooltip="Bidang Sertifikasi">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Template Persyaratan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.certification-schemes.index') }}"
                   class="nav-link {{ request()->routeIs('admin.certification-schemes.*') ? 'active' : '' }}"
                   data-tooltip="Bidang Sertifikasi">
                    <i class="bi bi-award"></i>
                    <span>Skema Sertifikasi</span>
                </a>
            </li>
           

            <li class="nav-item nav-group-header mt-3 mb-1 px-3 text-uppercase text-secondary small fw-bold">
                System & Monitoring
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.monitoring-profile.index') }}"
                   class="nav-link {{ request()->routeIs('admin.monitoring-profile.*') ? 'active' : '' }}"
                   data-tooltip="Monitoring Profile">
                    <i class="bi bi-clipboard-data"></i>
                    <span>Monitoring Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.apl01.index') }}"
                   class="nav-link {{ request()->routeIs('admin.apl01.*') ? 'active' : '' }}"
                   data-tooltip="Monitoring APL 01">
                    <i class="bi bi-file"></i>
                    <span>Monitoring APL 01</span>
                </a>
            </li>
            
            {{-- <li class="nav-item">
                <a href="{{ route('admin.schemes.unit-kompetensi.index') }}"
                   class="nav-link {{ request()->routeIs('schemes.unit-kompetensi.*') ? 'active' : '' }}"
                   data-tooltip="Bidang Sertifikasi">
                    <i class="bi bi-award"></i>
                    <span>Persyaratan</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.schemes.kelompok-kerja.index') }}"
                   class="nav-link {{ request()->routeIs('schemes.kelompok-kerja.*') ? 'active' : '' }}"
                   data-tooltip="Bidang Sertifikasi">
                    <i class="bi bi-award"></i>
                    <span>Persyaratan</span>
                </a>
            </li> --}}
            @endrole

            @role('asesi')
            <li class="nav-item">
                <a href="{{ route('asesi.dashboard') }}" 
                   class="nav-link {{ request()->routeIs('asesi.dashboard') ? 'active' : '' }}"
                   data-tooltip="Dashboard">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item nav-group-header mt-3 mb-1 px-3 text-uppercase text-secondary small fw-bold">
                Asesi Menu
            </li>
            <li class="nav-item">
                <a href="{{ route('asesi.data-pribadi.index') }}"
                   class="nav-link {{ request()->routeIs('asesi.data-pribadi.*') ? 'active' : '' }}"
                   data-tooltip="Profile">
                    <i class="bi bi-person-circle"></i>
                    <span>Profile</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('asesi.skema-sertifikasi.index') }}"
                   class="nav-link {{ request()->routeIs('asesi.skema-sertifikasi.*') ? 'active' : '' }}"
                   data-tooltip="Skema Sertifikasi">
                    <i class="bi bi-mortarboard"></i>
                    <span>Skema Sertifikasi</span>
                </a>
            </li>
            @endrole

        </ul>
    </div>
</div>

<style>
/* Sidebar collapse */
.sidebar.collapsed .nav-link span,
.sidebar.collapsed .nav-group-header {
    display: none;
}

.sidebar.collapsed .nav-link {
    justify-content: center;
    position: relative;
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
.sidebar.collapsed .nav-link::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: #333;
    color: #fff;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: 0.2s;
    font-size: 0.85rem;
    margin-left: 8px;
    z-index: 100;
}

.sidebar.collapsed .nav-link:hover::after {
    opacity: 1;
}
</style>
