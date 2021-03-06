<?php
  
namespace Database\Seeders;
  
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
  
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
           'role-list',
           'role-create',
           'role-edit',
           'role-delete',
           'user-list',
           'user-create',
           'user-edit',
           'user-delete',
           'professor-list',
           'professor-create',
           'professor-edit',
           'professor-delete',
           'aluno-list',
           'aluno-create',
           'aluno-edit',
           'aluno-delete',
           'aula-list',
           'aula-create',
           'aula-edit',
           'aula-delete',
           'aula-register',
           'aula-accept',
           'aula-cancel',
           'aula-reject'
        ];
     
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}