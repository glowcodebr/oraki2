<?php
// src/layout/modals/loader.php

// 1. Prepara os dados
if (!isset($statsService)) {
    require_once __DIR__ . '/../../services/DashboardStats.php';
    $statsService = new DashboardStats();
}

// Busca listas apenas se estivermos logados
if (isset($currentUserId)) {
    $clientsList = $statsService->getClientsList($currentUserId);
    $servicesList = $statsService->getServicesList($currentUserId);
} else {
    $clientsList = [];
    $servicesList = [];
}
?>

<div id="modal-overlay" class="hidden fixed inset-0 bg-astral/80 backdrop-blur-sm z-40 transition-opacity opacity-0" onclick="closeAllModals()"></div>

<?php include __DIR__ . '/appointment.php'; ?>
<?php include __DIR__ . '/client.php'; ?>
<?php include __DIR__ . '/service.php'; ?>

<script>
    function openModal(modalId) {
        console.log("Tentando abrir modal:", modalId); // Debug para ver no F12
        
        const overlay = document.getElementById('modal-overlay');
        const modal = document.getElementById(modalId);

        if (!modal) {
            console.error("Erro: Modal não encontrado com o ID " + modalId);
            return;
        }

        // Mostra o overlay
        overlay.classList.remove('hidden');
        // Pequeno delay para permitir o browser renderizar antes da transição
        setTimeout(() => {
            overlay.classList.remove('opacity-0');
        }, 10);

        // Mostra o modal
        modal.classList.remove('hidden');
        setTimeout(() => {
            modal.classList.remove('opacity-0', 'scale-95');
            modal.classList.add('scale-100');
        }, 10);
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        const overlay = document.getElementById('modal-overlay');

        if(modal) {
            modal.classList.add('opacity-0', 'scale-95');
            modal.classList.remove('scale-100');
        }
        
        if(overlay) {
            overlay.classList.add('opacity-0');
        }

        // Espera a animação acabar para esconder
        setTimeout(() => {
            if(modal) modal.classList.add('hidden');
            if(overlay) overlay.classList.add('hidden');
        }, 300);
    }

    function closeAllModals() {
        const modals = document.querySelectorAll('[id^="modal-"]');
        modals.forEach(modal => {
            if (modal.id !== 'modal-overlay') {
                closeModal(modal.id);
            }
        });
    }
</script>