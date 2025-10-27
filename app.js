document.addEventListener('DOMContentLoaded', () => {
    
    // Selectores del DOM
    const trackerForm = document.getElementById('trackerForm');
    const historyBody = document.getElementById('historyBody');
    const ctx = document.getElementById('imcChart').getContext('2d');

    // Clave para LocalStorage
    const STORAGE_KEY = 'statTrackerHistory';

    // Variable global para el gráfico
    let imcChart;

    /**
     * Carga y obtiene los registros desde LocalStorage.
     * @returns {Array} Un array de registros, o un array vacío si no hay nada.
     */
    function getHistory() {
        const history = localStorage.getItem(STORAGE_KEY);
        // Si existe historial, lo parsea (convierte de JSON string a Array). Si no, devuelve array vacío.
        return history ? JSON.parse(history) : [];
    }

    /**
     * Guarda el historial completo en LocalStorage.
     * @param {Array} history El array de registros a guardar.
     */
    function saveHistory(history) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(history));
    }

    /**
     * Calcula el Índice de Masa Corporal (IMC).
     * @param {number} peso - Peso en KG.
     * @param {number} estatura - Estatura en Metros.
     * @returns {string} El IMC calculado, redondeado a 2 decimales.
     */
    function calculateIMC(peso, estatura) {
        if (estatura <= 0) return 'N/A';
        const imc = peso / (estatura * estatura);
        return imc.toFixed(2); // Redondear a 2 decimales
    }

    /**
     * Renderiza (dibuja) la tabla del historial en el HTML.
     */
    function renderTable() {
        const history = getHistory();
        
        // Ordenar el historial por fecha (de más reciente a más antiguo, como un cronograma)
        // El README pide ordenado cronológicamente, usaremos ascendente (de más antiguo a más reciente).
        history.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

        // Limpiar el cuerpo de la tabla antes de volver a dibujarla
        historyBody.innerHTML = '';

        // Crear una fila (tr) por cada registro
        history.forEach(record => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${record.fecha}</td>
                <td>${record.peso}</td>
                <td>${record.estatura}</td>
                <td>${record.imc}</td>
            `;
            historyBody.appendChild(row);
        });
    }

    /**
     * Renderiza (dibuja) el gráfico de líneas del IMC.
     */
    function renderChart() {
        const history = getHistory();
        
        // Ordenar por fecha para que el gráfico de línea tenga sentido
        history.sort((a, b) => new Date(a.fecha) - new Date(b.fecha));

        // Extraer los datos para el gráfico
        const labels = history.map(record => record.fecha); // Eje X: Fechas
        const imcData = history.map(record => record.imc);   // Eje Y: IMC

        // Si el gráfico ya existe (p.ej. al añadir un nuevo dato), lo destruimos antes de crear uno nuevo
        if (imcChart) {
            imcChart.destroy();
        }

        // Crear la instancia de Chart.js
        imcChart = new Chart(ctx, {
            type: 'line', // Tipo de gráfico
            data: {
                labels: labels,
                datasets: [{
                    label: 'Índice de Masa Corporal (IMC)',
                    data: imcData,
                    borderColor: 'rgba(0, 123, 255, 1)',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    fill: true,
                    tension: 0.1 // Línea ligeramente curvada
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false,
                        title: { display: true, text: 'IMC' }
                    },
                    x: {
                        title: { display: true, text: 'Fecha' }
                    }
                }
            }
        });
    }

    /**
     * Manejador del evento 'submit' del formulario.
     */
    function handleSubmit(event) {
        event.preventDefault(); // Evitar que la página se recargue

        // 1. Obtener valores del formulario
        const nombre = document.getElementById('nombre').value;
        const apellido = document.getElementById('apellido').value;
        const peso = parseFloat(document.getElementById('peso').value);
        const estatura = parseFloat(document.getElementById('estatura').value);
        const fecha = document.getElementById('fecha').value;
        
        // Validación simple (el HTML 'required' ya hace la mayoría)
        if (isNaN(peso) || isNaN(estatura) || peso <= 0 || estatura <= 0) {
            alert("Por favor, introduce un peso y estatura válidos.");
            return;
        }

        // 2. Calcular IMC
        const imc = calculateIMC(peso, estatura);

        // 3. Crear el nuevo registro
        const newRecord = {
            nombre,
            apellido,
            peso,
            estatura,
            fecha,
            imc
        };

        // 4. Guardar en LocalStorage
        const history = getHistory(); // Obtener historial actual
        history.push(newRecord);      // Añadir el nuevo registro
        saveHistory(history);         // Guardar el historial actualizado

        // 5. Actualizar la interfaz
        renderTable();  // Actualizar la tabla
        renderChart();  // Actualizar el gráfico

        // 6. Limpiar el formulario
        trackerForm.reset();
        
        // Opcional: Guardar el nombre y apellido para la próxima vez
        localStorage.setItem('statTrackerUser', JSON.stringify({ nombre, apellido }));
    }
    
    /**
     * Función de inicialización
     */
    function init() {
        // Rellenar nombre y apellido si ya se guardaron
        const user = localStorage.getItem('statTrackerUser');
        if(user) {
            const { nombre, apellido } = JSON.parse(user);
            document.getElementById('nombre').value = nombre;
            document.getElementById('apellido').value = apellido;
        }

        // Cargar datos al iniciar la página
        renderTable();
        renderChart();

        // Asignar el evento al formulario
        trackerForm.addEventListener('submit', handleSubmit);
    }

    // Iniciar la aplicación
    init();

});