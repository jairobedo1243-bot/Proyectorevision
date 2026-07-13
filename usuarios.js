const USERS_KEY = 'sgrsi_usuarios';
const usuarios = JSON.parse(localStorage.getItem(USERS_KEY) || '[]');

const botonAgregarUsuario = document.getElementById("botonAgregarUsuario");
const mensajeUsuario = document.getElementById("mensajeUsuario");

function guardarUsuarios() {
    localStorage.setItem(USERS_KEY, JSON.stringify(usuarios));
}

botonAgregarUsuario.addEventListener("click", function () {
    const nombre = document.getElementById("nombreUsuario").value.trim();
    const correo = document.getElementById("correoUsuario").value.trim();
    const password = document.getElementById("passwordUsuario").value.trim();
    const rol    = document.getElementById("rolUsuario").value;

    mostrarMensaje(mensajeUsuario, "", "");

    if (nombre === "") {
        mostrarMensaje(mensajeUsuario, "Ingresa el nombre del usuario.", "error");
        return;
    }
    if (correo === "") {
        mostrarMensaje(mensajeUsuario, "Ingresa el correo electronico.", "error");
        return;
    }
    if (password === "") {
        mostrarMensaje(mensajeUsuario, "Ingresa una contrasena.", "error");
        return;
    }
    if (rol === "") {
        mostrarMensaje(mensajeUsuario, "Selecciona un rol.", "error");
        return;
    }

    const duplicado = usuarios.some(function (u) { return u.correo === correo; });
    if (duplicado) {
        mostrarMensaje(mensajeUsuario, "Ya existe un usuario con ese correo.", "error");
        return;
    }

    usuarios.push({ nombre: nombre, correo: correo, password: password, rol: rol });
    guardarUsuarios();
    mostrarMensaje(mensajeUsuario, "Usuario agregado: " + nombre + " (" + rol + ")", "exito");

    document.getElementById("nombreUsuario").value = "";
    document.getElementById("correoUsuario").value = "";
    document.getElementById("passwordUsuario").value = "";
    document.getElementById("rolUsuario").value    = "";

    renderTablaUsuarios();
});

function renderTablaUsuarios() {
    const contenedor = document.getElementById("contenedorTablaUsuarios");

    if (usuarios.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay usuarios registrados aun.</p>";
        return;
    }

    const html = "<table><thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Accion</th></tr></thead><tbody>";
    for (let i = 0; i < usuarios.length; i++) {
        const u = usuarios[i];
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + u.nombre + "</td>";
        html += "<td>" + u.correo + "</td>";
        html += "<td><span class='badge badge-info'>" + u.rol + "</span></td>";
        html += "<td><button class='btn-danger btn-sm' onclick='eliminarUsuario(" + i + ")'>Eliminar</button></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

function eliminarUsuario(i) {
    if (confirm("Eliminar el usuario \"" + usuarios[i].nombre + "\"?")) {
        usuarios.splice(i, 1);
        guardarUsuarios();
        renderTablaUsuarios();
        mostrarMensaje(mensajeUsuario, "Usuario eliminado.", "info");
    }
}

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}

renderTablaUsuarios();
