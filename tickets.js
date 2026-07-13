
const tickets = [];
const mensajeTicket = document.getElementById("mensajeTicket");

document.getElementById("formTicket").addEventListener("submit", function (event) {
    event.preventDefault();

    const solicitante = document.getElementById("solicitanteTicket").value.trim();
    const prioridad   = document.getElementById("prioridadTicket").value;
    const problema    = document.getElementById("problemaTicket").value.trim();

    mostrarMensaje(mensajeTicket, "", "");

    if (solicitante === "") {
        mostrarMensaje(mensajeTicket, "Ingresá el nombre del solicitante.", "error");
        return;
    }
    if (prioridad === "") {
        mostrarMensaje(mensajeTicket, "Seleccioná la prioridad.", "error");
        return;
    }
    if (problema === "") {
        mostrarMensaje(mensajeTicket, "Describí el problema.", "error");
        return;
    }

    const fecha = new Date().toLocaleDateString("es-UY");

    tickets.push({
        solicitante: solicitante,
        prioridad: prioridad,
        problema: problema,
        estado: "Abierto",
        fecha: fecha
    });

    mostrarMensaje(mensajeTicket, "Ticket #" + tickets.length + " creado correctamente.", "exito");

    document.getElementById("solicitanteTicket").value = "";
    document.getElementById("prioridadTicket").value   = "";
    document.getElementById("problemaTicket").value    = "";

    renderTablaTickets();
});

function renderTablaTickets() {
    const contenedor = document.getElementById("contenedorTablaTickets");

    if (tickets.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay tickets registrados aún.</p>";
        return;
    }

    let html = "<table><thead><tr><th>#</th><th>Fecha</th><th>Solicitante</th><th>Prioridad</th><th>Problema</th><th>Estado</th><th>Acción</th></tr></thead><tbody>";

    for (let i = 0; i < tickets.length; i++) {
        const t = tickets[i];
        const prioClase = t.prioridad === "Alta" ? "badge-danger" : t.prioridad === "Media" ? "badge-warning" : "badge-info";
        const estadoClase = t.estado === "Abierto" ? "badge-warning" : "badge-success";
        const accion = t.estado === "Abierto"
            ? "<button class='btn-sm btn-success' onclick='cerrarTicket(" + i + ")'>Cerrar</button>"
            : "<span style='color:var(--text-muted)'>✓ Cerrado</span>";

        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + t.fecha + "</td>";
        html += "<td>" + t.solicitante + "</td>";
        html += "<td><span class='badge " + prioClase + "'>" + t.prioridad + "</span></td>";
        html += "<td>" + t.problema + "</td>";
        html += "<td><span class='badge " + estadoClase + "'>" + t.estado + "</span></td>";
        html += "<td>" + accion + "</td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function cerrarTicket(i) {
    tickets[i].estado = "Cerrado";
    renderTablaTickets();
    mostrarMensaje(mensajeTicket, "Ticket #" + (i + 1) + " cerrado.", "info");
}

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}
