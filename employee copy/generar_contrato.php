<?php
require_once 'db.php';
require_once __DIR__ . '/../vendor/autoload.php';  // Dompdf para exportar a PDF

use Dompdf\Dompdf;

// Recibir el ID del alquiler desde GET o POST
$IdReserva = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($IdReserva > 0) {
    // Obtener datos del alquiler
    $stmt = $dbh->prepare("
        SELECT 
            b.NumeroReserva, u.NombreCompleto AS Cliente, u.CorreoElectronico, u.Direccion, 
            v.TituloVehiculo, v.PrecioPorDia, b.FechaDesde, b.FechaHasta, b.CostoTotal,
            c.Nombre AS NombreConductor, c.Licencia, c.Telefono AS TelefonoConductor
        FROM tblbooking b
        JOIN tblusers u ON b.CorreoUsuario = u.CorreoElectronico
        JOIN tblvehicles v ON b.IdVehiculo = v.id
        JOIN tblconductores c ON b.IdConductor = c.id
        WHERE b.id = ?
    ");
    $stmt->execute([$IdReserva]);
    $alquiler = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$alquiler) {
        die("No se encontró el registro del alquiler.");
    }

    // Plantilla HTML del contrato/factura
    $html = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .highlight { font-weight: bold; color: #000; }
    </style>
    <div class="header">
        <h2>Contrato y Factura de Alquiler</h2>
        <p>RUC: 12345678910 | Teléfono: 963852147 | Dirección: LIMA, PERÚ</p>
    </div>
    <h3>Datos del Cliente</h3>
    <table>
        <tr><td class="highlight">Nombre:</td><td>' . htmlspecialchars($alquiler['Cliente']) . '</td></tr>
        <tr><td class="highlight">Correo:</td><td>' . htmlspecialchars($alquiler['CorreoElectronico']) . '</td></tr>
        <tr><td class="highlight">Dirección:</td><td>' . htmlspecialchars($alquiler['Direccion']) . '</td></tr>
    </table>

    <h3>Datos del Vehículo</h3>
    <table>
        <tr><td class="highlight">Vehículo:</td><td>' . htmlspecialchars($alquiler['TituloVehiculo']) . '</td></tr>
        <tr><td class="highlight">Precio por Día:</td><td>$' . htmlspecialchars($alquiler['PrecioPorDia']) . '</td></tr>
        <tr><td class="highlight">Fecha de Préstamo:</td><td>' . htmlspecialchars($alquiler['FechaDesde']) . '</td></tr>
        <tr><td class="highlight">Fecha de Devolución:</td><td>' . htmlspecialchars($alquiler['FechaHasta']) . '</td></tr>
        <tr><td class="highlight">Costo Total:</td><td>$' . htmlspecialchars($alquiler['CostoTotal']) . '</td></tr>
    </table>

    <h3>Datos del Conductor</h3>
    <table>
        <tr><td class="highlight">Nombre:</td><td>' . htmlspecialchars($alquiler['NombreConductor']) . '</td></tr>
        <tr><td class="highlight">Licencia:</td><td>' . htmlspecialchars($alquiler['Licencia']) . '</td></tr>
        <tr><td class="highlight">Teléfono:</td><td>' . htmlspecialchars($alquiler['TelefonoConductor']) . '</td></tr>
    </table>

    <div class="footer">
        <p>Pendiente de Pago: <strong>$0.00</strong></p>
        <p>Gracias por su preferencia</p>
    </div>
    ';

    // Generar PDF usando Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Contrato_Alquiler_{$alquiler['NumeroReserva']}.pdf", ["Attachment" => false]);
    exit();
} else {
    echo "ID de reserva no proporcionado.";
}
?>
