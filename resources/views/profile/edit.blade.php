@extends('layouts.admin')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-12">

                {{-- Update Profile Information --}}
                <div class="main-card mb-4 shadow-sm">
                    <div class="card-header-custom">
                        @if (session('success'))
                            <div class="alert-success-custom">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">
                                    <i class="bi bi-table me-2"></i>Update User Information
                                </h5>
                                <p class="mb-0 text-muted">Manage and organize User</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body m-3">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                    <hr>
                    <div class="card-body m-3">
                        @include('profile.partials.update-password-form')
                    </div>
                    <hr>
                    <div class="card-body m-3 ">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>

            </div>
        </div>
        </div>
    @endsection
