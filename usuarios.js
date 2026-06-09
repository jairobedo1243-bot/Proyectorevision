

const usuarios = [];

const botonAgregarUsuario = document.getElementById("botonAgregarUsuario");

botonAgregarUsuario.addEventListener("click", function(event) {
    event.preventDefault();

    const nombre = document.getElementById("nombreUsuario").value;
    const correo = document.getElementById("correoUsuario").value;

    const mensaje = document.getElementById("mensajeUsuario");
    if (!mensaje) {
        mensaje = document.createElement("p");
        mensaje.id = "mensajeUsuario";
        document.getElementById("altaUsuarios").appendChild(mensaje);
    }

    if (nombre == "") {
        mensaje.textContent = "Ingresá el nombre del usuario.";
        return;
    }

    if (correo == "") {
        mensaje.textContent = "Ingresá el correo del usuario.";
        return;
    }

    const usuario = {
        nombre: nombre,
        correo: correo
    };

    usuarios.push(usuario);

    mensaje.textContent = "Usuario agregado: " + nombre;

    document.getElementById("nombreUsuario").value = "";
    document.getElementById("correoUsuario").value = "";

    mostrarTablaUsuarios();
});

function mostrarTablaUsuarios() {
    const tablaVieja = document.getElementById("tablaUsuarios");
    if (tablaVieja) {
        tablaVieja.remove();
    }

    const tabla = document.createElement("table");
    tabla.id = "tablaUsuarios";

    const encabezado = tabla.insertRow();
    encabezado.innerHTML = "<th>Nombre</th><th>Correo</th>";

    for (const i = 0; i < usuarios.length; i++) {
        const fila = tabla.insertRow();
        fila.insertCell().textContent = usuarios[i].nombre;
        fila.insertCell().textContent = usuarios[i].correo;
    }

    document.getElementById("altaUsuarios").appendChild(tabla);
}
