@extends('layouts.admin')

@section('page_title', 'Create Role')
@section('page_subtitle', 'Add a new user role')

@section('page_actions')
    <a href="{{ route('admin.roles.index') }}" class="dstv-btn dstv-btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Roles
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

    <form method="POST" action="{{ route('admin.roles.store') }}">
        @csrf
        <div class="dstv-card">
            <div class="dstv-card-header" style="background: var(--video-color);">
                <h3 class="dstv-card-title" style="color: white;"><i class="fas fa-user-tag"></i> New Role</h3>
            </div>
            <div class="dstv-card-body" style="padding: 24px;">
                @include('admin.roles._form', ['role' => null])
            </div>
        </div>

        <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 24px;">
            <a href="{{ route('admin.roles.index') }}" class="dstv-btn dstv-btn-outline"><i class="fas fa-times"></i> Cancel</a>
            <button type="submit" class="dstv-btn dstv-btn-primary"><i class="fas fa-save"></i> Create Role</button>
        </div>
    </form>
@endsection
