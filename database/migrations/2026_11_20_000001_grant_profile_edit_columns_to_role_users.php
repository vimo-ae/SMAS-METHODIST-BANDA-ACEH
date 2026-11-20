<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const HOSTS = ['localhost', '127.0.0.1'];

    private const ROLE_COLUMNS = [
        'mysql_siswa' => [
            'users' => [
                'email',
                'email_verified_at',
                'phone',
                'password',
                'updated_at',
            ],
            'students' => [
                'address',
                'district',
                'city',
                'province',
                'parent_name',
                'parent_phone',
                'updated_at',
            ],
        ],

        'mysql_guru' => [
            'users' => [
                'email',
                'email_verified_at',
                'phone',
                'password',
                'updated_at',
            ],
            'teachers' => [
                'address',
                'updated_at',
            ],
        ],
    ];

    public function up(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            throw new RuntimeException(
                'Migration ini hanya mendukung MySQL.'
            );
        }

        $pdo = $connection->getPdo();
        $database = $this->quoteIdentifier(
            $connection->getDatabaseName()
        );

        foreach (self::ROLE_COLUMNS as $connectionName => $tables) {
            $username = config(
                "database.connections.{$connectionName}.username"
            );

            if (!is_string($username) || trim($username) === '') {
                throw new RuntimeException(
                    "Username {$connectionName} belum diatur di .env."
                );
            }

            foreach (self::HOSTS as $host) {
                $account = $pdo->quote($username)
                    . '@'
                    . $pdo->quote($host);

                foreach ($tables as $table => $columns) {
                    $columnList = implode(', ', array_map(
                        fn ($column) => $this->quoteIdentifier($column),
                        $columns
                    ));

                    $qualifiedTable = $database . '.'
                        . $this->quoteIdentifier($table);

                    $connection->unprepared(
                        "GRANT UPDATE ({$columnList}) ON {$qualifiedTable} TO {$account}"
                    );
                }
            }
        }

        $connection->unprepared('FLUSH PRIVILEGES');
    }

    public function down(): void
    {
        $connection = DB::connection();

        if ($connection->getDriverName() !== 'mysql') {
            throw new RuntimeException(
                'Migration ini hanya mendukung MySQL.'
            );
        }

        $pdo = $connection->getPdo();
        $database = $this->quoteIdentifier(
            $connection->getDatabaseName()
        );

        foreach (self::ROLE_COLUMNS as $connectionName => $tables) {
            $username = config(
                "database.connections.{$connectionName}.username"
            );

            if (!is_string($username) || trim($username) === '') {
                continue;
            }

            foreach (self::HOSTS as $host) {
                $account = $pdo->quote($username)
                    . '@'
                    . $pdo->quote($host);

                foreach ($tables as $table => $columns) {
                    $columnList = implode(', ', array_map(
                        fn ($column) => $this->quoteIdentifier($column),
                        $columns
                    ));

                    $qualifiedTable = $database . '.'
                        . $this->quoteIdentifier($table);

                    $connection->unprepared(
                        "REVOKE UPDATE ({$columnList}) ON {$qualifiedTable} FROM {$account}"
                    );
                }
            }
        }

        $connection->unprepared('FLUSH PRIVILEGES');
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }
};