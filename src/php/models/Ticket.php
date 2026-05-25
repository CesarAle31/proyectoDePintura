<?php
/**
 * ============================================================
 *  Ticket.php — Modelo de Tickets (detalle de venta)
 * ============================================================
 */

class Ticket extends Model
{
    /**
     * Obtiene el detalle de un ticket por folio.
     */
    public function getByFolio(int $folio): array
    {
        return $this->query("
            SELECT t.idTicket, t.cantidad, t.precio, t.importe,
                   p.nombre AS pintura, p.color, p.presentacion, p.capacidad
            FROM ticket t
            INNER JOIN pintura p ON t.idPintura = p.idPintura
            WHERE t.folio = :folio
            ORDER BY t.idTicket
        ", ['folio' => $folio]);
    }

    /**
     * Obtiene la auditoría de stock.
     */
    public function getAuditoriaStock(): array
    {
        return $this->query("
            SELECT * FROM auditoria_stock_pintura
            ORDER BY fecha_cambio DESC
            LIMIT 50
        ");
    }

    /**
     * Obtiene la auditoría de costos.
     */
    public function getAuditoriaCosto(): array
    {
        return $this->query("
            SELECT * FROM auditoria_costo_pintura
            ORDER BY fecha_cambio DESC
            LIMIT 50
        ");
    }

    /**
     * Obtiene la auditoría de clientes insertados.
     */
    public function getAuditoriaClienteInsert(): array
    {
        return $this->query("
            SELECT * FROM auditoria_cliente_insert
            ORDER BY fecha_registro DESC
            LIMIT 50
        ");
    }

    /**
     * Obtiene la auditoría de clientes eliminados.
     */
    public function getAuditoriaClienteDelete(): array
    {
        return $this->query("
            SELECT * FROM auditoria_cliente_delete
            ORDER BY fecha_eliminacion DESC
            LIMIT 50
        ");
    }
}
