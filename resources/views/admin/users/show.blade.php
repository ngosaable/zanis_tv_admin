@extends('layouts.admin')

@section('page_title', 'User Details')
@section('page_subtitle', $user->email)

@section('page_actions')
    <a href="{{ route('admin.users.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Users
    </a>
    <a href="{{ route('admin.users.edit', $user) }}" class="dstv-btn dstv-btn-primary">
        <i class="fas fa-edit"></i> Edit User
    </a>
@endsection

@section('content')
    <div class="dstv-card">
        <div class="dstv-card-header" style="background: var(--video-color);">
            <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user"></i> {{ $user->name }}</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <table class="dstv-table">
                <tbody>
                    <tr><th style="width: 220px;">ID</th><td>{{ $user->id }}</td></tr>
                    <tr><th>Name</th><td>{{ $user->name }}</td></tr>
                    <tr><th>Email</th><td>{{ $user->email }}</td></tr>
                    <tr><th>Role</th><td><span class="dstv-badge dstv-badge-info">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span></td></tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            @if($user->is_active)
                                <span class="dstv-badge dstv-badge-success">Active</span>
                            @else
                                <span class="dstv-badge dstv-badge-error">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th>Email Verified</th><td>{{ $user->email_verified_at ? $user->email_verified_at->format('M d, Y H:i') : 'No' }}</td></tr>
                    <tr><th>Created</th><td>{{ $user->created_at?->format('M d, Y H:i') }}</td></tr>
                    <tr><th>Last Updated</th><td>{{ $user->updated_at?->format('M d, Y H:i') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
