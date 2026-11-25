@echo off
REM ====================================================================
REM SKRIP PEMULIHAN (RESTORE) BASIS DATA SMAS METHODIST BANDA ACEH
REM Gunakan untuk Disaster Recovery / Point-in-Time Restore
REM ====================================================================

set DB_NAME=methodist_aceh
set DB_USER=root
set DB_PASS=
set DB_HOST=127.0.0.1
set DB_PORT=3306

if "%~1"=="" (
    echo [ERROR] Masukkan nama file cadangan SQL yang ingin dipulihkan!
    echo Contoh penggunaan: restore.bat ..\storage\app\backups\methodist_backup_2026-11-23.sql
    pause
    exit /b 1
)

set BACKUP_FILE=%~1

if not exist "%BACKUP_FILE%" (
    echo [ERROR] File backup "%BACKUP_FILE%" tidak ditemukan!
    pause
    exit /b 1
)

echo [PERINGATAN] Anda akan memulihkan database %DB_NAME% dari %BACKUP_FILE%.
echo Seluruh data saat ini akan ditimpa!
set /p CONFIRM="Ketik Y untuk melanjutkan pemulihan: "

if /i "%CONFIRM%" NEQ "Y" (
    echo [INFO] Pemulihan dibatalkan.
    pause
    exit /b 0
)

echo [INFO] Sedang memulihkan database...
mysql --host=%DB_HOST% --port=%DB_PORT% --user=%DB_USER% %DB_NAME% < "%BACKUP_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo [SUKSES] Database %DB_NAME% berhasil dipulihkan dari %BACKUP_FILE%!
) else (
    echo [GAGAL] Terjadi kesalahan saat memulihkan database!
)
pause
