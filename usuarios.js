const API = "http://localhost/Proyectorevision-main/api_usuarios.php";

const botonAgregarUsuario = document.getElementById("botonAgregarUsuario");
const mensajeUsuario = document.getElementById("mensajeUsuario");
const contenedor = document.getElementById("contenedorTablaUsuarios");

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className = "mensaje " + tipo;
}

async function cargarUsuarios() {
    const res = await fetch(API);
    const usuarios = await res.json();
    renderTablaUsuarios(usuarios);
}

async function agregarUsuario() {
    const nombre = document.getElementById("nombreUsuario").value.trim();
    const correo = document.getElementById("correoUsuario").value.trim();
    const rol = document.getElementById("rolUsuario").value;
    

    mostrarMensaje(mensajeUsuario, "", "");

    if (!nombre || !correo || !rol) {
        mostrarMensaje(mensajeUsuario, "Completá todos los campos.", "error");
        return;
    }

    const res = await fetch(API, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ nombre, correo, rol })
    });

    const data = await res.json();
    if (res.ok) {
        mostrarMensaje(mensajeUsuario, "Usuario agregado: " + nombre, "exito");
        document.getElementById("nombreUsuario").value = "";
        document.getElementById("correoUsuario").value = "";
        document.getElementById("rolUsuario").value = "";
        cargarUsuarios();
    } else {
        mostrarMensaje(mensajeUsuario, data.error || "Error al agregar", "error");
    }
}

async function eliminarUsuario(id) {
    if (!confirm("¿Eliminar este usuario?")) return;
    const res = await fetch(API + "?id=" + id, { method: "DELETE" });
    if (res.ok) {
        mostrarMensaje(mensajeUsuario, "Usuario eliminado.", "info");
        cargarUsuarios();
    }
}

function renderTablaUsuarios(usuarios) {
    if (!usuarios || usuarios.length === 0) {
        contenedor.innerHTML = "<p class='empty-state'>No hay usuarios registrados aún.</p>";
        return;
    }

    let html = "<table><thead><tr><th>#</th><th>Nombre</th><th>Correo</th><th>Rol</th><th>Acción</th></tr></thead><tbody>";
    for (let i = 0; i < usuarios.length; i++) {
        const u = usuarios[i];
        html += "<tr>";
        html += "<td>" + (i + 1) + "</td>";
        html += "<td>" + (u.nom || u.nombre) + "</td>";
        html += "<td>" + u.email + "</td>";
        html += "<td><span class='badge badge-info'>" + u.rol + "</span></td>";
        html += "<td><button class='btn-danger btn-sm' onclick='eliminarUsuario(" + u.ci_usuario + ")'>Eliminar</button></td>";
        html += "</tr>";
    }
    html += "</tbody></table>";
    contenedor.innerHTML = html;
}

botonAgregarUsuario.addEventListener("click", agregarUsuario);
cargarUsuarios();
