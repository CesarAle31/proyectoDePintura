<?php
/**
 * ============================================================
 *  AuthModel.php — Acceso a datos para autenticación
 * ============================================================
 *  Consulta la tabla usuario uniéndola con empleado y rol.
 *  No verifica contraseñas — eso lo hace AuthController con
 *  password_verify(). Aquí solo recuperamos el registro.
 * ============================================================
 */

class AuthModel extends Model
{
    /**
     * Busca un usuario ACTIVO por nombre de usuario y devuelve
     * sus datos junto con los del empleado y el rol asociado.
     */
    public function buscarPorUsuario(string $usuario): ?array
    {
        $sql = "
            SELECT
                u.idUsuario,
                u.idEmpleado,
                u.idRol,
                u.usuario,
                u.password_hash,
                u.activo,
                e.nombre      AS empNombre,
                e.apellidoP   AS empApellidoP,
                e.apellidoM   AS empApellidoM,
                r.nombre      AS rolNombre,
                r.descripcion AS rolDescripcion
            FROM usuario u
            INNER JOIN empleado e ON e.idEmpleado = u.idEmpleado
            INNER JOIN rol      r ON r.idRol      = u.idRol
            WHERE u.usuario = :usuario
              AND u.activo  = 1
            LIMIT 1
        ";

        return $this->queryOne($sql, [':usuario' => $usuario]);
    }
}