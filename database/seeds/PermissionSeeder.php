<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'exam-list',
            'exam-create',
            'exam-edit',
            'exam-delete',
            'exam-question-list',
            'exam-question-create',
            'exam-question-edit',
            'exam-question-delete',
            'exam-choice-list',
            'exam-choice-create',
            'exam-choice-edit',
            'exam-choice-delete',
            'exam-answer-sheet-list',
            'exam-answer-sheet-create',
            'exam-answer-sheet-edit',
            'exam-answer-sheet-delete',
            'exam-result-list',
            'exam-result-create',
            'exam-result-edit',
            'exam-result-delete',
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'activity-logs',
            
         ];
 
 
         foreach ($permissions as $permission) {
              Permission::firstOrCreate(['name' => $permission]);
         }
    }
}
