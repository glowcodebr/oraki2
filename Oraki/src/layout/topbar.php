<?php
require_once __DIR__ . '/../services/MoonService.php';

// Busca os dados da lua
$moonData = MoonService::getCurrentPhaseData();

// Garante variáveis do usuário (fallbacks)
$displayName = isset($_SESSION['user_name']) ? explode(' ', $_SESSION['user_name'])[0] : 'Visitante';
$displayInitial = substr($displayName, 0, 1);
?>

<header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8 bg-white/50 backdrop-blur-sm p-6 rounded-2xl border border-white/40 shadow-sm">
    
    <div>
        <h1 class="text-2xl font-bold text-astral font-serif tracking-tight">
            Bem-vinda, <?= htmlspecialchars($displayName) ?>! 🌙
        </h1>
        <p class="text-gray-500 text-sm mt-1 flex items-center gap-2">
            <i data-lucide="sparkles" size="14" class="text-summer"></i>
            Vamos deixar a gestão do seu negócio em ordem!
        </p>
    </div>

    <div class="flex items-center gap-6 w-full md:w-auto">
        
        <div class="flex-1 md:flex-none bg-astral text-milk p-3 pr-5 rounded-xl flex items-center gap-4 shadow-lg shadow-crowberry/20 border border-crowberry relative overflow-hidden group transition-all hover:scale-[1.02]">
            <div class="absolute -left-2 -top-2 w-12 h-12 bg-galactic blur-xl opacity-50"></div>
            
            <div class="relative z-10 bg-white/10 p-2 rounded-full text-summer">
                <i data-lucide="<?= $moonData['icon'] ?>"></i>
            </div>
            
            <div class="relative z-10">
                <p class="text-[10px] uppercase tracking-widest text-palm font-bold mb-0.5">
                    Lua <?= $moonData['phase'] ?>
                </p>
                <p class="text-xs text-gray-300 leading-tight max-w-[200px]">
                    Essas são as energias para o dia: <span class="text-white font-medium"><?= $moonData['advice'] ?></span>
                </p>
            </div>
        </div>

        <a href="profile.php" class="relative group" title="Ir para meu perfil">
            <div class="absolute -inset-1 bg-gradient-to-r from-summer to-galactic rounded-full blur opacity-25 group-hover:opacity-75 transition duration-500"></div>
            
            <div class="relative h-12 w-12 bg-galactic text-white rounded-full flex items-center justify-center font-bold text-lg shadow-xl ring-2 ring-white cursor-pointer transform transition group-hover:scale-105">
                <?= $displayInitial ?>
            </div>
            
            <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full ring-2 ring-white bg-green-400"></span>
        </a>

    </div>
</header>