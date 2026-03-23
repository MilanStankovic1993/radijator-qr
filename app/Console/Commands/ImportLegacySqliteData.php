<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class ImportLegacySqliteData extends Command
{
    protected $signature = 'app:import-legacy-sqlite';
    protected $description = 'Import legacy data from sqlite_legacy into the current database';

    public function handle(): int
    {
        $sourceConnection = 'sqlite_legacy';
        $targetConnection = config('database.default');

        $source = DB::connection($sourceConnection);
        $target = DB::connection($targetConnection);

        $tablesToCheck = [
            'users',
            'roles',
            'model_has_roles',
            'qr_labels',
            'qr_label_audits',
        ];

        foreach ($tablesToCheck as $table) {
            $count = $target->table($table)->count();

            if ($count > 0) {
                $this->error("Target table '{$table}' is not empty ({$count}). Import aborted.");
                return self::FAILURE;
            }
        }

        $this->info('Target tables are empty. Starting import...');

        try {
            $target->beginTransaction();
            $target->statement('SET FOREIGN_KEY_CHECKS=0');

            $this->importTable($sourceConnection, $targetConnection, 'roles');
            $this->importTable($sourceConnection, $targetConnection, 'users');
            $this->importTable($sourceConnection, $targetConnection, 'qr_labels');
            $this->importTable($sourceConnection, $targetConnection, 'qr_label_audits');
            $this->importTable($sourceConnection, $targetConnection, 'model_has_roles');

            $target->statement('SET FOREIGN_KEY_CHECKS=1');
            $target->commit();

            $this->info('Legacy import finished successfully.');
            $this->line('Imported counts:');
            $this->line('- users: ' . $target->table('users')->count());
            $this->line('- roles: ' . $target->table('roles')->count());
            $this->line('- model_has_roles: ' . $target->table('model_has_roles')->count());
            $this->line('- qr_labels: ' . $target->table('qr_labels')->count());
            $this->line('- qr_label_audits: ' . $target->table('qr_label_audits')->count());

            return self::SUCCESS;
        } catch (Throwable $e) {
            try {
                $target->statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (Throwable $inner) {
                //
            }

            $target->rollBack();

            $this->error('Import failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    protected function importTable(string $sourceConnection, string $targetConnection, string $table): void
    {
        $sourceRows = DB::connection($sourceConnection)->table($table)->get();

        if ($sourceRows->isEmpty()) {
            $this->line("Skipping {$table} (0 rows).");
            return;
        }

        $targetColumns = Schema::connection($targetConnection)->getColumnListing($table);
        $targetColumnMap = array_flip($targetColumns);

        $payload = [];

        foreach ($sourceRows as $row) {
            $rowArray = (array) $row;
            $filtered = array_intersect_key($rowArray, $targetColumnMap);
            $payload[] = $filtered;
        }

        DB::connection($targetConnection)->table($table)->insert($payload);

        $this->info("Imported {$table}: " . count($payload) . ' rows.');
    }
}