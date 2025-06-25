/**
 * Cookie Banner Manager
 * Maneja la funcionalidad del banner de cookies
 */
class CookieBanner {
    constructor() {
        this.cookieName = 'cookieConsent';
        this.cookieValue = 'accepted';
        this.cookieExpireDays = 365; // 1 año
        this.banner = null;
        this.acceptButton = null;

        this.init();
    }

    /**
     * Inicializa el banner de cookies
     */
    init() {
        this.banner = document.getElementById('cookieBanner');
        this.acceptButton = document.getElementById('acceptCookies');

        if (!this.banner || !this.acceptButton) {
            console.warn('Cookie banner elements not found');
            return;
        }

        // Verificar si ya se han aceptado las cookies
        if (!this.hasConsent()) {
            this.showBanner();
        }

        // Configurar event listeners
        this.setupEventListeners();
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        this.acceptButton.addEventListener('click', () => {
            this.acceptCookies();
        });

        // Cerrar con ESC (opcional)
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.banner.classList.contains('show')) {
                this.acceptCookies();
            }
        });
    }

    /**
     * Muestra el banner de cookies
     */
    showBanner() {
        // Pequeño delay para permitir que el CSS se cargue
        setTimeout(() => {
            this.banner.classList.add('show');
        }, 100);
    }

    /**
     * Oculta el banner de cookies
     */
    hideBanner() {
        this.banner.classList.remove('show');
    }

    /**
     * Acepta las cookies y oculta el banner
     */
    acceptCookies() {
        this.setCookie(this.cookieName, this.cookieValue, this.cookieExpireDays);
        this.hideBanner();

        // Disparar evento personalizado para otros scripts
        document.dispatchEvent(new CustomEvent('cookiesAccepted', {
            detail: { timestamp: new Date().toISOString() }
        }));
    }

    /**
     * Verifica si el usuario ya ha dado su consentimiento
     * @returns {boolean}
     */
    hasConsent() {
        return this.getCookie(this.cookieName) === this.cookieValue;
    }

    /**
     * Establece una cookie
     * @param {string} name - Nombre de la cookie
     * @param {string} value - Valor de la cookie
     * @param {number} days - Días hasta que expire
     */
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));

        const cookieString = `${name}=${value}; expires=${expires.toUTCString()}; path=/; SameSite=Lax; Secure`;
        document.cookie = cookieString;
    }

    /**
     * Obtiene el valor de una cookie
     * @param {string} name - Nombre de la cookie
     * @returns {string|null}
     */
    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');

        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return c.substring(nameEQ.length, c.length);
            }
        }
        return null;
    }

    /**
     * Elimina una cookie
     * @param {string} name - Nombre de la cookie
     */
    deleteCookie(name) {
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    }

    /**
     * Método público para resetear el consentimiento (útil para testing)
     */
    resetConsent() {
        this.deleteCookie(this.cookieName);
        this.showBanner();
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.cookieBanner = new CookieBanner();
});

// Exporto para uso en módulos ES6
export default CookieBanner;
