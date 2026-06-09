
const tickets = [
    { problema: "La PC no enciende", estado: "Abierto" },
    { problema: "Sin conexión a internet", estado: "Cerrado" }
];

const reportes = [
    { fecha: "01/06/2026", equipos: 3, tickets: 2 },
    { fecha: "02/06/2026", equipos: 5, tickets: 4 }
];

document.getElementById("botonMostrarTickets").addEventListener("click", function() {
    mostrarTickets();
    document.getElementById("tablaReportes").style.display = "none";
});

document.getElementById("botonMostrarReportes").addEventListener("click", function() {
    mostrarReportes();
    document.getElementById("tablaTickets").style.display = "none";
});

function mostrarTickets() {
    const tabla = document.getElementById("tablaTickets");
    const cuerpo = document.getElementById("cuerpoTablaTickets");
    cuerpo.innerHTML = "";

    const encabezado = tabla.querySelector("thead tr");
    encabezado.innerHTML = "<th>#</th><th>Problema</th><th>Estado</th>";

    for (const i = 0; i < tickets.length; i++) {
        const fila = document.createElement("tr");

        const tdNum = document.createElement("td");
        tdNum.textContent = i + 1;

        const tdProblema = document.createElement("td");
        tdProblema.textContent = tickets[i].problema;

        const tdEstado = document.createElement("td");
        tdEstado.textContent = tickets[i].estado;

        fila.appendChild(tdNum);
        fila.appendChild(tdProblema);
        fila.appendChild(tdEstado);
        cuerpo.appendChild(fila);
    }

    tabla.style.display = "table";
}

function mostrarReportes() {
    const tabla = document.getElementById("tablaReportes");
    const cuerpo = document.getElementById("cuerpoTablaReportes");
    cuerpo.innerHTML = "";

    const encabezado = tabla.querySelector("thead tr");
    encabezado.innerHTML = "<th>#</th><th>Fecha</th><th>Equipos</th><th>Tickets</th>";

    for (const i = 0; i < reportes.length; i++) {
        const fila = document.createElement("tr");

        const tdNum = document.createElement("td");
        tdNum.textContent = i + 1;

        const tdFecha = document.createElement("td");
        tdFecha.textContent = reportes[i].fecha;

        const tdEquipos = document.createElement("td");
        tdEquipos.textContent = reportes[i].equipos;

        const tdTickets = document.createElement("td");
        tdTickets.textContent = reportes[i].tickets;

        fila.appendChild(tdNum);
        fila.appendChild(tdFecha);
        fila.appendChild(tdEquipos);
        fila.appendChild(tdTickets);
        cuerpo.appendChild(fila);
    }

    tabla.style.display = "table";
}
