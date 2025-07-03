{{-- Sección de estadísticas generales del sitio --}}
<section class="agradecimientos-section">
    <div class="stats-cards-container">
        {{-- Contenido total --}}
        <div class="stats-card">
            <h2>Contenido Total</h2>
            <p>Enviado por nuestra increíble comunidad de jajajeros</p>
            <div class="stats-value">{{\App\Helpers\StatsHelper::getContentsAndSuggestionsTotal()}}</div>
        </div>

        {{-- Colaboradores activos --}}
        <div class="stats-card">
            <h2>Colaboradores</h2>
            <p>Usuarios activos contribuyendo a diario sin descanso</p>
            <div class="stats-value">{{\App\Helpers\StatsHelper::getUsersActiveTotal()}}</div>
        </div>

        {{-- Risas Generadas --}}
        <div class="stats-card">
            <h2>Risas Generadas</h2>
            <p>
                Imposible de contar
                <br />
                ¡Pero subiendo cada día! 😄😄😄
            </p>
            <div class="stats-value">999999999.99</div>
        </div>
    </div>
</section>
