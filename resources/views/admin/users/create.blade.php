@extends('layouts.admin')

@section('page_title', 'Create User')
@section('page_subtitle', 'Add a new user to the platform')

@section('page_actions')
    <a href="{{ route('admin.users.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
@endsection

@section('content')
    @if($errors->any())
        <div class="dstv-alert dstv-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            Please fix the errors below
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="dstv-card">
            <div class="dstv-card-header" style="background: var(--video-color);">
                <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user-plus"></i> Create New User</h3>
            </div>
            <div class="dstv-card-body" style="padding: 24px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" class="dstv-form-input" value="{{ old('name') }}" required placeholder="Enter full name">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" class="dstv-form-input" value="{{ old('email') }}" required placeholder="Enter email address">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password" class="dstv-form-input" required placeholder="Enter password">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Confirm Password <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password_confirmation" class="dstv-form-input" required placeholder="Confirm password">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Role <span style="color: var(--danger);">*</span></label>
                    <select name="role" class="dstv-form-input" required>
                        <option value="">Select a role</option>
                        <option value="super_admin">Super Administrator</option>
                        <option value="content_manager">Content Manager</option>
                        <option value="subscriber">Subscriber</option>
                        <option value="premium_subscriber">Premium Subscriber</option>
                    </select>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('admin.users.index') }}" class="dstv-btn dstv-btn-outline">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dstv-btn dstv-btn-primary">
                <i class="fas fa-save"></i> Create User
            </button>
        </div>
    </form>
@endsection