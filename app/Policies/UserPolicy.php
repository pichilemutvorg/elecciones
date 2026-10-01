<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Este policy no gobierna el acceso al panel: esa comprobacion la hace
     * User::canAccessPanel(), que es el contrato que consulta Filament. Aqui se
     * resuelven las acciones sobre las cuentas.
     */

    /**
     * Solo un administrador puede ver la lista de cuentas.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Solo un administrador puede crear cuentas.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Un usuario puede ver su propia cuenta. Un administrador, la de cualquiera.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    /**
     * Un usuario puede editar su propia cuenta, de forma limitada: nombre,
     * correo y contrasena. Cambiar is_admin queda reservado a un administrador,
     * para que una cuenta no pueda escalate a si misma.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->is($model);
    }

    /**
     * El cambio del flag de administrador requiere el nivel de administrador.
     */
    public function updateAdminFlag(User $user, User $model): bool
    {
        return $user->isAdmin();
    }

    /**
     * Nadie se borra a si mismo desde el panel.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->isAdmin() && ! $user->is($model);
    }
}
