

const prestamos = [];


const equipos = [
    { nombre: "PC-01", tipo: "PC", disponible: true },
    { nombre: "Laptop-01", tipo: "Laptop", disponible: true },
    { nombre: "Impresora-01", tipo: "Impresora", disponible: true }
];

cargarSelectEquipos();

function cargarSelectEquipos() {
    const select = document.getElementById("equipoPrestamo");
    select.innerHTML = "<option value=''>Seleccionar equipo</option>";

    for (const i = 0; i < equipos.length; i++) {
        if (equipos[i].disponible) {
            const opcion = document.createElement("option");
            opcion.value = i;
            opcion.textContent = equipos[i].nombre + " (" + equipos[i].tipo + ")";
            select.appendChild(opcion);
        }
    }
}

const botonRegistrarPrestamo = document.getElementById("botonRegistrarPrestamo");

botonRegistrarPrestamo.addEventListener("click", function(event) {
    event.preventDefault();

    const solicitante = document.getElementById("nombreSolicitante").value;
    const indice = document.getElementById("equipoPrestamo").value;

    const mensaje = document.getElementById("mensajePrestamo");
    if (!mensaje) {
        mensaje = document.createElement("p");
        mensaje.id = "mensajePrestamo";
        document.getElementById("prestamos").appendChild(mensaje);
    }

    if (solicitante == "") {
        mensaje.textContent = "Ingresá el nombre del solicitante.";
        return;
    }

    if (indice == "") {
        mensaje.textContent = "Seleccioná un equipo.";
        return;
    }

    equipos[indice].disponible = false;

    const prestamo = {
        solicitante: solicitante,
        equipo: equipos[indice].nombre,
        tipo: equipos[indice].tipo,
        devuelto: false
    };

    prestamos.push(prestamo);

    mensaje.textContent = "Préstamo registrado: " + equipos[indice].nombre + " → " + solicitante;

    document.getElementById("nombreSolicitante").value = "";
    document.getElementById("equipoPrestamo").value = "";

    cargarSelectEquipos();
    mostrarTablaPrestamos();
});

function mostrarTablaPrestamos() {
    const tablaVieja = document.getElementById("tablaPrestamos");
    if (tablaVieja) {
        tablaVieja.remove();
    }

    const tabla = document.createElement("table");
    tabla.id = "tablaPrestamos";

    const encabezado = tabla.insertRow();
    encabezado.innerHTML = "<th>Solicitante</th><th>Equipo</th><th>Tipo</th><th>Estado</th><th>Devolver</th>";

    for (const i = 0; i < prestamos.length; i++) {
        const fila = tabla.insertRow();
        fila.insertCell().textContent = prestamos[i].solicitante;
        fila.insertCell().textContent = prestamos[i].equipo;
        fila.insertCell().textContent = prestamos[i].tipo;
        fila.insertCell().textContent = prestamos[i].devuelto ? "Devuelto" : "En préstamo";

        const celdaBtn = fila.insertCell();
        if (!prestamos[i].devuelto) {
            const btn = document.createElement("button");
            btn.textContent = "Devolver";
            btn.setAttribute("data-indice", i);
            btn.addEventListener("click", function() {
                const idx = parseInt(this.getAttribute("data-indice"));
                devolverEquipo(idx);
            });
            celdaBtn.appendChild(btn);
        } else {
            celdaBtn.textContent = " ";
        }
    }

    document.getElementById("prestamos").appendChild(tabla);
}

function devolverEquipo(indice) {
    prestamos[indice].devuelto = true;

   for (const i = 0; i < equipos.length; i++) {
        if (equipos[i].nombre == prestamos[indice].equipo) {
            equipos[i].disponible = true;
            break;
        }
    }

    cargarSelectEquipos();
    mostrarTablaPrestamos();
}
