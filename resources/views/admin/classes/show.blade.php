<div class="mb-3">
    <label class="form-label">Nama Kelas</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $class->name ?? '') }}" placeholder="Contoh: X IPA 1" required>
</div>
<div class="mb-3">
    <label class="form-label">Jenjang</label>
    <select name="grade_level" class="form-select" required>
        @foreach([10 => 'X', 11 => 'XI', 12 => 'XII'] as $val => $label)
            <option value="{{ $val }}" @selected(old('grade_level', $class->grade_level ?? '') == $val)>{{ $label }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Wali Kelas</label>
    <select name="homeroom_teacher_id" class="form-select">
        <option value="">-- Tidak ada --</option>
        @foreach($teachers as $t)
            <option value="{{ $t->nip }}" @selected(old('homeroom_teacher_id', $class->homeroom_teacher_id ?? '') == $t->nip)>{{ $t->user->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Tahun Ajaran</label>
    <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $class->academic_year ?? '2025/2026') }}" placeholder="2025/2026" required>
</div>
