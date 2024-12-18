<?php
require_once 'db.php';
require_once __DIR__ . '/../vendor/autoload.php'; // Cargar Dompdf

use Dompdf\Dompdf;

// Recibir el ID de la reserva
$IdReserva = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($IdReserva > 0) {
    // Consultar los datos del alquiler
    $stmt = $dbh->prepare("
        SELECT 
            b.NumeroReserva, u.NombreCompleto AS Cliente, u.CorreoElectronico, u.Direccion, 
            v.TituloVehiculo, v.PrecioPorDia, b.FechaDesde, b.FechaHasta, b.CostoTotal,
            c.Nombre AS NombreConductor
        FROM tblbooking b
        JOIN tblusers u ON b.CorreoUsuario = u.CorreoElectronico
        JOIN tblvehicles v ON b.IdVehiculo = v.id
        JOIN tblconductores c ON b.IdConductor = c.id
        WHERE b.id = ?
    ");
    $stmt->execute([$IdReserva]);
    $factura = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$factura) {
        die("No se encontró el registro del alquiler.");
    }

    // Plantilla HTML de la factura
    $html = '
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        .header, .footer { text-align: center; margin-bottom: 20px; }
        .highlight { font-weight: bold; color: #000; }
    </style>
    <div class="header">
        <h2>Factura de Alquiler</h2>
        <p>RUC: 12345678910 | Teléfono: 963852147 | Dirección: LIMA, PERÚ</p>
    </div>
    <h3>Datos del Cliente</h3>
    <table>
        <tr><td class="highlight">Nombre:</td><td>' . htmlspecialchars($factura['Cliente']) . '</td></tr>
        <tr><td class="highlight">Correo:</td><td>' . htmlspecialchars($factura['CorreoElectronico']) . '</td></tr>
        <tr><td class="highlight">Dirección:</td><td>' . htmlspecialchars($factura['Direccion']) . '</td></tr>
    </table>
    <h3>Detalle de Factura</h3>
    <table>
        <tr><th>Vehículo</th><th>Precio por Día</th><th>Fecha Desde</th><th>Fecha Hasta</th><th>Total</th></tr>
        <tr>
            <td>' . htmlspecialchars($factura['TituloVehiculo']) . '</td>
            <td>$' . htmlspecialchars($factura['PrecioPorDia']) . '</td>
            <td>' . htmlspecialchars($factura['FechaDesde']) . '</td>
            <td>' . htmlspecialchars($factura['FechaHasta']) . '</td>
            <td>$' . htmlspecialchars($factura['CostoTotal']) . '</td>
        </tr>
    </table>
    <div class="footer">
        <p>Total a Pagar: <strong>$' . htmlspecialchars($factura['CostoTotal']) . '</strong></p>
        <p>¡Gracias por su preferencia!</p>
    </div>
    ';

    // Generar PDF usando Dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    $dompdf->stream("Factura_{$factura['NumeroReserva']}.pdf", ["Attachment" => false]);
    exit();
} else {
    echo "ID de reserva no proporcionado.";
}
?>