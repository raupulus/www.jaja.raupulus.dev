{{-- Cartelito BETA --}}
<div class="beta-label-simple">
    <span>B</span>
    <span>E</span>
    <span>T</span>
    <span>A</span>
</div>

<style>
    .beta-label-simple {
        position: fixed;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        background: linear-gradient(135deg, #ff6b6b, #ee5a24);
        color: white;
        padding: 12px 6px;
        font-size: 11px;
        font-weight: bold;
        letter-spacing: 1px;
        z-index: 1000;
        border-radius: 0 8px 8px 0;
        box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
        cursor: default;
        user-select: none;
        width: 25px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 2px;
    }

    .beta-label-simple span {
        display: block;
        line-height: 1;
    }

    .beta-label-simple:hover {
        background: linear-gradient(135deg, #ff7675, #fd79a8);
        transform: translateY(-50%) scale(1.05);
        transition: all 0.3s ease;
    }

    /* Responsive - ocultar en móviles pequeños */
    @media (max-width: 768px) {
        .beta-label-simple {
            display: none;
        }
    }
</style>
