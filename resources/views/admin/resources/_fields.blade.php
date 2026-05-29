@php
    $resource = $resource ?? new \App\Models\Resource();
    $types = $types ?? config('resources.types', []);
    $categories = $categories ?? collect();
    $selectedCategoryId = old('category_id', $resource->category_id ?? optional($resource->resourceCategory)->id ?? null);
    if (blank($selectedCategoryId) && filled($resource->category)) {
        $selectedCategoryId = optional($categories->firstWhere('name', $resource->category))->id;
    }
    $slugValue = old('slug', $resource->slug ?? '');
    $titleValue = old('title', $resource->title ?? '');
    $formKey = $formKey ?? 'resource';
@endphp

<div class="row g-3 resource-form-grid">
    <div class="col-lg-8">
        <label class="form-label fw-semibold">Title</label>
        <input id="{{ $formKey }}Title" type="text" name="title" class="form-control" value="{{ $titleValue }}" placeholder="Enter resource title" required>
        @error('title')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-lg-4">
        <label class="form-label fw-semibold">Slug</label>
        <input id="{{ $formKey }}Slug" type="text" name="slug" class="form-control" value="{{ $slugValue }}" placeholder="http://127.0.0.1:8000/resources/field-data-to-copilot-checklist">
        <small class="text-muted">Lowercase letters, numbers, and hyphens only.</small>
        @error('slug')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Resource Type</label>
        <select name="resource_type" class="form-select" required>
            <option value="" disabled {{ old('resource_type', $resource->resource_type ?: '') === '' ? 'selected' : '' }}>Select a type</option>
            @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ old('resource_type', $resource->resource_type ?: 'guide') === $key ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('resource_type')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-8">
        <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-1">
            <label class="form-label fw-semibold mb-0">Category</label>
            <a href="{{ route('admin.resource-categories.index') }}" class="btn btn-sm btn-outline-secondary">Manage categories</a>
        </div>
        <select name="category_id" class="form-select" required>
            <option value="" disabled {{ blank($selectedCategoryId) ? 'selected' : '' }}>Select a category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ (string) $selectedCategoryId === (string) $category->id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label fw-semibold">Description</label>
        <textarea
            id="{{ $formKey }}Description"
            name="description"
            rows="5"
            class="form-control resource-description-editor"
            placeholder="Short description shown on resource cards"
        >{{ old('description', $resource->description) }}</textarea>
        @error('description')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">File Upload</label>
        <input type="file" name="file" class="form-control" {{ $resource->exists ? '' : 'required' }}>
        <small class="text-muted">Allowed: PDF, image, and video files.</small>
        @if($resource->file_url)
            <div class="mt-2"><a href="{{ route('admin.resources.download', ['resource' => $resource, 'mode' => 'inline']) }}" target="_blank" rel="noopener">Current file</a></div>
        @endif
        @error('file')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Thumbnail (optional)</label>
        <input type="file" name="thumbnail" class="form-control">
        <small class="text-muted">Recommended for better card previews.</small>
        @if($resource->thumbnail_url)
            <div class="mt-2 resource-thumb-preview">
                <img src="{{ $resource->thumbnail_url }}" alt="Thumbnail">
            </div>
        @endif
        @error('thumbnail')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex flex-wrap gap-3 mt-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_published" value="1" id="{{ $formKey }}Published" {{ old('is_published', $resource->is_published) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $formKey }}Published">Published</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="{{ $formKey }}Featured" {{ old('is_featured', $resource->is_featured) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $formKey }}Featured">Featured</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_noindex" value="1" id="{{ $formKey }}Noindex" {{ old('is_noindex', $resource->is_noindex) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $formKey }}Noindex">Noindex</label>
        </div>
    </div>
</div>

@once
    @push('styles')
    <style>
        .resource-thumb-preview {
            max-width: 180px;
            border-radius: 12px;
            border: 1px solid #d7dfef;
            overflow: hidden;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.08);
        }
        .resource-thumb-preview img {
            display: block;
            width: 100%;
            height: auto;
        }
            .resource-form-grid .form-select option[disabled] {
                color: #94a3b8;
            }
    </style>
    @endpush

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof CKEDITOR !== 'undefined') {
            document.querySelectorAll('.resource-description-editor').forEach(function (textarea) {
                if (!textarea.id) {
                    return;
                }

                if (CKEDITOR.instances[textarea.id]) {
                    CKEDITOR.instances[textarea.id].destroy(true);
                }

                CKEDITOR.replace(textarea.id, {
                    height: 220,
                    removeButtons: 'Subscript,Superscript',
                    toolbarGroups: [
                        { name: 'document', groups: ['mode', 'document', 'doctools'] },
                        { name: 'clipboard', groups: ['clipboard', 'undo'] },
                        { name: 'editing', groups: ['find', 'selection', 'spellchecker'] },
                        { name: 'basicstyles', groups: ['basicstyles', 'cleanup'] },
                        { name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align'] },
                        { name: 'links' },
                        { name: 'insert' },
                        { name: 'styles' },
                        { name: 'colors' },
                    ],
                });
            });
        }

        document.querySelectorAll('[id$="Title"]').forEach(function (titleInput) {
            const formKey = titleInput.id.replace(/Title$/, '');
            const slugInput = document.getElementById(formKey + 'Slug');
            if (!slugInput || slugInput.dataset.slugInit === '1') {
                return;
            }

            slugInput.dataset.slugInit = '1';
            let slugTouched = slugInput.value.trim() !== '';

            slugInput.addEventListener('input', function () {
                slugTouched = true;
            });

            titleInput.addEventListener('input', function () {
                if (slugTouched) {
                    return;
                }

                const generated = titleInput.value
                    .toLowerCase()
                    .trim()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-');

                slugInput.value = generated;
            });
        });
    });
    </script>
@endonce