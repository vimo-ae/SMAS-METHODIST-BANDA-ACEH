<div class="mb-3">
    <label class="form-label">Nama</label>
    <input type="text" name="name" class="form-control" value="{{ old('name', $teacher->user->name ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $teacher->user->email ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">No. HP</label>
    <input type="text" name="phone" class="form-control" value="{{ old('phone', $teacher->user->phone ?? '') }}">
</div>
<div class="mb-3">
    <label class="form-label">NIP</label>
    <input type="text" name="nip" class="form-control" value="{{ old('nip', $teacher->nip ?? '') }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Spesialisasi / Mapel</label>
    <input type="text" name="specialization" class="form-control" value="{{ old('specialization', $teacher->specialization ?? '') }}">
</div>
