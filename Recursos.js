

const equipos = [];

const botonAgregarEquipo = document.getElementById("botonAgregarEquipo");

botonAgregarEquipo.addEventListener("click", function(event) {
    event.preventDefault();

    const nombreEquipo = document.getElementById("nombreEquipo").value;
    const tipoEquipo = document.getElementById("tipoEquipo").value;
    const mensaje = document.getElementById("mensajeEquipo");

    if (nombreEquipo == "") {
        mensaje.textContent = "Ingresá el nombre del equipo.";
        return;
    }

    if (nombreEquipo.length > 20) {
        mensaje.textContent = "El nombre no puede tener más de 20 caracteres.";
        return;
    }

    if (tipoEquipo == "") {
        mensaje.textContent = "Seleccioná el tipo de equipo.";
        return;
    }

    const equipo = {
        nombre: nombreEquipo,
        tipo: tipoEquipo,
        disponible: true
    };

    equipos.push(equipo);

    mensaje.textContent = "Equipo agregado: " + nombreEquipo;

    document.getElementById("nombreEquipo").value = "";
    document.getElementById("tipoEquipo").value = "";

    mostrarTablaEquipos();
});

function mostrarTablaEquipos() {
    const tablaVieja = document.getElementById("tablaEquipos");
    if (tablaVieja) {
        tablaVieja.remove();
    }

    const tabla = document.createElement("table");
    tabla.id = "tablaEquipos";

    const fila = tabla.insertRow();
    fila.innerHTML = "<th>Nombre</th><th>Tipo</th><th>Estado</th>";

    for (const i = 0; i < equipos.length; i++) {
        const fila = tabla.insertRow();
        fila.insertCell().textContent = equipos[i].nombre;
        fila.insertCell().textContent = equipos[i].tipo;
        fila.insertCell().textContent = equipos[i].disponible ? "Disponible" : "Prestado";
    }

    document.getElementById("registroEquipo").appendChild(tabla);
}
