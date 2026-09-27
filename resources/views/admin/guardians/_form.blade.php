@php $selectedIds = old('student_ids', isset($guardian) ? $guardian->students->pluck('nis')->toArray() : []); @endphp
<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $guardian->user->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $guardian->user->email ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">No. HP</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $guardian->user->phone ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Hubungan</label>
    <select name="relationship" class="form-select">
        @foreach(['Ayah', 'Ibu', 'Wali'] as $rel)
            <option value="{{ $rel }}" @selected(old('relationship', $guardian->relationship ?? '') == $rel)>{{ $rel }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Pekerjaan</label>
    <input type="text" name="occupation" class="form-control" value="{{ old('occupation', $guardian->occupation ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="address" class="form-control">{{ old('address', $guardian->address ?? '') }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Anak (bisa pilih lebih dari satu)</label>
    <select name="student_ids[]" class="form-select" multiple size="6" required>
        @foreach($students as $s)
            <option value="{{  $s->nis }}" @selected(in_array( $s->nis, $selectedIds))>{{ $s->user->name }} ({{ $s->nis }})</option>
        @endforeach
    </select>
    <small class="text-muted">Tahan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu.</small>
</div>
