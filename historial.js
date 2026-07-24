// =============================================
// MÓDULO: Historial
// =============================================

// Datos de muestra pertinentes al sistema
const ticketsHistorial = [
    { fecha: "10/06/2026", solicitante: "Prof. García",  problema: "La PC no enciende",          prioridad: "Alta",  estado: "Cerrado" },
    { fecha: "11/06/2026", solicitante: "Tec. López",    problema: "Sin conexión a internet",    prioridad: "Media", estado: "Cerrado" },
    { fecha: "12/06/2026", solicitante: "Admin. Sánchez",problema: "Proyector sin señal HDMI",   prioridad: "Alta",  estado: "Abierto" },
    { fecha: "14/06/2026", solicitante: "Prof. Martínez",problema: "TV sala sin imagen",          prioridad: "Media", estado: "Abierto" }
];

const reportesHistorial = [
    { fecha: "01/06/2026", equipos: 4, ticketsAbiertos: 2, ticketsCerrados: 1, prestamos: 1 },
    { fecha: "07/06/2026", equipos: 4, ticketsAbiertos: 1, ticketsCerrados: 3, prestamos: 2 },
    { fecha: "14/06/2026", equipos: 4, ticketsAbiertos: 2, ticketsCerrados: 2, prestamos: 1 }
];

const contenedor = document.getElementById("contenedorHistorial");

document.getElementById("botonMostrarTickets").addEventListener("click", function () {
    mostrarTickets();
});

document.getElementById("botonMostrarReportes").addEventListener("click", function () {
    mostrarReportes();
});

function mostrarTickets() {
    let html = "<table><thead><tr><th>#</th><th>Fecha</th><th>Solicitante</th><th>Prioridad</th><th>Problema</th><th>Estado</th></tr></thead><tbody>";
    for (let i = 0; i < ticketsHistorial.length; i++) {
        const t = ticketsHistorial[i];
        const prioClase   = t.prioridad === "Alta" ? "badge-danger" : t.prioridad === "Media" ? "badge-warning" : "badge-info";
        const estadoClase = t.estado === "Abierto" ? "badge-warning" : "badge-success";
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + t.fecha + "</td>";
        html += "<td>" + t.solicitante + "</td>";
        html += "<td><span class='badge " + prioClase   + "'>" + t.prioridad + "</span></td>";
        html += "<td>" + t.problema + "</td>";
        html += "<td><span class='badge " + estadoClase + "'>" + t.estado    + "</span></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function mostrarReportes() {
    let html = "<table><thead><tr><th>#</th><th>Fecha</th><th>Equipos</th><th>Tickets abiertos</th><th>Tickets cerrados</th><th>Préstamos</th></tr></thead><tbody>";
    for (let i = 0; i < reportesHistorial.length; i++) {
        const r = reportesHistorial[i];
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + r.fecha + "</td>";
        html += "<td>" + r.equipos + "</td>";
        html += "<td>" + r.ticketsAbiertos + "</td>";
        html += "<td>" + r.ticketsCerrados + "</td>";
        html += "<td>" + r.prestamos + "</td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

// Mostrar tickets por defecto al cargar
mostrarTickets();
