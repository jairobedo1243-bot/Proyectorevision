(function () {
    const USERS_KEY = 'sgrsi_usuarios';
     const SESSION_KEY = 'sgrsi_session';

    function getUsers() {
        return JSON.parse(localStorage.getItem(USERS_KEY) || '[]');
    }

    function saveUsers(users) {
        localStorage.setItem(USERS_KEY, JSON.stringify(users));
    }

    function seedDefaultUsers() {
        const users = getUsers();
        if (users.length === 0) {
            users.push(
                {
                    nombre: 'Administrador',
                    correo: 'admin@sgrsi.edu',
                    password: 'admin123',
                    rol: 'Administrador'
                },
                {
                    nombre: 'Coordinador',
                    correo: 'coord@sgrsi.edu',
                    password: 'coord123',
                    rol: 'Coordinador'
                }
            );
            saveUsers(users);
        }
    }

    seedDefaultUsers();

    const rolePages = {
        'Docente': ['index.html', 'indexTickets.html'],
        'Técnico': ['index.html', 'indexRecursos.html', 'indexPrestamos.html', 'indexTickets.html', 'indexHistorial.html'],
        'Administrador': ['index.html', 'indexRecursos.html', 'indexUsuarios.html', 'indexPrestamos.html', 'indexTickets.html', 'indexReportes.html', 'indexHistorial.html'],
        'Coordinador': ['index.html', 'indexRecursos.html', 'indexUsuarios.html', 'indexPrestamos.html', 'indexTickets.html', 'indexReportes.html', 'indexHistorial.html']
    };

    window.Auth = {
        login: function (correo, password) {
             const users = getUsers();
            for ( let i = 0; i < users.length; i++) {
                if (users[i].correo === correo && users[i].password === password) {
                    const session = { nombre: users[i].nombre, correo: users[i].correo, rol: users[i].rol };
                    sessionStorage.setItem(SESSION_KEY, JSON.stringify(session));
                    return { success: true };
                }
            }
            return { success: false, mensaje: 'Correo o contraseña incorrectos.' };
        },

        logout: function () {
            sessionStorage.removeItem(SESSION_KEY);
            window.location.href = 'login.html';
        },

        getCurrentUser: function () {
            return JSON.parse(sessionStorage.getItem(SESSION_KEY) || 'null');
        },

        requireAuth: function () {
            const user = this.getCurrentUser();
            if (!user) {
                window.location.href = 'login.html';
                return null;
            }
            return user;
        },

        hasAccess: function (page) {
            const user = this.getCurrentUser();
            if (!user) return false;
            const allowed = rolePages[user.rol] || [];
            return allowed.indexOf(page) !== -1;
        },

        initNav: function () {
            const user = this.getCurrentUser();
            if (!user) return;

            const header = document.querySelector('header');
            if (header) {
                const infoDiv = document.createElement('div');
                infoDiv.className = 'user-info';
                infoDiv.innerHTML = '<span><strong>' + user.nombre + '</strong> <span class="rol-badge">' + user.rol + '</span></span> <button class="btn-logout" onclick="Auth.logout()">Cerrar sesion</button>';
                header.appendChild(infoDiv);
            }

            const allowed = rolePages[user.rol] || [];
            const nav = document.querySelector('nav');
            if (nav) {
                const links = nav.querySelectorAll('a');
                for (let i = 0; i < links.length; i++) {
                    const href = links[i].getAttribute('href');
                    if (allowed.indexOf(href) === -1) {
                        links[i].style.display = 'none';
                    }
                }
            }
        }
    };

    const currentPage = window.location.pathname;
    const isLoginPage = currentPage.indexOf('login.html') >= 0;

    if (!isLoginPage) {
        const user = Auth.requireAuth();
        if (user) {
            const fileName = currentPage.split('/').pop() || 'index.html';
            if (!Auth.hasAccess(fileName) && fileName.indexOf('index') === 0) {
                window.location.href = 'index.html';
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () { Auth.initNav(); });
        } else {
            Auth.initNav();
        }
    }
})();
