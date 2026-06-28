@extends('layouts.admin')

@section('page_title', 'Roles')
@section('page_subtitle', 'Manage user roles and permissions')

@section('page_actions')
    <a href="{{ route('admin.roles.create') }}" class="dstv-btn dstv-btn-primary">
        <i class="fas fa-plus"></i> Create New Role
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
            <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user-tag"></i> All Roles</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            @if($roles->isEmpty())
                <div class="dstv-empty">
                    <i class="fas fa-user-tag"></i>
                    <h3>No roles found</h3>
                    <p>Create your first role by clicking the "Create New Role" button above.</p>
                </div>
            @else
                <div class="dstv-table-responsive">
                    <table class="dstv-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Display Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->display_name }}</td>
                                    <td>{{ $role->description ?? 'N/A' }}</td>
                                    <td>{{ $role->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="dstv-action-group">
                                            <a href="{{ route('admin.roles.show', $role->id) }}" class="dstv-action-btn" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="dstv-action-btn" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
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