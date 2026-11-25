<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClassSubjectTeacher extends Model
{
    use HasFactory;

    protected $table = 'class_subject_teacher';
    protected $primaryKey = 'cst_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'cst_id',
        'class_id',
        'subject_code',
        'teacher_id',
        'academic_year',
        'semester',
        'join_code',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (empty($model->cst_id)) {
                $model->cst_id = 'CST-' . strtoupper(Str::random(6));
            }

            self::generateJoinCode($model);
        });

        static::updating(function (self $model): void {
            if ($model->isDirty(['class_id', 'subject_code']) || empty($model->join_code)) {
                self::generateJoinCode($model);
            }
        });
    }

    public static function makeJoinCode(string $subjectCode, string $className, int $classId): string
    {
        $normalized = strtoupper(trim($className));
        $normalized = preg_replace('/\bXII\b/', '12', $normalized);
        $normalized = preg_replace('/\bXI\b/', '11', $normalized);
        $normalized = preg_replace('/\bX\b/', '10', $normalized);

        $normalized = preg_replace('/\bIPAS\s+([AB])\b/', '$1', $normalized);
        $normalized = preg_replace('/\bIPS\b/', 'B', $normalized);
        $normalized = preg_replace('/\bIPA\b/', 'A', $normalized);

        $cleanClass = preg_replace('/[^A-Z0-9]/', '', $normalized);
        $code = strtoupper(trim($subjectCode)) . $cleanClass;

        if (strlen($code) > 8) {
            $code = strtoupper(trim($subjectCode)) . $classId;
        }

        return $code;
    }

    private static function generateJoinCode(self $model): void
    {
        $class = SchoolClass::query()->find($model->class_id);
        $subject = Subject::query()->find($model->subject_code);

        if ($class && $subject) {
            $model->join_code = self::makeJoinCode(
                (string) $subject->code,
                (string) $class->name,
                (int) $model->class_id
            );
        } elseif (empty($model->join_code)) {
            $model->join_code = strtoupper(Str::random(6));
        }
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_code', 'code');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'nip');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'cst_id', 'cst_id');
    }

    public function materials()
    {
        return $this->hasMany(Material::class, 'cst_id', 'cst_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'cst_id', 'cst_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'cst_id', 'cst_id');
    }

    public function joinedStudents()
    {
        return $this->belongsToMany(
            Student::class,
            'cst_students',
            'cst_id',
            'students_id',
            'cst_id',
            'nis'
        )->withPivot(['status', 'joined_at', 'left_at']);
    }

    public function label(): string
    {
        return ($this->subject->name ?? $this->subject_code)
            . ' — '
            . ($this->schoolClass->name ?? '-');
    }

    public function enrollments()
    {
        return $this->hasMany(ClassEnrollment::class, 'cst_id', 'cst_id');
    }
}
