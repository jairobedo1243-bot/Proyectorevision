// =============================================
// MÓDULO: Reportes
// =============================================

// Datos de muestra representativos del sistema
const equipos   = [
    { nombre: "PC-Laboratorio-01", tipo: "PC",         disponible: true  },
    { nombre: "Proyector-Aula-A",  tipo: "Proyector",  disponible: false },
    { nombre: "TV-Sala-Profesores",tipo: "Television", disponible: true  },
    { nombre: "PC-Laboratorio-02", tipo: "PC",         disponible: true  }
];
const tickets   = [
    { problema: "PC no enciende",          prioridad: "Alta",  estado: "Abierto"  },
    { problema: "Sin conexión a internet", prioridad: "Media", estado: "Cerrado"  },
    { problema: "Proyector sin señal",     prioridad: "Alta",  estado: "Abierto"  }
];
const prestamos = [
    { equipo: "Proyector-Aula-A", devuelto: false },
    { equipo: "PC-Lab-03",        devuelto: true  }
];

const mensajeReporte = document.getElementById("mensajeReporte");

document.getElementById("botonGenerarReporte").addEventListener("click", function () {

    // Calcular totales
    let equiposDisponibles = 0, equiposPrestados = 0;
    for (let i = 0; i < equipos.length; i++) {
        if (equipos[i].disponible) { equiposDisponibles++; } else { equiposPrestados++; }
    }

    let ticketsAbiertos = 0, ticketsCerrados = 0;
    for (let i = 0; i < tickets.length; i++) {
        if (tickets[i].estado === "Abierto") { ticketsAbiertos++; } else { ticketsCerrados++; }
    }

    let prestamosActivos = 0, prestamosDevueltos = 0;
    for (let i = 0; i < prestamos.length; i++) {
        if (!prestamos[i].devuelto) { prestamosActivos++; } else { prestamosDevueltos++; }
    }

    const fecha = new Date().toLocaleDateString("es-UY");

    // Cards de resumen
    let html = "<div class='stats-grid'>";
    html += tarjetaStat(equipos.length,       "Equipos totales",    "#1e3a5f");
    html += tarjetaStat(equiposDisponibles,   "Disponibles",        "#16a34a");
    html += tarjetaStat(equiposPrestados,     "Prestados",          "#d97706");
    html += tarjetaStat(ticketsAbiertos,      "Tickets abiertos",   "#dc2626");
    html += tarjetaStat(ticketsCerrados,      "Tickets cerrados",   "#16a34a");
    html += tarjetaStat(prestamosActivos,     "Préstamos activos",  "#d97706");
    html += "</div>";

    // Tabla detallada
    const datos = [
        ["Total de equipos",    equipos.length],
        ["Equipos disponibles", equiposDisponibles],
        ["Equipos prestados",   equiposPrestados],
        ["Tickets abiertos",    ticketsAbiertos],
        ["Tickets cerrados",    ticketsCerrados],
        ["Préstamos activos",   prestamosActivos],
        ["Préstamos devueltos", prestamosDevueltos]
    ];

    html += "<div class='tabla-wrapper'><table><thead><tr><th>Concepto</th><th>Valor</th></tr></thead><tbody>";
    for (let i = 0; i < datos.length; i++) {
        html += "<tr><td>" + datos[i][0] + "</td><td><strong>" + datos[i][1] + "</strong></td></tr>";
    }
    html += "</tbody></table></div>";
    html += "<p style='margin-top:12px;font-size:0.8rem;color:var(--text-muted)'>Reporte generado el " + fecha + "</p>";

    document.getElementById("contenedorReporte").innerHTML = html;
    mostrarMensaje(mensajeReporte, "Reporte generado correctamente.", "exito");
});

function tarjetaStat(numero, etiqueta, color) {
    return "<div class='stat-card' style='border-top-color:" + color + "'>" +
           "<div class='numero' style='color:" + color + "'>" + numero + "</div>" +
           "<div class='etiqueta'>" + etiqueta + "</div>" +
           "</div>";
}

function mostrarMensaje(el, texto, tipo) {
    el.textContent = texto;
    el.className   = "mensaje " + tipo;
}
