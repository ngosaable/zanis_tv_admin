@extends('layouts.admin')

@section('page_title', 'Role Details')
@section('page_subtitle', $role->display_name)

@section('page_actions')
    <a href="{{ route('admin.roles.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Roles
    </a>
    <a href="{{ route('admin.roles.edit', $role) }}" class="dstv-btn dstv-btn-primary">
        <i class="fas fa-edit"></i> Edit Role
    </a>
@endsection

@section('content')
    @php($members = $role->users()->orderBy('name')->get())

    <div class="dstv-card">
        <div class="dstv-card-header" style="background: var(--video-color);">
            <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user-tag"></i> {{ $role->display_name }}</h3>
        </div>
        <div class="dstv-card-body" style="padding: 24px;">
            <table class="dstv-table">
                <tbody>
                    <tr><th style="width: 220px;">System Name</th><td><code>{{ $role->name }}</code></td></tr>
                    <tr><th>Display Name</th><td>{{ $role->display_name }}</td></tr>
                    <tr><th>Description</th><td>{{ $role->description ?: 'N/A' }}</td></tr>
                    <tr><th>Users with this role</th><td>{{ $members->count() }}</td></tr>
                    <tr><th>Created</th><td>{{ $role->created_at?->format('M d, Y H:i') }}</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    @if($members->isNotEmpty())
        <div class="dstv-card" style="margin-top: 24px;">
            <div class="dstv-card-header">
                <h3 class="dstv-card-title"><i class="fas fa-users"></i> Users</h3>
            </div>
            <div class="dstv-card-body" style="padding: 24px;">
                <table class="dstv-table">
                    <thead><tr><th>Name</th><th>Email</th><th></th></tr></thead>
                    <tbody>
                        @foreach($members as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                                <td><a href="{{ route('admin.users.show', $member) }}" class="dstv-action-btn" title="View"><i class="fas fa-eye"></i></a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
