<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DatabaseRecoveryService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RecoveryController extends Controller
{
    protected DatabaseRecoveryService $recoveryService;

    public function __construct(DatabaseRecoveryService $recoveryService)
    {
        $this->recoveryService = $recoveryService;
    }

    /**
     * Tampilkan halaman Pusat Pemulihan & Cadangan Basis Data.
     */
    public function index()
    {
        $backups = $this->recoveryService->listBackups();
        $stats = $this->recoveryService->getDatabaseStats();

        return view('admin.recovery.index', compact('backups', 'stats'));
    }

    /**
     * Buat cadangan penuh database baru.
     */
    public function backup(): RedirectResponse
    {
        try {
            $result = $this->recoveryService->createBackup();
            return redirect()->route('admin.recovery.index')
                ->with('success', "Cadangan database berhasil dibuat: {$result['filename']} ({$result['size']}) via {$result['method']}.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal membuat cadangan database: ' . $e->getMessage());
        }
    }

    /**
     * Pulihkan database dari file cadangan SQL.
     */
    public function restore(Request $request): RedirectResponse
    {
        $request->validate([
            'filename' => ['required', 'string'],
        ]);

        try {
            $result = $this->recoveryService->restoreBackup($request->filename);
            return redirect()->route('admin.recovery.index')
                ->with('success', $result['message']);
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }

    /**
     * Unduh file cadangan SQL.
     */
    public function download(string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = $this->recoveryService->getBackupPath($filename);

        if (!$path) {
            return back()->with('error', 'Berkas cadangan tidak ditemukan.');
        }

        return response()->download($path, $filename, [
            'Content-Type' => 'application/sql',
        ]);
    }

    /**
     * Hapus file cadangan.
     */
    public function destroy(string $filename): RedirectResponse
    {
        if ($this->recoveryService->deleteBackup($filename)) {
            return redirect()->route('admin.recovery.index')
                ->with('success', "Berkas cadangan {$filename} berhasil dihapus.");
        }

        return back()->with('error', 'Gagal menghapus berkas cadangan.');
    }

    /**
     * Pulihkan data granular (1 rekaman baris) langsung dari tabel audit log.
     */
    public function recoverRecord(Request $request, string $table, int $id): RedirectResponse
    {
        try {
            $result = $this->recoveryService->recoverRecordFromLog($table, $id);
            return redirect()->route('admin.activity-logs.show', [$table, $id])
                ->with('recovery_success', "Berhasil memulihkan data tabel {$table} (Aksi: {$result['action']}) dari snapshot Log #{$id}.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memulihkan data rekaman: ' . $e->getMessage());
        }
    }
}
