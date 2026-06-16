

const prestamos = [];


const mensajePrestamo = document.getElementById("mensajePrestamo");

cargarSelectEquipos();

function cargarSelectEquipos() {
    const select = document.getElementById("equipoPrestamo");
    select.innerHTML = "<option value=''>Seleccionar equipo…</option>";
    for (let i = 0; i < equipos.length; i++) {
        if (equipos[i].disponible) {
            const op = document.createElement("option");
            op.value       = i;
            op.textContent = equipos[i].nombre + " (" + equipos[i].tipo + ")";
            select.appendChild(op);
        }
    }
}

document.getElementById("botonRegistrarPrestamo").addEventListener("click", function () {
    const solicitante = document.getElementById("nombreSolicitante").value.trim();
    const indice      = document.getElementById("equipoPrestamo").value;
    const fechaDev    = document.getElementById("fechaDevolucion").value;

    mostrarMensaje(mensajePrestamo, "", "");

    if (solicitante === "") {
        mostrarMensaje(mensajePrestamo, "Ingresá el nombre del solicitante.", "error");
        return;
    }
    if (indice === "") {
        mostrarMensaje(mensajePrestamo, "Seleccioná un equipo.", "error");
        return;
    }

    const idx = parseInt(indice);
    equipos[idx].disponible = false;

    prestamos.push({
        solicitante: solicitante,
        equipo: equipos[idx].nombre,
        tipo: equipos[idx].tipo,
        fechaDevolucion: fechaDev || "—",
        devuelto: false,
        equipoIdx: idx
    });

    mostrarMensaje(mensajePrestamo, "Préstamo registrado: " + equipos[idx].nombre + " → " + solicitante, "exito");

    document.getElementById("nombreSolicitante").value = "";
    document.getElementById("equipoPrestamo").value    = "";
    document.getElementById("fechaDevolucion").value   = "";

    cargarSelectEquipos();
    renderTablaPrestamos();
});

function renderTablaPrestamos() {
    const contenedor = document.getElementById("contenedorTablaPrestamos");

    if (prestamos.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay préstamos registrados aún.</p>";
        return;
    }

    let html = "<table><thead><tr><th>#</th><th>Solicitante</th><th>Equipo</th><th>Tipo</th><th>Devolución</th><th>Estado</th><th>Acción</th></tr></thead><tbody>";
    for (let i = 0; i < prestamos.length; i++) {
        const p = prestamos[i];
        const badgeClase = p.devuelto ? "badge-success" : "badge-warning";
        const badgeTexto = p.devuelto ? "Devuelto" : "En préstamo";
        const accion     = p.devuelto
            ? "<span style='color:var(--text-muted)'></span>"
            : "<button class='btn-sm btn-success' onclick='devolverEquipo(" + i + ")'>Devolver</button>";

        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + p.solicitante + "</td>";
        html += "<td>" + p.equipo + "</td>";
        html += "<td>" + p.tipo + "</td>";
        html += "<td>" + p.fechaDevolucion + "</td>";
        html += "<td><span class='badge " + badgeClase + "'>" + badgeTexto + "</span></td>";
        html += "<td>" + accion + "</td>";
        html += "</tr>";
        
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function devolverEquipo(i) {
    prestamos[i].devuelto = true;
    equipos[prestamos[i].equipoIdx].disponible = true;
    cargarSelectEquipos();
    renderTablaPrestamos();
    mostrarMensaje(mensajePrestamo, "Equipo devuelto: " + prestamos[i].equipo, "info");
}

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}
