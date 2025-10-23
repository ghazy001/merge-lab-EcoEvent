@csrf
<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title"
           value="{{ old('title', $cause->title ?? '') }}" required>
    @error('title')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $cause->description ?? '') }}</textarea>
    @error('description')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="goal_amount" class="form-label">Goal Amount (€)</label>
    <input type="number" class="form-control" id="goal_amount" name="goal_amount"
           value="{{ old('goal_amount', $cause->goal_amount ?? '') }}" min="0" step="0.01" required>
    @error('goal_amount')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    @php $status = old('status', $cause->status ?? 'active') @endphp
    <select class="form-control" id="status" name="status" required>
        <option value="active" {{ $status=='active' ? 'selected' : '' }}>Active</option>
        <option value="completed" {{ $status=='completed' ? 'selected' : '' }}>Completed</option>
        <option value="canceled" {{ $status=='canceled' ? 'selected' : '' }}>Canceled</option>
    </select>
    @error('status')<div class="text-danger">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">Image (jpg, png, webp) — max 2MB</label>
    <input type="file" class="form-control" id="image" name="image" accept="image/*">
    @error('image')<div class="text-danger">{{ $message }}</div>@enderror

    @isset($cause)
        @if($cause->image_path)
            <div class="mt-2 d-flex align-items-center gap-3">
                <img src="{{ asset('storage/'.$cause->image_path) }}" alt="Current image" class="rounded" style="height:80px">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image">
                    <label class="form-check-label" for="remove_image">Remove current image</label>
                </div>
            </div>
        @endif
    @endisset
</div>

<button type="submit" class="btn btn-success">{{ $buttonText }}</button>
<a href="{{ route('admin.causes.index') }}" class="btn btn-secondary">Cancel</a>
