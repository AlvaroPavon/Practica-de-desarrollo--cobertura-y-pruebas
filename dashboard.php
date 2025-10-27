<?php
session_start();

// Proteger la página: Si el usuario no está logueado, redirigir al index
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Saludo al usuario
$nombreUsuario = htmlspecialchars($_SESSION['user_nombre']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - StatTracker</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 10px 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header h1 { margin: 0; }
        .header a { text-decoration: none; background: #dc3545; color: white; padding: 8px 12px; border-radius: 4px; }
        .content { display: flex; flex-wrap: wrap; gap: 30px; margin-top: 20px; }
        .form-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 350px; box-sizing: border-box;}
        .chart-container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); flex-grow: 1; min-width: 300px;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input { width: 100%; padding: 8px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        .btn:hover { background-color: #218838; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Bienvenido, <?php echo $nombreUsuario; ?></h1>
        <a href="logout.php">Cerrar Sesión</a>
    </div>

    <div class="content">
        <div class="form-container">
            <h2>Registrar Nuevo Peso</h2>
            <?php if (isset($_GET['error'])): ?>
                <p style="color:red;"><?php echo htmlspecialchars($_GET['error']); ?></p>
            <?php endif; ?>
            <form action="add_data.php" method="POST">
                <div class="form-group">
                    <label for="peso">Peso (en KG):</label>
                    <input type="number" step="0.1" id="peso" name="peso" placeholder="Ej: 70.5" required>
                </div>
                <div class="form-group">
                    <label for="altura">Altura (en Metros):</label>
                    <input type="number" step="0.01" id="altura" name="altura" placeholder="Ej: 1.75" required>
                </div>
                <div class="form-group">
                    <label for="fecha">Fecha del Registro:</label>
                    <input type="date" id="fecha" name="fecha_registro" required>
                </div>
                <button type="submit" class="btn">Guardar Registro</button>
            </form>
        </div>

        <div class="chart-container">
            <h2>Evolución de tu IMC</h2>
            <canvas id="imcChart"></canvas>
        </div>
    </div>

    <script>
        // Lógica para cargar y mostrar el gráfico
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Usar fetch (AJAX) para pedir los datos al backend (get_data.php)
            fetch('get_data.php')
                .then(response => response.json())
                .then(data => {
                    // 2. Procesar los datos recibidos
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Ordenar por fecha (aunque ya deberían venir ordenados del SQL)
                    data.sort((a, b) => new Date(a.fecha_registro) - new Date(b.fecha_registro));
                    
                    const labels = data.map(item => item.fecha_registro); // Eje X: Fechas
                    const imcData = data.map(item => item.imc); // Eje Y: IMC

                    // 3. Renderizar el gráfico
                    const ctx = document.getElementById('imcChart').getContext('2d');
                    const imcChart = new Chart(ctx, {
                        type: 'line', // Tipo de gráfico (línea)
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Índice de Masa Corporal (IMC)',
                                data: imcData,
                                borderColor: 'rgba(0, 123, 255, 1)',
                                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                                fill: true,
                                tension: 0.1
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: false,
                                    title: { display: true, text: 'IMC' }
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error('Error al cargar los datos del gráfico:', error);
                    // Opcional: mostrar un error en el contenedor del gráfico
                    document.querySelector('.chart-container').innerHTML = '<h2>Error al cargar el gráfico</h2><p>No se pudieron obtener los datos.</p>';
                });
        });
    </script>

</body>
</html>