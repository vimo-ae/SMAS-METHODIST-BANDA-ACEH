<div class="mb-3">
    <label class="form-label">Nama Mapel</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $subject->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Kode Mapel</label>
    <input type="text" name="code" class="form-control" value="{{ old('code', $subject->code ?? '') }}" placeholder="Contoh: MTK" required>
</div>
