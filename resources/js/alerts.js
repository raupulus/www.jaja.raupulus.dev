// Oculto alertas tras 5 segundos
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.main-alert.alert-success, .main-alert.alert-danger');

    if (alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                alert.classList.add('fade-out');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            });
        }, 5000);
    }

});
