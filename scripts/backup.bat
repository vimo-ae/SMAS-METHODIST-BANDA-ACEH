@echo off
REM ====================================================================
REM SKRIP OTOMATISASI CADANGAN (BACKUP) BASIS DATA SMAS METHODIST BANDA ACEH
REM Dapat dijalankan manual atau via Windows Task Scheduler (Daily/Weekly)
REM ====================================================================

set DB_NAME=methodist_aceh
set DB_USER=root
set DB_PASS=
set DB_HOST=127.0.0.1
set DB_PORT=3306

set BACKUP_DIR=..\storage\app\backups
if not exist "%BACKUP_DIR%" mkdir "%BACKUP_DIR%"

for /f "tokens=2 delims==" %%I in ('wmic os get localdatetime /value') do set datetime=%%I
set TIMESTAMP=%datetime:~0,4%-%datetime:~4,2%-%datetime:~6,2%_%datetime:~8,2%-%datetime:~10,2%-%datetime:~12,2%
set BACKUP_FILE=%BACKUP_DIR%\methodist_backup_%TIMESTAMP%.sql

echo [INFO] Memulai proses backup database %DB_NAME% ke %BACKUP_FILE%...

mysqldump --host=%DB_HOST% --port=%DB_PORT% --user=%DB_USER% --routines --triggers --events --single-transaction --quick --add-drop-table %DB_NAME% > "%BACKUP_FILE%"

if %ERRORLEVEL% EQU 0 (
    echo [SUKSES] Backup berhasil dibuat: %BACKUP_FILE%
) else (
    echo [GAGAL] Terjadi kesalahan saat melakukan backup database!
)
pause
