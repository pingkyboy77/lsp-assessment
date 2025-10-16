<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EvidenceUploadRequest extends FormRequest
{
    public function authorize()
    {
        $apl02 = $this->route('apl02');
        return $this->user()->can('uploadEvidence', $apl02);
    }

    public function rules()
    {
        return [
            'portfolio_file_id' => 'required|exists:portfolio_files,id',
            'evidence_file' => [
                'required',
                'file',
                'mimes:' . implode(',', config('apl02.file_upload.allowed_types')),
                'max:' . config('apl02.file_upload.max_size')
            ],
            'description' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'evidence_file.max' => 'File size cannot exceed ' . (config('apl02.file_upload.max_size') / 1024) . 'MB',
            'evidence_file.mimes' => 'File type not allowed. Allowed types: ' . implode(', ', config('apl02.file_upload.allowed_types')),
        ];
    }
}
