@extends('layouts.admin')

@section('page_title', 'Users')
@section('page_subtitle', 'Manage platform users')

@section('page_actions')
    <a href="{{ route('admin.users.create') }}" class="dstv-btn dstv-btn-primary">
        <i class="fas fa-user-plus"></i> Add New User
    </a>
@endsection

@section('content')
    @if($errors->any())
        <div class="dstv-alert dstv-alert-error">
            <i class="fas fa-exclamation-circle"></i>
            Please fix the errors below
        </div>
    @endif

    <div class="dstv-card">
        <div class="dstv-card-header" style="background: var(--video-color);">
            <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-users"></i> All Users</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            @if($users->isEmpty())
                <div class="dstv-empty">
                    <i class="fas fa-users"></i>
                    <h3>No users found</h3>
                    <p>Create your first user by clicking the "Add New User" button above.</p>
                </div>
            @else
                <div class="dstv-table-responsive">
                    <table class="dstv-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="dstv-badge dstv-badge-{{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="dstv-badge dstv-badge-success">Active</span>
                                        @else
                                            <span class="dstv-badge dstv-badge-error">Inactive</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dstv-action-group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" class="dstv-action-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" class="dstv-action-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dstv-action-btn dstv-action-btn--delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection