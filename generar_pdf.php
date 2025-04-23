<?php
ob_start(); // Inicia el buffer de salida

require('assets/libs/fpdf/fpdf.php');
require_once 'config.php';

// Habilitar reporte de errores en el servidor
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->SetTextColor(33, 37, 41);
    $pdf->Cell(0, 10, 'Reporte de Alumnos, Vehiculos y Registros', 0, 1, 'C');
    $pdf->Ln(8);

    function addTableHeader($pdf, $headers, $widths) {
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        foreach ($headers as $index => $header) {
            $pdf->Cell($widths[$index], 6, utf8_decode($header), 1, 0, 'C', true);
        }
        $pdf->Ln();
    }

    function addTableRow($pdf, $row, $widths) {
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetTextColor(33, 37, 41);
        $pdf->SetFillColor(245, 245, 245);
        foreach ($row as $index => $cell) {
            $pdf->Cell($widths[$index], 6, utf8_decode($cell), 1, 0, 'C', true);
        }
        $pdf->Ln();
    }

    $stmt = $pdo->prepare("SELECT * FROM Usuarios");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Usuarios Registrados', 0, 1, 'L');
    $pdf->Ln(4);

    $userHeaders = ['ID', 'Nombre', 'Correo', 'Teléfono'];
    $userWidths = [15, 50, 65, 30];  // Reduced total width to 160
    addTableHeader($pdf, $userHeaders, $userWidths);
    foreach ($usuarios as $usuario) {
        addTableRow($pdf, [
            $usuario['ID'],
            $usuario['NombreCompleto'],
            $usuario['Correo'],
            $usuario['Telefono']
        ], $userWidths);
    }
    $pdf->Ln(6);

    $stmt = $pdo->prepare("SELECT * FROM Vehiculo");
    $stmt->execute();
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Vehículos Registrados', 0, 1, 'L');
    $pdf->Ln(4);

    $vehHeaders = ['ID', 'Placa', 'Marca', 'Modelo'];
    $vehWidths = [15, 35, 60, 50];  // Reduced total width to 160
    addTableHeader($pdf, $vehHeaders, $vehWidths);
    foreach ($vehiculos as $vehiculo) {
        addTableRow($pdf, [
            $vehiculo['IDVehiculo'],
            $vehiculo['Placa'],
            $vehiculo['Marca'],
            $vehiculo['Modelo']
        ], $vehWidths);
    }
    $pdf->Ln(6);

    $stmt = $pdo->prepare("SELECT r.IDRegistro, u.NombreCompleto, v.Placa, v.Marca, v.Modelo, r.Entrada, r.Salida 
                           FROM Registro r
                           JOIN Usuarios u ON r.UsuarioID = u.ID
                           JOIN Vehiculo v ON r.PlacaVehiculo = v.Placa");
    $stmt->execute();
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 8, 'Registros de Entradas y Salidas', 0, 1, 'L');
    $pdf->Ln(4);

    $regHeaders = ['ID', 'Usuario', 'Vehículo', 'Placa', 'Entrada', 'Salida'];
    $regWidths = [15, 45, 45, 25, 25, 25];  // Reduced total width to 180
    addTableHeader($pdf, $regHeaders, $regWidths);
    foreach ($registros as $registro) {
        $salida = $registro['Salida'] ? $registro['Salida'] : 'En curso';
        addTableRow($pdf, [
            $registro['IDRegistro'],
            $registro['NombreCompleto'],
            "{$registro['Marca']} {$registro['Modelo']}",
            $registro['Placa'],
            $registro['Entrada'],
            $salida
        ], $regWidths);
    }

    ob_end_clean();
    $pdf->Output('D', 'reporte_admin.pdf');
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>