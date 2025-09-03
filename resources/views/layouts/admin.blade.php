<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>LSPPM Assessment Application</title>
    <link rel="icon" href="{{ asset('images/logo-putih-small.png') }}" type="image/png">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/select/1.7.0/css/select.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchpanes/2.2.0/css/searchPanes.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css"
        href="https://cdn.datatables.net/searchbuilder/1.6.0/css/searchBuilder.bootstrap5.min.css">

         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <style>
        .counter-number {
            transition: all 0.3s ease;
        }

        .stat-card {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            transform: translateY(-5px) scale(1.02);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card.asesi .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.sertifikasi .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.lsp .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card.skema .stat-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 1rem 1rem 0 0;
        }

        .stat-card.asesi::before {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card.sertifikasi::before {
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card.lsp::before {
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card.skema::before {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
        }

        .pulse-effect {
            animation: pulse 2s infinite;
        }

        /* @keyframes pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.05);
                }

                100% {
                    transform: scale(1);
                }
            } */

        .growth-indicator {
            font-size: 0.85rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            margin-top: 0.75rem;
            display: inline-block;
        }

        .growth-positive {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .growth-negative {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        :root {
            --primary-color: #6c757d;
            --primary-dark: #495057;
            --primary-light: #adb5bd;
            --sidebar-bg: #f8f9fa;
            --sidebar-active: #6c757d;
            --main-bg: #e9ecef;
            --card-bg: #ffffff;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-muted: #adb5bd;
            --border-color: #dee2e6;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --sidebar-width: 250px;
            --sidebar-collapsed-width: 80px;
            --topbar-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--main-bg);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            transition: width 0.3s ease-in-out;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .sidebar.collapsed {
            width: var(--sidebar-collapsed-width);
        }

        .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            background: white;
            height: var(--topbar-height);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease-in-out;
        }

        .logo-large {
            transition: opacity 0.3s ease-in-out;
            max-width: 120px;
        }

        .logo-small {
            display: none;
            transition: opacity 0.3s ease-in-out;
            width: 40px;
            height: 40px;
        }

        .sidebar.collapsed .logo-large {
            opacity: 0;
            display: none;
        }

        .sidebar.collapsed .logo-small {
            display: block;
            opacity: 1;
        }

        .sidebar-nav {
            padding: 1rem 0;
            overflow-y: auto;
            height: calc(100vh - var(--topbar-height));
        }

        .nav-pills .nav-link {
            color: var(--text-secondary);
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            margin: 0.125rem 0.75rem;
            border-radius: 0.5rem;
            transition: all 0.2s ease-in-out;
            border: none;
            background: transparent;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
            position: relative;
            text-decoration: none;
        }

        .nav-pills .nav-link i {
            width: 20px;
            font-size: 1rem;
            flex-shrink: 0;
            margin-right: 0.75rem;
        }

        .nav-pills .nav-link span {
            transition: opacity 0.3s ease-in-out;
        }

        .sidebar.collapsed .nav-pills .nav-link {
            justify-content: center;
            padding: 0.75rem;
            margin: 0.125rem 0.5rem;
        }

        .sidebar.collapsed .nav-pills .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .nav-pills .nav-link span {
            opacity: 0;
            position: absolute;
            left: 70px;
            background: var(--text-primary);
            color: white;
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            white-space: nowrap;
            transform: translateX(-10px);
            pointer-events: none;
            z-index: 1001;
            box-shadow: var(--shadow);
        }

        .sidebar.collapsed .nav-pills .nav-link:hover span {
            opacity: 1;
            transform: translateX(0);
        }

        .nav-pills .nav-link:hover {
            background: rgba(108, 117, 125, 0.1);
            color: var(--primary-dark);
        }

        .nav-pills .nav-link.active {
            background: var(--sidebar-active);
            color: white;
        }

        .nav-item.logout .nav-link {
            color: #dc3545;
            margin-top: 1rem;
        }

        .nav-item.logout .nav-link:hover {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        /* Top Navbar */
        .top-navbar {
            background: var(--card-bg);
            border-bottom: 1px solid var(--border-color);
            height: var(--topbar-height);
            box-shadow: var(--shadow-sm);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 999;
            transition: left 0.3s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
        }

        .top-navbar.collapsed {
            left: var(--sidebar-collapsed-width);
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.25rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: rgba(108, 117, 125, 0.1);
            color: var(--primary-dark);
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--topbar-height);
            min-height: calc(100vh - var(--topbar-height));
            transition: margin-left 0.3s ease-in-out;
            background: var(--main-bg);
        }

        .main-content.collapsed {
            margin-left: var(--sidebar-collapsed-width);
        }

        .content-area {
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stat-card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
            height: 100%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .stat-icon.total-asesi {
            background: linear-gradient(135deg, #6f42c1, #8e4ec6);
        }

        .stat-icon.sertifikat {
            background: linear-gradient(135deg, #e83e8c, #fd7e14);
        }

        .stat-icon.lp-aktif {
            background: linear-gradient(135deg, #17a2b8, #20c997);
        }

        .stat-icon.skema {
            background: linear-gradient(135deg, #28a745, #20c997);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .stat-change {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .stat-change.positive {
            color: #198754;
        }

        .stat-change.negative {
            color: #dc3545;
        }

        /* Chart Section */
        .chart-card {
            background: var(--card-bg);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--border-color);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                width: var(--sidebar-width);
            }

            .top-navbar {
                left: 0;
            }

            .top-navbar.collapsed {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            .main-content.collapsed {
                margin-left: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease-in-out;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        @media (max-width: 576px) {
            .content-area {
                padding: 1rem;
            }

            .top-navbar {
                padding: 0 1rem;
            }

            .stat-card {
                margin-bottom: 1rem;
            }
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper {
            margin-top: 1rem;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.5rem;
            border: 1px solid var(--border-color);
            padding: 0.375rem 0.75rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            background-color: var(--card-bg);
        }

        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead th {
            background-color: var(--sidebar-bg);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: var(--text-primary);
        }

        .table tbody tr:hover {
            background-color: rgba(108, 117, 125, 0.05);
        }

        /* Utilities */
        .btn-outline-gray {
            color: var(--text-secondary);
            border-color: var(--border-color);
        }

        .btn-outline-gray:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }

        /* Loading Spinner for DataTables */
        .dataTables_processing {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            width: auto !important;
            margin: 0 !important;
            transform: translate(-50%, -50%) !important;
            background: var(--card-bg) !important;
            border: 1px solid var(--border-color) !important;
            border-radius: 0.5rem !important;
            padding: 1rem 2rem !important;
            box-shadow: var(--shadow) !important;
        }

        <style>.page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .page-header h4 {
            margin: 0;
            font-weight: 700;
            font-size: 2rem;
        }

        .page-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }

        .main-card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .main-card:hover {
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.12);
            /* transform: translateY(-2px); */
        }

        .card-header-custom {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #e9ecef;
            padding: 2rem;
            position: relative;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        .btn-primary-custom:focus {
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .table-container {
            padding: 2rem;
            background: white;
        }

        .table {
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            font-weight: 700;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1.25rem 1rem;
            position: relative;
        }

        .table thead th:first-child {
            border-top-left-radius: 1rem;
        }

        .table thead th:last-child {
            border-top-right-radius: 1rem;
        }

        .table tbody td {
            padding: 1.25rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f3f4;
            vertical-align: middle;
            transition: all 0.2s ease;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
            transform: scale(1.01);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: 1rem;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: 1rem;
        }

        .btn-action {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-edit {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: white;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #bd2130 100%);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
            color: white;
        }

        .alert-success-custom {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
            border: 2px solid rgba(40, 167, 69, 0.2);
            border-radius: 1rem;
            color: #155724;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .alert-success-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: #28a745;
        }

        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1.5rem;
        }

        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 0.75rem;
            border: 2px solid #e9ecef;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_filter input {
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236c757d' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat right 1rem center;
            padding-right: 3rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus,
        .dataTables_wrapper .dataTables_length select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .dataTables_wrapper .dataTables_info {
            color: #6c757d;
            font-weight: 500;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 0.5rem !important;
            /* margin: 0 0.25rem; */
            color: black !important;
            /* border: 2px solid transparent; */
            transition: all 0.3s ease;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #00#FF0000FF, #764ba2 100%) !important;
            color: white !important;
            border-color: transparent !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            /* background: rgba(102, 126, 234, 0.1) !important; */
            /* color: #667eea !important; */
            /* border-color: #667eea !important; */
        }

        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
            border-color: rgba(102, 126, 234, 0.2);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Loading Animation */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                padding: 1.5rem;
                text-align: center;
            }

            .page-header h4 {
                font-size: 1.5rem;
            }

            .card-header-custom {
                padding: 1.5rem;
            }

            .table-container {
                padding: 1rem;
            }

            .btn-action {
                padding: 0.375rem 0.75rem;
                font-size: 0.8rem;
                margin: 0.1rem;
            }
        }
    </style>

    <style>
        /* Paper-like styling untuk halaman detail */
        .paper-container {
            background: #ffffff;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            margin: 20px auto;
            max-width: 1200px;
            position: relative;
            overflow: hidden;
        }

        .paper-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100%;
            background:
                linear-gradient(90deg, transparent 79px, #e8f4f8 80px, #e8f4f8 81px, transparent 82px),
                linear-gradient(#f8f9fa 0px, transparent 1px);
            background-size: 100% 24px;
            pointer-events: none;
            border-radius: 8px;
        }

        .paper-content {
            position: relative;
            z-index: 1;
            padding: 40px 60px 40px 100px;
            min-height: 800px;
        }

        .paper-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #2c5282;
        }

        .paper-title {
            font-size: 24px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .paper-subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 0;
        }

        .info-section {
            margin-bottom: 35px;
            page-break-inside: avoid;
        }

        .info-section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 20px;
            padding: 10px 15px;
            background: #f7fafc;
            border-left: 4px solid #2c5282;
            border-radius: 4px;
            display: flex;
            align-items: center;
        }

        .info-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            gap: 30px;
        }

        .info-col {
            flex: 1;
            min-width: 250px;
        }

        .info-col-full {
            width: 100%;
        }

        .info-item {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .info-label {
            font-weight: 600;
            color: #2d3748;
            font-size: 14px;
            margin-bottom: 3px;
        }

        .info-value {
            color: #4a5568;
            font-size: 14px;
            padding: 8px 12px;
            background: #f7fafc;
            border-radius: 4px;
            border-left: 3px solid #cbd5e0;
            min-height: 20px;
            display: flex;
            align-items: center;
        }

        .info-value.empty {
            color: #a0aec0;
            font-style: italic;
        }

        /* Document section styling */
        .document-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin: 30px 0;
        }

        .document-table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-top: 15px;
        }

        .document-table table {
            margin-bottom: 0;
        }

        .document-table th {
            background: #2c5282;
            color: white;
            font-weight: 600;
            padding: 15px;
            border: none;
            font-size: 14px;
        }

        .document-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 14px;
        }

        .document-table tbody tr:hover {
            background: #f7fafc;
        }

        .document-table tbody tr:last-child td {
            border-bottom: none;
        }

        .btn-doc-action {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
            margin-right: 5px;
            border: none;
        }

        .btn-view-doc {
            background: #4299e1;
            color: white;
        }

        .btn-view-doc:hover {
            background: #3182ce;
            color: white;
            transform: translateY(-1px);
        }

        .btn-download-doc {
            background: #38a169;
            color: white;
        }

        .btn-download-doc:hover {
            background: #2f855a;
            color: white;
            transform: translateY(-1px);
        }

        .no-documents {
            text-align: center;
            color: #a0aec0;
            padding: 30px;
            background: white;
            border-radius: 6px;
        }

        .no-documents i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        /* Navigation buttons */
        .nav-buttons {
            position: fixed;
            gap: 10px;
        }

        .btn-nav {
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: none;
        }

        .btn-warning-custom {
            background: #ed8936;
            color: white;
        }

        .btn-warning-custom:hover {
            background: #dd6b20;
            color: white;
            transform: translateY(-2px);
        }

        .btn-secondary-custom {
            background: #718096;
            color: white;
        }

        .btn-secondary-custom:hover {
            background: #4a5568;
            color: white;
            transform: translateY(-2px);
        }

        /* Alert styling */
        .alert-success-custom {
            background: #c6f6d5;
            color: #22543d;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #38a169;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        .alert-danger-custom {
            background: #fed7d7;
            color: #c53030;
            padding: 15px 20px;
            border-radius: 8px;
            border-left: 4px solid #e53e3e;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
        }

        /* Info footer */
        .info-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px dashed #cbd5e0;
            text-align: center;
        }

        .info-footer small {
            background: #f7fafc;
            padding: 10px 20px;
            border-radius: 20px;
            display: inline-block;
        }

        /* File status styling */
        .file-status {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-status.exists {
            color: #38a169;
        }

        .file-status.missing {
            color: #e53e3e;
        }

        .file-size {
            color: #666;
            font-size: 12px;
            font-style: italic;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .paper-content {
                padding: 30px 20px;
            }

            .info-row {
                flex-direction: column;
                gap: 15px;
            }

            .nav-buttons {
                position: fixed;
                justify-content: center;
            }

            .btn-nav {
                font-size: 14px;
                padding: 8px 16px;
            }

            .document-table {
                font-size: 12px;
            }

            .btn-doc-action {
                padding: 4px 8px;
                font-size: 11px;
            }
        }

        @media print {
            .nav-buttons {
                display: none;
            }

            .paper-container {
                box-shadow: none;
                margin: 0;
                max-width: none;
            }

            .paper-content {
                padding: 20px;
            }

            .btn-doc-action {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Include Sidebar -->
    @include('partials.admin-side')

    <!-- Include Topbar -->
    @include('partials.topbar')

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.min.js"></script>
    <script src="https://cdn.datatables.net/searchbuilder/1.6.0/js/dataTables.searchBuilder.min.js"></script>

    <!-- JSZip for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

    <!-- pdfmake for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
     <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Main Layout JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const topNavbar = document.getElementById('topNavbar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            // Function to toggle sidebar
            function toggleSidebar() {
                if (window.innerWidth > 991.98) {
                    // Desktop: collapse/expand sidebar
                    sidebar.classList.toggle('collapsed');
                    mainContent.classList.toggle('collapsed');
                    topNavbar.classList.toggle('collapsed');

                    // Save state to localStorage
                    localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
                } else {
                    // Mobile: show/hide sidebar
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                    document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
                }
            }

            // Initialize sidebar state from localStorage
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true' && window.innerWidth > 991.98) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('collapsed');
                topNavbar.classList.add('collapsed');
            }

            // Sidebar toggle button click
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }

            // Close sidebar when clicking overlay (mobile only)
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            }

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 991.98) {
                    // Desktop mode
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';

                    // Restore collapsed state from localStorage
                    const savedState = localStorage.getItem('sidebarCollapsed');
                    if (savedState === 'true') {
                        sidebar.classList.add('collapsed');
                        mainContent.classList.add('collapsed');
                        topNavbar.classList.add('collapsed');
                    }
                } else {
                    // Mobile mode - remove collapsed classes
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('collapsed');
                    topNavbar.classList.remove('collapsed');
                }
            });

            // Close mobile sidebar when clicking nav links
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth <= 991.98) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                        document.body.style.overflow = '';
                    }
                });
            });
        });

        // Global DataTable defaults
        $(document).ready(function() {
            $.extend(true, $.fn.dataTable.defaults, {
                processing: true,
                responsive: true,
                language: {
                    search: '',
                    searchPlaceholder: 'Search Data...',
                    lengthMenu: 'Show _MENU_ entries per page',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    emptyTable: 'No data available in table',
                    zeroRecords: 'No matching Data found',
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>'
                    }
                },
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                pageLength: 10,
                lengthMenu: [
                    [10, 25, 50, 100],
                    [10, 25, 50, 100]
                ]
            });
        });

        // Utility functions
        function initializeDataTable(selector, options = {}) {
            const defaultOptions = {
                processing: true,
                serverSide: false,
                responsive: true,
                autoWidth: false,
                order: [
                    [0, 'desc']
                ],
                columnDefs: [{
                    targets: 'no-sort',
                    orderable: false,
                    searchable: false
                }]
            };

            return $(selector).DataTable($.extend(true, {}, defaultOptions, options));
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 5000);
        }

        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        function formatCurrency(amount, currency = 'IDR') {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: currency
            }).format(amount);
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>

    @stack('scripts')
</body>

</html>
