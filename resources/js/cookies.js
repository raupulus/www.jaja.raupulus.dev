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
        this.dismissButton = null;

        this.init();
    }

    /**
     * Inicializa el banner de cookies
     */
    init() {
        this.banner = document.getElementById('cookieBanner');
        this.acceptButton = document.getElementById('acceptCookies');
        this.dismissButton = document.getElementById('dismissCookies');

        if (!this.banner || !this.acceptButton) {
            return;
        }

        // Configurar event listeners
        this.setupEventListeners();

        // Verificar si ya se han aceptado las cookies
        if (!this.hasConsent()) {
            this.showBanner();
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        this.acceptButton.addEventListener('click', () => {
            this.acceptCookies();
        });

        if (this.dismissButton) {
            this.dismissButton.addEventListener('click', () => {
                this.hideBanner();
            });
        }

        // Cerrar con ESC sin otorgar consentimiento
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.banner.classList.contains('show')) {
                this.hideBanner();
            }
        });
    }

    /**
     * Muestra el banner de cookies
     */
    showBanner() {
        this.banner.hidden = false;
        this.banner.inert = false;

        // Pequeño delay para permitir que el CSS anime la entrada tras quitar hidden
        setTimeout(() => {
            this.banner.classList.add('show');
        }, 100);
    }

    /**
     * Oculta el banner de cookies sin registrar consentimiento
     */
    hideBanner() {
        const wasFocusedInside = this.banner.contains(document.activeElement);
        this.banner.classList.remove('show');
        this.banner.inert = true;

        const reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hideDelay = reducedMotion ? 0 : 300;

        setTimeout(() => {
            if (!this.banner.classList.contains('show')) {
                this.banner.hidden = true;
            }
        }, hideDelay);

        if (wasFocusedInside) {
            document.getElementById('main-content')?.focus({ preventScroll: true });
        }
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

        const secureFlag = window.location.protocol === 'https:' ? '; Secure' : '';
        const cookieString = `${name}=${value}; expires=${expires.toUTCString()}; path=/; SameSite=Lax${secureFlag}`;
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
