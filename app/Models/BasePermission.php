<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BasePermission extends Model
{
    protected $fillable = ['espai_id', 'nom'];

    public function roles()
    {
        return $this->belongsToMany(
            BaseRole::class,
            'base_role_permission',
            'base_permission_id',
            'base_role_id'
        );
    }

    /**
     * Nom formatat per mostrar a la UI
     * Exemple: users.view → Veure Usuaris
     */
    public function getNomFormatAttribute()
    {
        $parts = explode('.', $this->nom);

        $module = $parts[0] ?? '';
        $action = $parts[1] ?? '';

        $modules = [
            'users' => 'Usuaris',
            'groups' => 'Grups',
            'students' => 'Alumnes',
            'aulas' => 'Aules',
            'noticies' => 'Notícies',
            'guardies' => 'Guardies',
            'tickets' => 'Tiquets',
            'roles' => 'Rols',
            'permissions' => 'Permisos',
        ];

        $actions = [
            'view' => 'Veure',
            'create' => 'Crear',
            'update' => 'Editar',
            'delete' => 'Eliminar',
            'manage' => 'Gestionar',
            'import' => 'Importar',
            'export' => 'Exportar',
            'reaccionar' => 'Reaccionar',
        ];

        if ($module === 'aulas' && $action === 'horari') {
            return 'Actualitzar Horari d’Aules';
        }

        $moduleName = $modules[$module] ?? ucfirst($module);
        $actionName = $actions[$action] ?? ucfirst($action);

        return "$actionName $moduleName";
    }
}
