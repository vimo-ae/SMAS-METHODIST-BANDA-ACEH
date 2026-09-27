<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $student->user->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $student->user->email ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">NIS</label>
    <input type="text" name="nis" class="form-control" value="{{ old('nis', $student->nis ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Kelas</label>
    <select name="class_id" class="form-select">
        <option value="">-- Belum ada kelas --</option>
        @foreach($classes as $c)
            <option value="{{ $c->id }}" @selected(old('class_id', $student->class_id ?? '') == $c->id)>{{ $c->name }}</option>
        @endforeach
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Jenis Kelamin</label>
    <select name="gender" class="form-select" required>
        <option value="L" @selected(old('gender', $student->gender ?? '') == 'L')>Laki-laki</option>
        <option value="P" @selected(old('gender', $student->gender ?? '') == 'P')>Perempuan</option>
    </select>
</div>
<div class="mb-3">
    <label class="form-label">Tanggal Lahir</label>
    <input type="date" name="birth_date" class="form-control" value="{{ old('birth_date', $student->birth_date ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">Alamat</label>
    <textarea name="address" class="form-control">{{ old('address', $student->address ?? '') }}</textarea>
</div>
