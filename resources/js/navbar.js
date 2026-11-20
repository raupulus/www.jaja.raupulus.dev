document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    if (!menuToggle || !navLinks) {
        return;
    }

    const setMenuOpen = (isOpen) => {
        menuToggle.classList.toggle('active', isOpen);
        navLinks.classList.toggle('active', isOpen);
        menuToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        menuToggle.setAttribute('aria-label', isOpen ? 'Cerrar menú' : 'Abrir menú');
    };

    menuToggle.addEventListener('click', function () {
        const isOpen = !navLinks.classList.contains('active');
        setMenuOpen(isOpen);
    });

    // Cierra el menú al hacer clic en un enlace (versión móvil/tablet)
    const navItems = document.querySelectorAll('.nav-links a');
    navItems.forEach((item) => {
        item.addEventListener('click', function () {
            if (window.innerWidth <= 1024 && navLinks.classList.contains('active')) {
                setMenuOpen(false);
            }
        });
    });

    // Cierra el menú con Escape
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && navLinks.classList.contains('active')) {
            setMenuOpen(false);
            menuToggle.focus();
        }
    });
});
