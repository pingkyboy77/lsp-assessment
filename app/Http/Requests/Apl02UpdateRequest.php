<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Apl02UpdateRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('update', $this->route('apl02'));
    }

    public function rules()
    {
        return [
            'assessments' => 'required|array|min:1',
            'assessments.*.elemen_id' => 'required|exists:elemen_kompetensis,id',
            'assessments.*.unit_id' => 'required|exists:unit_kompetensis,id',
            'assessments.*.result' => 'required|in:kompeten,belum_kompeten',
            'assessments.*.notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'assessments.required' => 'Assessment data is required',
            'assessments.*.elemen_id.required' => 'Element ID is required',
            'assessments.*.elemen_id.exists' => 'Invalid element ID',
            'assessments.*.unit_id.required' => 'Unit ID is required',
            'assessments.*.unit_id.exists' => 'Invalid unit ID',
            'assessments.*.result.required' => 'Assessment result is required',
            'assessments.*.result.in' => 'Assessment result must be kompeten or belum_kompeten',
            'assessments.*.notes.max' => 'Notes cannot exceed 1000 characters',
        ];
    }
} function viewAny(User $user)
    {
        return $user->hasAnyRole(['admin', 'asesor', 'asesi']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Apl02 $apl02)
    {
        // Admin and asesor can view all APL 02
        if ($user->hasAnyRole(['admin', 'asesor'])) {
            return true;
        }

        // Asesi can only view their own APL 02
        if ($user->hasRole('asesi')) {
            return $apl02->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasRole('asesi');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Apl02 $apl02)
    {
        // Only asesi can update their own APL 02
        if ($user->hasRole('asesi')) {
            return $apl02->user_id === $user->id && 
                   in_array($apl02->status, ['draft', 'returned']);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Apl02 $apl02)
    {
        // Only asesi can delete their own draft APL 02
        if ($user->hasRole('asesi')) {
            return $apl02->user_id === $user->id && 
                   $apl02->status === 'draft';
        }

        return false;
    }

    /**
     * Determine whether the user can review the model.
     */
    public function review(User $user, Apl02 $apl02)
    {
        return $user->hasAnyRole(['admin', 'asesor']) && 
               in_array($apl02->status, ['submitted', 'review']);
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Apl02 $apl02)
    {
        return $user->hasAnyRole(['admin', 'asesor']) && 
               $apl02->status === 'review' &&
               $apl02->is_signed_by_asesor;
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Apl02 $apl02)
    {
        return $user->hasAnyRole(['admin', 'asesor']) && 
               in_array($apl02->status, ['submitted', 'review']);
    }

    /**
     * Determine whether the user can return the model to asesi.
     */
    public function returnToAsesi(User $user, Apl02 $apl02)
    {
        return $user->hasAnyRole(['admin', 'asesor']) && 
               in_array($apl02->status, ['submitted', 'review']);
    }

    /**
     * Determine whether the user can sign as asesor.
     */
    public function signAsesor(User $user, Apl02 $apl02)
    {
        return $user->hasAnyRole(['admin', 'asesor']) && 
               $apl02->status === 'review' &&
               $apl02->is_signed_by_asesi;
    }

    /**
     * Determine whether the user can upload evidence.
     */
    public function uploadEvidence(User $user, Apl02 $apl02)
    {
        return $user->hasRole('asesi') && 
               $apl02->user_id === $user->id && 
               in_array($apl02->status, ['draft', 'returned']);
    }

    /**
     * Determine whether the user can download evidence.
     */
    public function downloadEvidence(User $user, Apl02 $apl02)
    {
        // Admin and asesor can download all evidence
        if ($user->hasAnyRole(['admin', 'asesor'])) {
            return true;
        }

        // Asesi can download their own evidence
        if ($user->hasRole('asesi')) {
            return $apl02->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can export the model.
     */
    public function export(User $user, Apl02 $apl02)
    {
        return $this->view($user, $apl02);
    }
}