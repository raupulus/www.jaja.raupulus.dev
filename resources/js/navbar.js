document.addEventListener('DOMContentLoaded', function () {
    const navbar = document.querySelector('.navbar');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (!menuToggle || !navLinks) {
        return;
    }

    const mobileMediaQuery = window.matchMedia('(max-width: 1024px)');

    const syncInertState = () => {
        navLinks.inert = mobileMediaQuery.matches && !navLinks.classList.contains('active');
    };

    const setMenuOpen = (isOpen) => {
        menuToggle.classList.toggle('active', isOpen);
        navLinks.classList.toggle('active', isOpen);
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuToggle.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
        syncInertState();
    };

    // Sincronizar estado inicial (oculta del tab-order en móvil si está cerrado)
    syncInertState();

    const handleMediaChange = (event) => {
        if (!event.matches) {
            setMenuOpen(false);
        } else {
            syncInertState();
        }
    };

    if (typeof mobileMediaQuery.addEventListener === 'function') {
        mobileMediaQuery.addEventListener('change', handleMediaChange);
    } else if (typeof mobileMediaQuery.addListener === 'function') {
        mobileMediaQuery.addListener(handleMediaChange);
    }

    menuToggle.addEventListener('click', function () {
        const isOpen = !navLinks.classList.contains('active');
        setMenuOpen(isOpen);
    });

    // Cierra el menú al hacer clic en un enlace (versión móvil/tablet)
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach((item) => {
        item.addEventListener('click', function () {
            if (mobileMediaQuery.matches && navLinks.classList.contains('active')) {
                setMenuOpen(false);
            }
        });
    });

    // Cierra el menú al hacer clic fuera de la barra de navegación en móvil
    document.addEventListener('click', function (event) {
        if (
            mobileMediaQuery.matches
            && navLinks.classList.contains('active')
            && navbar
            && !navbar.contains(event.target)
        ) {
            setMenuOpen(false);
        }
    });

    // Cierra el menú con Escape y devuelve el foco al botón
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && navLinks.classList.contains('active')) {
            setMenuOpen(false);
            menuToggle.focus();
        }
    });
});
