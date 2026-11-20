<div id="cookieBanner"
     class="cookie-banner"
     role="dialog"
     aria-modal="false"
     aria-labelledby="cookieBannerTitle"
     aria-describedby="cookieBannerText"
     hidden
     inert>
    <div class="cookie-banner-content">
        <div class="cookie-banner-text">
            <h2 id="cookieBannerTitle" class="visually-hidden">Aviso de cookies</h2>
            <p id="cookieBannerText">
                Utilizamos únicamente cookies esenciales para garantizar la seguridad y funcionalidad del sitio web.
                <a href="{{route('page.show', 'politica-de-cookies')}}" target="_blank" rel="noopener noreferrer">
                    Consulta nuestra política de cookies
                </a>
                para más información.
            </p>
        </div>
        <div class="cookie-banner-actions">
            <button type="button" id="dismissCookies" class="btn btn--secondary cookie-dismiss-btn">
                Cerrar sin guardar
            </button>
            <button type="button" id="acceptCookies" class="cookie-accept-btn">
                Aceptar esenciales
            </button>
        </div>
    </div>
</div>
