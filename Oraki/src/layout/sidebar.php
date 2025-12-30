<?php
// Lógica para determinar qual menu está ativo
// $activePage deve ser definido na página pai (ex: dashboard.php)
$currentPair = isset($active) ? explode('.', $active) : ['dashboard']; 
$mainSection = $currentPair[0]; // Ex: 'clients'
$subSection  = $currentPair[1] ?? ''; // Ex: 'list'

// Definição da Estrutura do Menu (Data-Driven)
$menuStructure = [
    'dashboard' => [
        'label' => 'Início',
        'icon'  => 'layout-dashboard',
        'link'  => 'dashboard.php',
        'submenu' => []
    ],
    'clients' => [
        'label' => 'Cliente',
        'icon'  => 'users',
        'link'  => '#',
        'submenu' => [
            ['label' => 'Lista Completa', 'link' => 'clients_list.php', 'id' => 'list'],
            ['label' => 'Inativos',       'link' => 'clients_inactive.php', 'id' => 'inactive'],
            ['label' => 'Cadastrar',      'link' => 'clients_create.php', 'id' => 'create'],
        ]
    ],
    'agenda' => [
        'label' => 'Agenda',
        'icon'  => 'calendar',
        'link'  => '#',
        'submenu' => [
            ['label' => 'Solicitações',     'link' => 'agenda_requests.php', 'id' => 'requests'],
            ['label' => 'Confirmada',       'link' => 'agenda_confirmed.php', 'id' => 'confirmed'],
            ['label' => 'Agenda Pessoal',   'link' => 'agenda_personal.php', 'id' => 'personal'],
        ]
    ],
    'management' => [
        'label' => 'Gestão',
        'icon'  => 'briefcase', // ou pie-chart
        'link'  => '#',
        'submenu' => [
            ['label' => 'Financeiro',        'link' => 'finance.php', 'id' => 'finance'],
            ['label' => 'Relatórios Anuais', 'link' => 'reports.php', 'id' => 'reports'],
            ['label' => 'Serviços',          'link' => 'services.php', 'id' => 'services'],
        ]
    ],
    'expansion' => [
        'label' => 'Expansão',
        'icon'  => 'rocket',
        'link'  => '#',
        'submenu' => [
            ['label' => 'Fidelidade', 'link' => 'loyalty.php', 'id' => 'loyalty'],
            ['label' => 'Links (Bio)', 'link' => 'bio_links.php', 'id' => 'links'],
            ['label' => 'Marketing',  'link' => 'marketing.php', 'id' => 'marketing'],
        ]
    ],
];
?>

<nav class="bg-astral text-white flex flex-col md:w-64 md:h-full shadow-2xl z-20 transition-all duration-300 overflow-y-auto custom-scrollbar">
    
    <div class="p-6 pb-2">
        <h1 class="font-serif text-3xl font-bold tracking-tighter text-milk">
            Oraki<span class="text-summer">.</span>
        </h1>
        <p class="text-xs text-gray-400 mt-1 uppercase tracking-widest">Gestão Holística</p>
    </div>

    <div class="h-px bg-gradient-to-r from-transparent via-crowberry to-transparent my-4"></div>
    
    <div class="flex-1 px-3 space-y-1">
        
        <?php foreach ($menuStructure as $key => $item): ?>
            <?php 
                $isActive = ($mainSection === $key);
                $hasSubmenu = !empty($item['submenu']);
            ?>

            <div class="group">
                <?php if (!$hasSubmenu): ?>
                    <a href="<?= $item['link'] ?>" 
                       class="flex items-center gap-3 p-3 rounded-xl transition-all duration-200 
                       <?= $isActive ? 'bg-crowberry text-white shadow-lg border border-white/5' : 'text-gray-400 hover:bg-white/5 hover:text-milk' ?>">
                        <i data-lucide="<?= $item['icon'] ?>" size="20" class="<?= $isActive ? 'text-summer' : 'text-gray-500 group-hover:text-white' ?>"></i> 
                        <span class="font-medium text-sm"><?= $item['label'] ?></span>
                    </a>
                <?php else: ?>
                    <button onclick="toggleMenu('submenu-<?= $key ?>')" 
                            class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 cursor-pointer
                            <?= $isActive ? 'bg-crowberry/50 text-white' : 'text-gray-400 hover:bg-white/5 hover:text-milk' ?>">
                        <div class="flex items-center gap-3">
                            <i data-lucide="<?= $item['icon'] ?>" size="20" class="<?= $isActive ? 'text-summer' : 'text-gray-500 group-hover:text-white' ?>"></i>
                            <span class="font-medium text-sm"><?= $item['label'] ?></span>
                        </div>
                        <i data-lucide="chevron-down" size="16" class="transition-transform duration-300 <?= $isActive ? 'rotate-180' : '' ?>" id="chevron-<?= $key ?>"></i>
                    </button>

                    <div id="submenu-<?= $key ?>" class="<?= $isActive ? 'block' : 'hidden' ?> pl-10 pr-2 space-y-1 mt-1 mb-2 overflow-hidden transition-all">
                        <?php foreach ($item['submenu'] as $subItem): ?>
                            <?php $isSubActive = ($subSection === $subItem['id']); ?>
                            <a href="<?= $subItem['link'] ?>" 
                               class="block py-2 px-3 text-sm rounded-lg transition-colors border-l-2 
                               <?= $isSubActive ? 'border-summer text-summer bg-summer/10' : 'border-transparent text-gray-500 hover:text-milk hover:bg-white/5' ?>">
                                <?= $subItem['label'] ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="p-4 mt-auto border-t border-crowberry bg-astral/50">
        <a href="settings.php" class="flex items-center gap-3 p-3 text-gray-400 hover:text-milk hover:bg-white/5 rounded-xl transition-all mb-1">
            <i data-lucide="settings" size="18"></i>
            <span class="font-medium text-sm">Configurações</span>
        </a>
        
        <form action="auth_action.php" method="POST">
            <input type="hidden" name="action" value="logout">
            <button type="submit" class="w-full flex items-center gap-3 p-3 text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-xl transition-all">
                <i data-lucide="log-out" size="18"></i>
                <span class="font-medium text-sm">Sair da Conta</span>
            </button>
        </form>
    </div>

</nav>

<script>
    function toggleMenu(menuId) {
        const menu = document.getElementById(menuId);
        const chevronId = menuId.replace('submenu-', 'chevron-');
        const chevron = document.getElementById(chevronId);
        
        // Toggle da visibilidade
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            if(chevron) chevron.style.transform = 'rotate(180deg)';
        } else {
            menu.classList.add('hidden');
            if(chevron) chevron.style.transform = 'rotate(0deg)';
        }
    }
</script>

<style>
    /* Personalização da Barra de Rolagem (Fina e Discreta) */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #220055; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #472E97; }
</style>