<?php
// CONFIGURACIÓN
$categorias_permitidas = ["Electrónica", "Ropa", "Alimentos", "Hogar"];
$errores = [];
$email_enviado = false;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function limpiar_datos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = isset($_POST["email"]) ? limpiar_datos($_POST["email"]) : '';
    $nombre = isset($_POST["nombre"]) ? limpiar_datos($_POST["nombre"]) : '';
    $precio = isset($_POST["precio"]) ? limpiar_datos($_POST["precio"]) : '';
    $cantidad = isset($_POST["cantidad"]) ? limpiar_datos($_POST["cantidad"]) : '';
    $categoria = isset($_POST["categoria"]) ? limpiar_datos($_POST["categoria"]) : '';

    // Validaciones
    if (empty($email)) {
        $errores["email"] = "El correo electrónico es obligatorio.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores["email"] = "El formato del correo no es válido.";
    }
    
    if (empty($nombre)) { 
        $errores["nombre"] = "El nombre del producto es obligatorio."; 
    }
    
    if (empty($precio)) { 
        $errores["precio"] = "El precio es obligatorio."; 
    } elseif (!is_numeric($precio) || $precio <= 0) {
        $errores["precio"] = "El precio debe ser un número mayor a cero.";
    }

    if (empty($cantidad)) { 
        $errores["cantidad"] = "La cantidad es obligatoria."; 
    } elseif (!filter_var($cantidad, FILTER_VALIDATE_INT) || $cantidad <= 0) {
        $errores["cantidad"] = "La cantidad debe ser un número entero positivo.";
    }

    if (empty($categoria)) { 
        $errores["categoria"] = "La categoría es obligatoria."; 
    } elseif (!in_array($categoria, $categorias_permitidas)) {
        $errores["categoria"] = "Categoría no válida.";
    }

    if (empty($errores)) {
        // ENVÍO CON PHPMailer Y GMAIL
        $mail = new PHPMailer(true);
        
        try {
            // Configuración SMTP GMAIL
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'lin.cry88@gmail.com';
            $mail->Password = 'ojje dqvb uehm axzw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Configuración de codificación
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            
            // Destinatarios
            $mail->setFrom('lin.cry88@gmail.com', 'Sistema de Registro');
            $mail->addAddress($email);
            $mail->addReplyTo('lin.cry88@gmail.com', 'Soporte');
            
            // Contenido
            $mail->isHTML(true);
            $mail->Subject = "=?UTF-8?B?" . base64_encode("✅ Confirmación de Registro - " . $nombre) . "?=";
            
            $mail->Body = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0; }
                    .container { max-width: 600px; margin: 40px auto; background: #2d2d2d; border-radius: 12px; overflow: hidden; }
                    .header { background: linear-gradient(135deg, #064e3b 0%, #10b981 100%); color: white; padding: 40px 30px; text-align: center; }
                    .header h1 { margin: 0; font-size: 24px; }
                    .content { padding: 30px; color: #d1d5db; }
                    .content p { font-size: 15px; line-height: 1.6; margin-bottom: 20px; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #1f2937; border-radius: 8px; overflow: hidden; }
                    th { background: #374151; color: #f3f4f6; padding: 12px; text-align: left; font-size: 12px; text-transform: uppercase; }
                    td { padding: 12px; color: #d1d5db; border-top: 1px solid #374151; }
                    td:first-child { font-weight: 600; color: #9ca3af; }
                    td:last-child { color: #10b981; font-weight: 700; }
                    .footer { background: #1f2937; padding: 20px; text-align: center; color: #9ca3af; font-size: 13px; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h1>✅ Producto Registrado Exitosamente</h1>
                    </div>
                    <div class="content">
                        <p>Estimado usuario, se ha completado el registro del siguiente producto:</p>
                        
                        <table>
                            <thead>
                                <tr>
                                    <th>Campo</th>
                                    <th>Valor Registrado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Nombre del Producto:</td>
                                    <td>' . htmlspecialchars($nombre) . '</td>
                                </tr>
                                <tr>
                                    <td>Precio:</td>
                                    <td>$' . number_format((float)$precio, 2) . '</td>
                                </tr>
                                <tr>
                                    <td>Cantidad (Stock):</td>
                                    <td>' . htmlspecialchars($cantidad) . ' unidades</td>
                                </tr>
                                <tr>
                                    <td>Categoría:</td>
                                    <td>' . htmlspecialchars($categoria) . '</td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <p style="margin-top: 24px; font-size: 13px; color: #9ca3af;">
                            Fecha de registro: ' . date('d/m/Y H:i:s') . '
                        </p>
                    </div>
                    <div class="footer">
                        <p>Gracias por usar nuestro sistema de registro.</p>
                    </div>
                </div>
            </body>
            </html>
            ';
            
            $mail->send();
            $email_enviado = true;
            
        } catch (Exception $e) {
            $errores["email"] = "Error al enviar correo: " . $mail->ErrorInfo;
        }
        
        // MOSTRAR RESULTADO
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Detalles del Producto Registrado</title>
            <link rel="stylesheet" href="estilos.css">
        </head>
        <body class="resultado">
        <div class="container">
            <h1>Detalles del Producto Registrado</h1>
            
            <div class="mensaje">
                <p>Estimado usuario, se ha completado el registro del siguiente producto:</p>
            </div>
            
            <?php if ($email_enviado): ?>
            <div class="email-info">
                <p>📧 Correo de confirmación ENVIADO a:</p>
                <strong><?php echo htmlspecialchars($email); ?></strong>
                <p style="margin-top: 10px; font-size: 14px;">✅ El correo llegará en unos segundos</p>
            </div>
            <?php else: ?>
            <div class="error-container">
                <p>⚠️ No se pudo enviar el correo.</p>
                <p>Error: <?php echo $errores["email"] ?? 'Desconocido'; ?></p>
            </div>
            <?php endif; ?>
            
            <table>
                <thead>
                    <tr>
                        <th>Campo</th>
                        <th>Valor Registrado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Nombre del Producto:</td>
                        <td><?php echo htmlspecialchars($nombre); ?></td>
                    </tr>
                    <tr>
                        <td>Precio:</td>
                        <td>$<?php echo number_format((float)$precio, 2); ?></td>
                    </tr>
                    <tr>
                        <td>Cantidad (Stock):</td>
                        <td><?php echo htmlspecialchars($cantidad); ?> unidades</td>
                    </tr>
                    <tr>
                        <td>Categoría:</td>
                        <td><?php echo htmlspecialchars($categoria); ?></td>
                    </tr>
                </tbody>
            </table>
            
            <p class="footer-text">Gracias por usar nuestro sistema de registro.</p>
            
            <a href="index.html" class="btn-volver">➕ Registrar Otro Producto</a>
        </div>
        </body>
        </html>
        <?php

    } else {
        // MOSTRAR ERRORES
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Error en el Formulario</title>
            <link rel="stylesheet" href="estilos.css">
        </head>
        <body class="resultado">
        <div class="container">
            <h1>Error en el Formulario</h1>
            
            <div class="error-container">
                <h2>Se encontraron los siguientes problemas:</h2>
                <ul>
                    <?php foreach ($errores as $campo => $mensaje): ?>
                    <li><strong><?php echo ucfirst($campo); ?>:</strong> <?php echo $mensaje; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <a href="index.html" class="btn-volver">← Volver al Formulario</a>
        </div>
        </body>
        </html>
        <?php
    }

} else {
    header('Location: index.html');
    exit();
}
?>