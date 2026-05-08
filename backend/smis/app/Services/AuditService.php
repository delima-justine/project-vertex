<?php

namespace App\Services;

use App\Models\AdminAudit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Log an admin action.
     *
     * @param string $actionType e.g., 'CREATE', 'UPDATE', 'DELETE'
     * @param mixed $target The model instance or target description
     * @param string|null $description Custom description
     * @param array|null $oldValues
     * @param array|null $newValues
     * @param \App\Models\User|null $performer
     * @return AdminAudit
     */
    public static function log($actionType, $target = null, $description = null, $oldValues = null, $newValues = null, $performer = null)
    {
        $user = $performer ?: Auth::user();
        
        $data = [
            'admin_id' => $user ? $user->id : null,
            'admin_name' => $user ? ($user->first_name . ' ' . $user->last_name) : 'System',
            'admin_role' => $user && $user->role ? $user->role->name : null,
            'action_type' => $actionType,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'performed_at' => now(),
        ];

        if ($target instanceof \Illuminate\Database\Eloquent\Model) {
            $data['target_id'] = $target->getKey();
            $data['target_type'] = get_class($target);
            $data['target_name'] = self::getTargetName($target);
        } elseif (is_array($target)) {
            $data['target_id'] = $target['id'] ?? null;
            $data['target_type'] = $target['type'] ?? null;
            $data['target_name'] = $target['name'] ?? null;
        }

        return AdminAudit::create($data);
    }

    /**
     * Try to get a descriptive name for the target model.
     */
    private static function getTargetName($model)
    {
        if (isset($model->item_desc)) return $model->item_desc;
        if (isset($model->category_name)) return $model->category_name;
        if (isset($model->unit_name)) return $model->unit_name;
        if (isset($model->office_name)) return $model->office_name;
        if (isset($model->name)) return $model->name;
        if (isset($model->email)) return $model->email;
        if (isset($model->title)) return $model->title;
        if (isset($model->first_name)) return $model->first_name . ' ' . ($model->last_name ?? '');
        
        return null;
    }
}
