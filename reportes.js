

const equipos   = [{ disponible: true }, { disponible: false }, { disponible: true }];
const tickets   = [{ estado: "Abierto" }, { estado: "Cerrado" }, { estado: "Abierto" }];
const prestamos = [{ devuelto: false }, { devuelto: true }];

const botonGenerarReporte = document.getElementById("botonGenerarReporte");

botonGenerarReporte.addEventListener("click", function() {
    const mensaje = document.getElementById("mensajeReporte");

    const equiposDisponibles = 0;
    const equiposPrestados = 0;
    for (const i = 0; i < equipos.length; i++) {
        if (equipos[i].disponible) {
            equiposDisponibles++;
        } else {
            equiposPrestados++;
        }
    }

    const ticketsAbiertos = 0;
    const ticketsCerrados = 0;
    for (const i = 0; i < tickets.length; i++) {
        if (tickets[i].estado == "Abierto") {
            ticketsAbiertos++;
        } else {
            ticketsCerrados++;
        }
    }

    const prestamosActivos = 0;
    const prestamosDevueltos = 0;
    for (const i = 0; i < prestamos.length; i++) {
        if (!prestamos[i].devuelto) {
            prestamosActivos++;
        } else {
            prestamosDevueltos++;
        }
    }

    const tablaVieja = document.getElementById("tablaReporte");
    if (tablaVieja) {
        tablaVieja.remove();
    }

    const tabla = document.createElement("table");
    tabla.id = "tablaReporte";

    const encabezado = tabla.insertRow();
    encabezado.innerHTML = "<th>Concepto</th><th>Valor</th>";

    const datos = [
        ["Total de equipos",    equipos.length],
        ["Equipos disponibles", equiposDisponibles],
        ["Equipos prestados",   equiposPrestados],
        ["Tickets abiertos",    ticketsAbiertos],
        ["Tickets cerrados",    ticketsCerrados],
        ["Préstamos activos",   prestamosActivos],
        ["Préstamos devueltos", prestamosDevueltos]
    ];

    for (const i = 0; i < datos.length; i++) {
        const fila = tabla.insertRow();
        fila.insertCell().textContent = datos[i][0];
        fila.insertCell().textContent = datos[i][1];
    }

    document.getElementById("reportes").appendChild(tabla);

    mensaje.textContent = "Reporte generado.";
});
