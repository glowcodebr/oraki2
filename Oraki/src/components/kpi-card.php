<?php
/**
 * Props esperadas:
 * $kpiTitle: Título do card (ex: Faturamento)
 * $kpiValue: Valor (ex: R$ 2.4k)
 * $kpiIcon: Nome do ícone Lucide
 * $isHighlight: (bool) Se true, usa o estilo escuro/premium
 */

$isHighlight = isset($isHighlight) && $isHighlight;
?>

<?php if ($isHighlight): ?>
    <div class="bg-astral p-6 rounded-2xl shadow-md flex items-center justify-between text-white relative overflow-hidden">
        <div class="absolute -right-6 -top-6 h-24 w-24 bg-crowberry rounded-full opacity-50 blur-xl"></div>
        <div class="relative z-10">
            <p class="text-palm text-xs font-bold uppercase tracking-wider"><?= $kpiTitle ?></p>
            <h3 class="text-3xl font-bold text-white"><?= $kpiValue ?></h3>
        </div>
        <div class="p-3 bg-white/10 text-summer rounded-full relative z-10 border border-white/20">
            <i data-lucide="<?= $kpiIcon ?>"></i>
        </div>
    </div>
<?php else: ?>
    <div class="bg-white p-6 rounded-2xl shadow-sm border-l-4 border-galactic flex items-center justify-between">
        <div>
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider"><?= $kpiTitle ?></p>
            <h3 class="text-3xl font-bold text-astral"><?= $kpiValue ?></h3>
        </div>
        <div class="p-3 bg-indigo-50 text-galactic rounded-full">
            <i data-lucide="<?= $kpiIcon ?>"></i>
        </div>
    </div>
<?php endif; ?>