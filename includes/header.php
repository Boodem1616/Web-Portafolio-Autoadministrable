<?php
require_once __DIR__ . '/../config/database.php';

// Obtener datos de biografía
$conn = getConnection();
$stmt = $conn->prepare("SELECT * FROM biografia ORDER BY id DESC LIMIT 1");
$stmt->execute();
$biografia = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portafolio - <?php echo isset($biografia['nombre_completo']) ? sanitize($biografia['nombre_completo']) : 'Mi Portafolio'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body>