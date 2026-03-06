<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * CustomReportQueryBuilder
 *
 * Translates a frontend report-builder config into a real query
 * and returns an array of associative-array rows ready for export.
 *
 * Config shape (comes from validated request):
 * {
 *   "source"  : "trainees" | "enrollments" | "attendance" | "assessments" | "certificates" | "trainers",
 *   "columns" : ["col_key", ...],          // subset of the source's available columns
 *   "filters" : [                          // zero or more filter rows
 *     { "column": "col_key", "operator": "="|"!="|">"|"<"|"like"|"between", "value": "...", "value2": "..." }
 *   ],
 *   "sort_by"  : "col_key",
 *   "sort_dir" : "asc" | "desc",
 *   "limit"    : null | int               // null = unlimited (premium only)
 * }
 */
class CustomReportQueryBuilder
{
    // ── Data-source registry ──────────────────────────────────────────────────
    // Each source defines:
    //   table      : primary DB table
    //   joins      : optional eager joins (raw SQL fragments)
    //   columns    : map of  key => [label, sql_expression]
    //   searchable : columns allowed in filters

    protected static array $sources = [

        'trainees' => [
            'label'   => 'Trainees',
            'table'   => 'trainees',
            'joins'   => [],
            'columns' => [
                'id'           => ['ID',             'trainees.id'],
                'name'         => ['Full Name',       'trainees.name'],
                'email'        => ['Email',           'trainees.email'],
                'phone'        => ['Phone',           'trainees.phone'],
                'address'      => ['Address',         'trainees.address'],
                'gender'       => ['Gender',          'trainees.gender'],
                'birthdate'    => ['Birthdate',       'trainees.birthdate'],
                'created_at'   => ['Date Registered', "DATE_FORMAT(trainees.created_at,'%Y-%m-%d')"],
            ],
        ],

        'trainers' => [
            'label'   => 'Trainers',
            'table'   => 'trainers',
            'joins'   => [],
            'columns' => [
                'id'           => ['ID',             'trainers.id'],
                'name'         => ['Full Name',       'trainers.name'],
                'email'        => ['Email',           'trainers.email'],
                'phone'        => ['Phone',           'trainers.phone'],
                'specialization' => ['Specialization','trainers.specialization'],
                'created_at'   => ['Date Added',      "DATE_FORMAT(trainers.created_at,'%Y-%m-%d')"],
            ],
        ],

        'enrollments' => [
            'label'   => 'Enrollments',
            'table'   => 'enrollments',
            'joins'   => [
                "LEFT JOIN trainees ON trainees.id = enrollments.trainee_id",
                "LEFT JOIN courses  ON courses.id  = enrollments.course_id",
            ],
            'columns' => [
                'id'            => ['Enrollment ID',  'enrollments.id'],
                'trainee_name'  => ['Trainee',        'trainees.name'],
                'course_name'   => ['Course',         'courses.name'],
                'course_level'  => ['Level',          'courses.level'],
                'status'        => ['Status',         'enrollments.status'],
                'enrolled_at'   => ['Enrolled At',    "DATE_FORMAT(enrollments.created_at,'%Y-%m-%d')"],
                'completed_at'  => ['Completed At',   "DATE_FORMAT(enrollments.completed_at,'%Y-%m-%d')"],
            ],
        ],

        'attendance' => [
            'label'   => 'Attendance',
            'table'   => 'attendances',
            'joins'   => [
                "LEFT JOIN enrollments ON enrollments.id = attendances.enrollment_id",
                "LEFT JOIN trainees    ON trainees.id    = enrollments.trainee_id",
                "LEFT JOIN courses     ON courses.id     = enrollments.course_id",
                "LEFT JOIN training_schedules ON training_schedules.id = attendances.schedule_id",
            ],
            'columns' => [
                'id'            => ['Record ID',      'attendances.id'],
                'trainee_name'  => ['Trainee',        'trainees.name'],
                'course_name'   => ['Course',         'courses.name'],
                'date'          => ['Date',           "DATE_FORMAT(attendances.date,'%Y-%m-%d')"],
                'status'        => ['Status',         'attendances.status'],
                'remarks'       => ['Remarks',        'attendances.remarks'],
            ],
        ],

        'assessments' => [
            'label'   => 'Assessments',
            'table'   => 'assessments',
            'joins'   => [
                "LEFT JOIN enrollments ON enrollments.id = assessments.enrollment_id",
                "LEFT JOIN trainees    ON trainees.id    = enrollments.trainee_id",
                "LEFT JOIN courses     ON courses.id     = enrollments.course_id",
            ],
            'columns' => [
                'id'            => ['Assessment ID',  'assessments.id'],
                'trainee_name'  => ['Trainee',        'trainees.name'],
                'course_name'   => ['Course',         'courses.name'],
                'score'         => ['Score',          'assessments.score'],
                'max_score'     => ['Max Score',      'assessments.max_score'],
                'result'        => ['Result',         'assessments.result'],
                'assessed_at'   => ['Date',           "DATE_FORMAT(assessments.created_at,'%Y-%m-%d')"],
            ],
        ],

        'certificates' => [
            'label'   => 'Certificates',
            'table'   => 'certificates',
            'joins'   => [
                "LEFT JOIN enrollments ON enrollments.id = certificates.enrollment_id",
                "LEFT JOIN trainees    ON trainees.id    = enrollments.trainee_id",
                "LEFT JOIN courses     ON courses.id     = enrollments.course_id",
            ],
            'columns' => [
                'id'                 => ['Certificate ID',    'certificates.id'],
                'certificate_number' => ['Certificate No.',   'certificates.certificate_number'],
                'trainee_name'       => ['Trainee',           'trainees.name'],
                'course_name'        => ['Course',            'courses.name'],
                'issued_at'          => ['Issued At',         "DATE_FORMAT(certificates.issued_at,'%Y-%m-%d')"],
                'expires_at'         => ['Expires At',        "DATE_FORMAT(certificates.expires_at,'%Y-%m-%d')"],
            ],
        ],
    ];

