{{-- resources/views/admin/workshops/_materials.blade.php --}}
@php($current = $current ?? collect())
<div class="mb-3">
    <label class="form-label">Matériels & quantités</label>
    <div class="border rounded p-2" style="max-height: 240px; overflow:auto;">
        @foreach($materials as $m)
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox"
                           name="material_ids[]" id="mat{{ $m->id }}" value="{{ $m->id }}"
                        @checked($current->has($m->id))>
                    <label class="form-check-label" for="mat{{ $m->id }}">
                        {{ $m->name }} <small class="text-muted">({{ $m->stock }} {{ $m->unit }})</small>
                    </label>
                </div>
                <input type="number" class="form-control form-control-sm ms-auto"
                       style="width:100px"
                       name="quantities[{{ $m->id }}]"
                       value="{{ $current->get($m->id, 1) }}" min="1">
            </div>
        @endforeach
    </div>
    <div class="form-text">Coche le matériel et ajuste la quantité.</div>
</div>
