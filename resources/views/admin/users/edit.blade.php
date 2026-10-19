@extends('layouts.admin')

@section('page_title', 'Edit User')
@section('page_subtitle', 'Update user details and role')

@section('page_actions')
    <a href="{{ route('admin.users.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
@endsection

@section('content')
    @if($errors->any())
        <div class="dstv-alert dstv-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="dstv-card">
            <div class="dstv-card-header" style="background: var(--video-color);">
                <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user-edit"></i> Edit {{ $user->name }}</h3>
            </div>
            <div class="dstv-card-body" style="padding: 24px;">
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Name <span style="color: var(--danger);">*</span></label>
                    <input type="text" name="name" class="dstv-form-input" value="{{ old('name', $user->name) }}" required>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Email <span style="color: var(--danger);">*</span></label>
                    <input type="email" name="email" class="dstv-form-input" value="{{ old('email', $user->email) }}" required>
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">New Password</label>
                    <input type="password" name="password" class="dstv-form-input" placeholder="Leave blank to keep the current password">
                </div>

                <div class="dstv-form-group">
                    <label class="dstv-form-label">Confirm New Password</label>
                    <input type="password" name="password_confirmation" class="dstv-form-input" placeholder="Repeat the new password">
                </div>

                @php
                    $roleOptions = [
                        'super_admin' => 'Super Administrator',
                        'content_manager' => 'Content Manager',
                        'subscriber' => 'Subscriber',
                        'premium_subscriber' => 'Premium Subscriber',
                    ];
                    $currentRole = old('role', $user->role);
                @endphp
                <div class="dstv-form-group">
                    <label class="dstv-form-label">Role <span style="color: var(--danger);">*</span></label>
                    <select name="role" class="dstv-form-input" required>
                        @foreach($roleOptions as $value => $label)
                            <option value="{{ $value }}" @selected($currentRole === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('admin.users.index') }}" class="dstv-btn dstv-btn-outline">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="dstv-btn dstv-btn-primary">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>
    </form>
@endsection