    // ── Operators allowed in filters ──────────────────────────────────────────
    protected static array $operators = [
        '='       => '=',
        '!='      => '!=',
        '>'       => '>',
        '<'       => '<',
        '>='      => '>=',
        '<='      => '<=',
        'like'    => 'LIKE',
        'between' => 'BETWEEN',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // Public API
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Return all source definitions for the frontend (labels + available columns).
     */
    public static function schema(): array
    {
        $out = [];
        foreach (self::$sources as $key => $def) {
            $cols = [];
            foreach ($def['columns'] as $colKey => [$label]) {
                $cols[] = ['key' => $colKey, 'label' => $label];
            }
            $out[] = ['key' => $key, 'label' => $def['label'], 'columns' => $cols];
        }
        return $out;
    }

    /**
     * Run the query and return rows as array of assoc arrays.
     *
     * @throws \InvalidArgumentException
     */
    public function run(array $config, ?int $limit = null): array
    {
        $source  = $config['source']  ?? null;
        $columns = $config['columns'] ?? [];
        $filters = $config['filters'] ?? [];
        $sortBy  = $config['sort_by']  ?? null;
        $sortDir = strtolower($config['sort_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        // ── Validate source ───────────────────────────────────────────────
        if (!$source || !isset(self::$sources[$source])) {
            throw new \InvalidArgumentException("Invalid data source: {$source}");
        }

        $def = self::$sources[$source];

        // ── Validate & resolve columns ────────────────────────────────────
        $availableCols = $def['columns'];

        if (empty($columns)) {
            // Default: all columns
            $columns = array_keys($availableCols);
        } else {
            $columns = array_filter($columns, fn($c) => isset($availableCols[$c]));
        }

        if (empty($columns)) {
            throw new \InvalidArgumentException('No valid columns selected.');
        }

        // Build SELECT expressions
        $selects = [];
        $labels  = [];
        foreach ($columns as $colKey) {
            [$label, $expr] = $availableCols[$colKey];
            $selects[]       = DB::raw("{$expr} as `{$colKey}`");
            $labels[$colKey] = $label;
        }

        // ── Build query ───────────────────────────────────────────────────
        $query = DB::table($def['table'])->select($selects);

        foreach ($def['joins'] as $join) {
            $query->joinRaw($join);
        }

        // ── Apply filters ─────────────────────────────────────────────────
        foreach ($filters as $filter) {
            $colKey   = $filter['column']   ?? null;
            $operator = $filter['operator'] ?? '=';
            $value    = $filter['value']    ?? '';
            $value2   = $filter['value2']   ?? '';

            if (!$colKey || !isset($availableCols[$colKey])) continue;
            if (!isset(self::$operators[$operator])) continue;

            [, $expr] = $availableCols[$colKey];
            $sqlOp    = self::$operators[$operator];

            if ($operator === 'like') {
                $query->whereRaw("{$expr} LIKE ?", ["%{$value}%"]);
            } elseif ($operator === 'between') {
                if ($value !== '' && $value2 !== '') {
                    $query->whereBetween(DB::raw($expr), [$value, $value2]);
                }
            } else {
                if ($value !== '') {
                    $query->whereRaw("{$expr} {$sqlOp} ?", [$value]);
                }
            }
        }

        // ── Sort ──────────────────────────────────────────────────────────
        if ($sortBy && isset($availableCols[$sortBy])) {
            [, $sortExpr] = $availableCols[$sortBy];
            $query->orderByRaw("{$sortExpr} {$sortDir}");
        }

        // ── Limit ─────────────────────────────────────────────────────────
        if ($limit !== null) {
            $query->limit($limit);
        }

        // ── Fetch & remap column keys → human labels ──────────────────────
        $rows = $query->get()->map(function ($row) use ($labels) {
            $out = [];
            foreach ($labels as $key => $label) {
                $out[$label] = $row->$key ?? '';
            }
            return $out;
        })->toArray();

        return $rows;
    }
}