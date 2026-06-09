
const tickets = [];

const formTicket = document.getElementById("formTicket");

formTicket.addEventListener("submit", function(event) {
    event.preventDefault();

    const problema = document.getElementById("problemaTicket").value;
    const mensaje = document.getElementById("mensajeTicket");

    if (problema == "") {
        mensaje.textContent = "Describí el problema.";
        return;
    }

    const ticket = {
        problema: problema,
        estado: "Abierto"
    };

    tickets.push(ticket);

    mensaje.textContent = "Ticket creado correctamente.";

    document.getElementById("problemaTicket").value = "";

    mostrarTablaTickets();
});

function mostrarTablaTickets() {
    const tablaVieja = document.getElementById("tablaTickets");
    if (tablaVieja) {
        tablaVieja.remove();
    }

    const tabla = document.createElement("table");
    tabla.id = "tablaTickets";

    const encabezado = tabla.insertRow();
    encabezado.innerHTML = "<th>#</th><th>Problema</th><th>Estado</th><th>Cerrar</th>";

    for (const i = 0; i < tickets.length; i++) {
        const fila = tabla.insertRow();
        fila.insertCell().textContent = i + 1;
        fila.insertCell().textContent = tickets[i].problema;
        fila.insertCell().textContent = tickets[i].estado;

        const celdaBtn = fila.insertCell();
        if (tickets[i].estado == "Abierto") {
            const btn = document.createElement("button");
            btn.textContent = "Cerrar";
            btn.setAttribute("data-indice", i);
            btn.addEventListener("click", function() {
                const idx = parseInt(this.getAttribute("data-indice"));
                tickets[idx].estado = "Cerrado";
                mostrarTablaTickets();
            });
            celdaBtn.appendChild(btn);
        } else {
            celdaBtn.textContent = "✓";
        }
    }

    document.getElementById("tickets").appendChild(tabla);
}
