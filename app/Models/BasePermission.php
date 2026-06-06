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
            'users'       => __('messages.users'),
            'groups'      => __('messages.groups'),
            'students'    => __('messages.students'),
            'aulas'       => __('messages.classrooms'),
            'noticies'    => __('messages.news'),
            'guardies'    => __('messages.guardies_title'),
            'tickets'     => __('messages.tickets'),
            'roles'       => __('messages.roles'),
            'permissions' => __('messages.permissions'),
        ];

        $actions = [
            'view'       => __('messages.action_view'),
            'create'     => __('messages.action_create'),
            'update'     => __('messages.action_update'),
            'delete'     => __('messages.action_delete'),
            'manage'     => __('messages.action_manage'),
            'import'     => __('messages.action_import'),
            'export'     => __('messages.action_export'),
            'reaccionar' => __('messages.action_react'),
        ];

        if ($module === 'aulas' && $action === 'horari') {
            return __('messages.perm_aulas_horari');
        }

        $moduleName = $modules[$module] ?? ucfirst($module);
        $actionName = $actions[$action] ?? ucfirst($action);

        return __('messages.perm_format', ['action' => $actionName, 'module' => $moduleName]);
    }
}