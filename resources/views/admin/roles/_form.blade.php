{{-- Shared fields for roles/create and roles/edit --}}
<div class="dstv-form-group">
    <label class="dstv-form-label">System Name <span style="color: var(--danger);">*</span></label>
    <input type="text" name="name" class="dstv-form-input" value="{{ old('name', $role->name ?? '') }}" required placeholder="e.g. content_manager">
    <small style="color: var(--text-muted, #888);">Lowercase with underscores. This is what is stored on each user.</small>
</div>

<div class="dstv-form-group">
    <label class="dstv-form-label">Display Name <span style="color: var(--danger);">*</span></label>
    <input type="text" name="display_name" class="dstv-form-input" value="{{ old('display_name', $role->display_name ?? '') }}" required placeholder="e.g. Content Manager">
</div>

<div class="dstv-form-group">
    <label class="dstv-form-label">Description</label>
    <textarea name="description" class="dstv-form-input" rows="4" placeholder="What can people with this role do?">{{ old('description', $role->description ?? '') }}</textarea>
</div>
