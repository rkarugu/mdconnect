<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckSchema extends Command
{
    protected $signature = 'check:schema';
    protected $description = 'Check database schema for medical_workers table';

    public function handle()
    {
        $this->info('Checking medical_workers table structure...');
        
        try {
            $columns = DB::select('DESCRIBE medical_workers');
            $this->info('Columns in medical_workers table:');
            foreach ($columns as $column) {
                $this->info("- {$column->Field} ({$column->Type})");
            }
            
            // Check if the column exists
            $hasSpecialtyId = Schema::hasColumn('medical_workers', 'specialty_id');
            $hasMedicalSpecialtyId = Schema::hasColumn('medical_workers', 'medical_specialty_id');
            
            $this->info('');
            $this->info('Column existence check:');
            $this->info("specialty_id exists: " . ($hasSpecialtyId ? 'YES' : 'NO'));
            $this->info("medical_specialty_id exists: " . ($hasMedicalSpecialtyId ? 'YES' : 'NO'));
            
        } catch (\Exception $e) {
            $this->error('Error checking schema: ' . $e->getMessage());
        }
        
        return 0;
    }
}
