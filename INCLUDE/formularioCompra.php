<?php
session_start();

// Verificar si el usuario ha iniciado sesión y obtener su ID
$id_usuario = isset($_SESSION['ID_Usuario']) ? $_SESSION['ID_Usuario'] : null;

if ($id_usuario) {
    // Conexión a la base de datos
    $conexion = new mysqli('localhost', 'root', '', 'tienda de ropa');

    // Verificar la conexión
    if ($conexion->connect_error) {
        die('Error de conexión: ' . $conexion->connect_error);
    }

    // Consulta para obtener los datos del usuario
    $sql = "SELECT Nombre, Apellido, Correo, N_Documento, Direccion, Ciudad, Telefono 
            FROM usuario WHERE ID_Usuario = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param('i', $id_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
    } else {
        die('No se encontraron datos para el usuario.');
    }

    $stmt->close();
    $conexion->close();
} else {
    die('Usuario no autenticado.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de Compra</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f2f8f9;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 70%;
            margin: 30px auto;
            background: #fbe9e7;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border: 2px solid #d3b3b3;
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 28px;
            color: #c17f6d;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 4px;
            font-weight: bold;
            font-size: 14px;
            color: #000; /* Cambiado a negro */
        }

        input[type="text"],
        input[type="email"],
        input[type="file"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #b7b7a4;
            border-radius: 6px;
            font-size: 14px;
            background-color: #fff8f0;
            transition: all 0.3s ease;
            color: #333;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="file"]:focus,
        select:focus {
            border-color: #c17f6d;
            outline: none;
            background-color: #ffffff;
        }

        .producto-detalle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #e9cfc1;
            margin-bottom: 10px;
            overflow: hidden;
        }

        .producto-detalle img {
            max-width: 80px; /* Tamaño aumentado */
            max-height: 80px;
            border-radius: 5px;
            object-fit: cover;
        }

        .producto-detalle div {
            flex-grow: 1;
            margin-left: 10px;
        }

        .producto-detalle p {
            margin: 4px 0;
            font-size: 16px; /* Letra agrandada */
        }

        .productos-container {
            margin-top: 20px;
            padding: 10px;
            background-color: #fff8f0;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .productos-container h3 {
            font-size: 20px; /* Letra agrandada */
            color: #c17f6d;
            margin-bottom: 15px;
        }

        .total-confirmar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .total-container {
            width: 45%;
        }

        .confirmar-container {
            width: 45%;
        }

        input[type="submit"] {
            padding: 12px;
            background-color: #a8d8a2;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        input[type="submit"]:hover {
            background-color: #88b68d;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
<div class="form-container">
    <form id="formCompra" method="POST" action="procesar_compra.php" onsubmit="procesarFormulario(event)">
        <h2>Detalles de Compra</h2>
        <div class="form-grid">
            <div>
                <label for="id_usuario">ID Usuario:</label>
                <input type="text" id="id_usuario" name="id_usuario" value="<?php echo htmlspecialchars($id_usuario); ?>" readonly>
            </div>
            <div>
                <label for="nombre_cliente">Nombre:</label>
                <input type="text" id="nombre_cliente" name="nombre_cliente" value="<?php echo htmlspecialchars($usuario['Nombre'] . ' ' . $usuario['Apellido']); ?>" readonly>
            </div>
            <div>
                <label for="email_cliente">Email:</label>
                <input type="email" id="email_cliente" name="email_cliente" value="<?php echo htmlspecialchars($usuario['Correo']); ?>" readonly>
            </div>
            <div>
                <label for="cedula_cliente">Cédula:</label>
                <input type="text" id="cedula_cliente" name="cedula_cliente" value="<?php echo htmlspecialchars($usuario['N_Documento']); ?>" readonly>
            </div>
            <div>
                <label for="direccion_entrega">Dirección de Entrega:</label>
                <input type="text" id="direccion_entrega" name="direccion_entrega" value="<?php echo htmlspecialchars($usuario['Direccion']); ?>" readonly>
            </div>
            <div>
                <label for="id_tipo_pago">Tipo de Pago:</label>
                <select id="id_tipo_pago" name="id_tipo_pago">
                    <option value="1">Transferencia</option>
                    <option value="2">Tarjeta de Crédito</option>
                </select>
            </div>
            <div>
                <label for="lugar_envio">Lugar de Envío:</label>
                <input type="text" id="lugar_envio" name="lugar_envio" value="<?php echo htmlspecialchars($usuario['Ciudad']); ?>" readonly>
            </div>
            <div>
                <label for="numero_telefono">Número de Teléfono:</label>
                <input type="text" id="numero_telefono" name="numero_telefono" value="<?php echo htmlspecialchars($usuario['Telefono']); ?>" readonly>
            </div>
        </div>
        <div class="productos-container">
            <h3>Detalles de los Productos:</h3>
            <div id="detallesProductos"></div>
        </div>
        <div class="total-confirmar">
            <div class="total-container">
                <label for="totalCompra">Total:</label>
                <input type="text" id="totalCompra" name="total" value="0.00" readonly>
            </div>
            <div class="confirmar-container">
                <input type="submit" value="Confirmar Compra">
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const productos = JSON.parse(localStorage.getItem('formularioCompra')) || [];
        let total = 0;
        const detallesProductos = document.getElementById('detallesProductos');
        if (productos.length > 0) {
            productos.forEach((item, index) => {
                const producto = document.createElement('div');
                producto.className = 'producto-detalle';
                producto.innerHTML = `
                    <div>
                        <p><strong>Producto:</strong> ${item.nombre}</p>
                        <p><strong>Precio:</strong> $${item.precio.toFixed(2)}</p>
                        <p><strong>Cantidad:</strong> ${item.cantidad}</p>
                        <p><strong>Total:</strong> $${(item.precio * item.cantidad).toFixed(2)}</p>
                        <p><strong>ID de la Prenda:</strong> ${item.idPrenda}</p>
                        <p><strong>Talla:</strong> ${item.talla}</p>
                        <p><strong>Stock Disponible:</strong> ${item.stock}</p>
                    </div>
                    <img src="${item.imagen}" alt="${item.nombre}">
                    <input type="hidden" name="productos[${index}][nombre]" value="${item.nombre}">
                    <input type="hidden" name="productos[${index}][precio]" value="${item.precio}">
                    <input type="hidden" name="productos[${index}][cantidad]" value="${item.cantidad}">
                    <input type="hidden" name="productos[${index}][total]" value="${(item.precio * item.cantidad).toFixed(2)}">
                    <input type="hidden" name="productos[${index}][id_prenda]" value="${item.idPrenda}">
                    <input type="hidden" name="productos[${index}][talla]" value="${item.talla}">
                    <input type="hidden" name="productos[${index}][stock]" value="${item.stock}">
                `;
                detallesProductos.appendChild(producto);
                total += parseFloat(item.precio) * item.cantidad;
            });
        } else {
            detallesProductos.innerHTML = '<p>No hay productos en el carrito.</p>';
        }
        document.getElementById('totalCompra').value = total.toFixed(2);
    });

    async function procesarFormulario(event) {
        event.preventDefault();
        const form = document.getElementById("formCompra");
        const formData = new FormData(form);
        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
            });
            const result = await response.text();
            if (result.includes("Compra realizada con éxito")) {
                alert("¡Compra realizada con éxito!");
                window.location.href = "../ROPA_CLIENTE_HOMBRE/CAMISETAS_H.PHP";
            } else {
                alert("Hubo un error en la compra: " + result);
            }
        } catch (error) {
            alert("Error en la comunicación con el servidor.");
        }
    }
</script>
