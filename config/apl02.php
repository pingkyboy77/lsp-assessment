<?php

return [

    /*
    |--------------------------------------------------------------------------
    | APL 02 Configuration
    |--------------------------------------------------------------------------
    */

    // File upload settings
    'file_upload' => [
        'max_size' => 10240, // 10MB in KB
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'],
        'storage_disk' => 'public',
        'base_path' => 'apl02-files',
    ],

    // Signature settings
    'signature' => [
        'max_width' => 400,
        'max_height' => 200,
        'format' => 'png',
        'quality' => 90,
    ],

    // Assessment settings
    'assessment' => [
        'passing_percentage' => 80,
        'minimum_competency' => 65,
        'auto_save_interval' => 30, // seconds
    ],

    // Folder structure: apl02-files/YYYY/MM/SCHEME_CODE/USER_NAME
    'folder_structure' => [
        'by_year' => true,
        'by_month' => true,
        'by_scheme' => true,
        'by_user' => true,
    ],

    // Status workflow
    'status_workflow' => [
        'draft' => ['submitted', 'returned'],
        'submitted' => ['review', 'rejected', 'returned'],
        'review' => ['approved', 'rejected', 'returned'],
        'approved' => [],
        'rejected' => [],
        'returned' => ['submitted']
    ],

];
