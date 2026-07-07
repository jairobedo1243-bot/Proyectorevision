
const equipos = [];

const botonAgregarEquipo = document.getElementById("botonAgregarEquipo");
const mensajeEquipo      = document.getElementById("mensajeEquipo");

botonAgregarEquipo.addEventListener("click", function () {
    const nombre  = document.getElementById("nombreEquipo").value.trim();
    const marca   = document.getElementById("marcaEquipo").value.trim();
    const nroSerie = document.getElementById("nroSerieEquipo").value.trim();
    const tipo    = document.getElementById("tipoEquipo").value;

    mostrarMensaje(mensajeEquipo, "", "");

    if (nombre === "") {
        mostrarMensaje(mensajeEquipo, "Ingresá el nombre del equipo.", "error");
        return;
    }
    if (nombre.length > 40) {
        mostrarMensaje(mensajeEquipo, "El nombre no puede tener más de 40 caracteres.", "error");
        return;
    }
    if (tipo === "") {
        mostrarMensaje(mensajeEquipo, "Seleccioná el tipo de equipo.", "error");
        return;
    }

    equipos.push({ nombre: nombre, marca: marca, nroSerie: nroSerie, tipo: tipo, disponible: true });
    mostrarMensaje(mensajeEquipo, "Equipo agregado: " + nombre, "exito");

    document.getElementById("nombreEquipo").value    = "";
    document.getElementById("marcaEquipo").value     = "";
    document.getElementById("nroSerieEquipo").value  = "";
    document.getElementById("tipoEquipo").value      = "";

    renderTablaEquipos();
});

function renderTablaEquipos() {
    const contenedor = document.getElementById("contenedorTablaEquipos");

    if (equipos.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay equipos registrados aún.</p>";
        return;
    }

    let html = "<table><thead><tr><th>#</th><th>Nombre</th><th>Marca/Modelo</th><th>N° Serie</th><th>Tipo</th><th>Estado</th></tr></thead><tbody>";
    for (let i = 0; i < equipos.length; i++) {
        const e = equipos[i];
        const badgeClase = e.disponible ? "badge-success" : "badge-danger";
        const badgeTexto = e.disponible ? "Disponible" : "Prestado";
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + e.nombre + "</td>";
        html += "<td>" + (e.marca || "—") + "</td>";
        html += "<td>" + (e.nroSerie || "—") + "</td>";
        html += "<td>" + e.tipo + "</td>";
        html += "<td><span class='badge " + badgeClase + "'>" + badgeTexto + "</span></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}
