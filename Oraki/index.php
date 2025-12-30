<?php
// 1. Segurança e Configurações
require_once 'src/layout/auth_guard.php'; 
require_once 'src/services/DashboardStats.php';

// 2. Instância do Serviço
$statsService = new DashboardStats();

// 3. Busca de Dados Gerais
// Garantimos que o $currentUserId venha da sessão (auth_guard já cuida disso, mas reforçamos)
$userId = $_SESSION['user_id']; 

$kpis = $statsService->getYearlyStats($userId);
$birthdays = $statsService->getBirthdaysMonth($userId);
$recentApps = $statsService->getRecentAppointments($userId);
$notes = $statsService->getDailyNotes($userId);

// 4. Configuração de Data
$currentMonth = date('n'); 
$currentYear = date('Y');
$meses = [1=>'Janeiro', 2=>'Fevereiro', 3=>'Março', 4=>'Abril', 5=>'Maio', 6=>'Junho', 7=>'Julho', 8=>'Agosto', 9=>'Setembro', 10=>'Outubro', 11=>'Novembro', 12=>'Dezembro'];

// --- CORREÇÃO DO GRÁFICO ---
// Buscamos os dados do gráfico e convertemos para JSON aqui no PHP
try {
    $chartDataArray = $statsService->getMonthlyRevenueChart($userId, $currentYear);
} catch (Exception $e) {
    // Se der erro no banco, gera um gráfico zerado para não quebrar a tela
    $chartDataArray = array_fill(0, 12, 0);
}
$chartDataJSON = json_encode($chartDataArray);

// --- DADOS DO CALENDÁRIO ---
$calendarData = $statsService->getCalendarData($userId, $currentMonth, $currentYear);

// Cálculos para o Grid do Calendário
$firstDayOfMonth = mktime(0, 0, 0, $currentMonth, 1, $currentYear);
$dayOfWeek = date('w', $firstDayOfMonth); 

// Configurações da Página
$title = "Dashboard - Oraki";
$active = "dashboard";

// 5. Layout
include 'src/layout/head.php';
include 'src/layout/sidebar.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar bg-milk">
    
    <?php include 'src/layout/topbar.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <div id="alert-box" class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center justify-between shadow-sm animate-bounce-in">
            <div class="flex items-center gap-2">
                <i data-lucide="check-circle" class="text-green-500"></i>
                <span class="font-medium">
                    <?php 
                        if($_GET['success'] == 'cliente_criado') echo "Cliente cadastrada com sucesso!";
                        elseif($_GET['success'] == 'servico_criado') echo "Novo serviço adicionado ao portfólio!";
                        elseif($_GET['success'] == 'agendamento_criado') echo "Agendamento confirmado!";
                    ?>
                </span>
            </div>
            <button onclick="document.getElementById('alert-box').remove()" class="text-green-500 hover:text-green-800"><i data-lucide="x" size="18"></i></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div id="error-box" class="mb-6 bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <i data-lucide="alert-triangle"></i>
            <span>Ops! Algo deu errado. Tente novamente.</span>
        </div>
    <?php endif; ?>

    <section class="mb-8">
        <div class="flex gap-4 overflow-x-auto pb-4 md:pb-0 hide-scrollbar snap-x">
            
            <button onclick="openModal('modal-appointment')" class="snap-center shrink-0 flex items-center gap-2 bg-galactic text-white px-6 py-3 rounded-xl shadow-lg shadow-galactic/30 hover:bg-crowberry transition-all transform hover:-translate-y-1">
                <i data-lucide="calendar-plus"></i> <span class="font-bold whitespace-nowrap">Novo Agendamento</span>
            </button>

            <button onclick="openModal('modal-client')" class="snap-center shrink-0 flex items-center gap-2 bg-white text-astral border border-gray-200 px-6 py-3 rounded-xl hover:bg-gray-50 transition-all">
                <i data-lucide="user-plus"></i> <span class="font-medium whitespace-nowrap">Novo Cliente</span>
            </button>

            <button onclick="openModal('modal-service')" class="snap-center shrink-0 flex items-center gap-2 bg-white text-astral border border-gray-200 px-6 py-3 rounded-xl hover:bg-gray-50 transition-all">
                <i data-lucide="sparkles"></i> <span class="font-medium whitespace-nowrap">Novo Serviço</span>
            </button>
            
            <button class="snap-center shrink-0 flex items-center gap-2 bg-white text-astral border border-gray-200 px-6 py-3 rounded-xl hover:bg-gray-50 transition-all">
                <i data-lucide="heart"></i> <span class="font-medium whitespace-nowrap">Fidelidade</span>
            </button>

            <button class="snap-center shrink-0 flex items-center gap-2 bg-astral text-summer border border-crowberry px-6 py-3 rounded-xl shadow-md hover:brightness-110 transition-all">
                <i data-lucide="book-open"></i> <span class="font-bold whitespace-nowrap">Grimório</span>
            </button>

        </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Consultas (Ano)</p>
                <div class="p-2 bg-indigo-50 text-galactic rounded-lg"><i data-lucide="calendar"></i></div>
            </div>
            <h3 class="text-3xl font-bold text-astral"><?= $kpis['appointments_year'] ?></h3>
        </div>

        <div class="bg-astral p-5 rounded-2xl shadow-md border border-crowberry flex flex-col justify-between h-32 relative overflow-hidden group">
            <div class="absolute right-0 top-0 w-24 h-24 bg-summer blur-[60px] opacity-20 group-hover:opacity-30 transition"></div>
            <div class="flex justify-between items-start relative z-10">
                <p class="text-palm text-xs font-bold uppercase tracking-wider">Faturamento Anual</p>
                <div class="p-2 bg-white/10 text-summer rounded-lg"><i data-lucide="trending-up"></i></div>
            </div>
            <h3 class="text-3xl font-bold text-white relative z-10">R$ <?= number_format($kpis['revenue_year'], 2, ',', '.') ?></h3>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Base de Clientes</p>
                <div class="p-2 bg-purple-50 text-purple-600 rounded-lg"><i data-lucide="users"></i></div>
            </div>
            <h3 class="text-3xl font-bold text-astral"><?= $kpis['total_clients'] ?></h3>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex flex-col justify-between h-32">
            <div class="flex justify-between items-start">
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Ticket Médio</p>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-lg"><i data-lucide="banknote"></i></div>
            </div>
            <h3 class="text-3xl font-bold text-emerald-600">R$ <?= number_format($kpis['avg_ticket'], 2, ',', '.') ?></h3>
        </div>

    </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8 items-start pb-20 md:pb-0">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-[420px] overflow-hidden">
                <div class="flex justify-between items-center p-6 pb-2 shrink-0">
                    <h3 class="font-bold text-astral flex items-center gap-2">
                        <i data-lucide="bar-chart-3" size="18" class="text-galactic"></i> Ganhos do Ano
                    </h3>
                    <select class="text-xs bg-gray-50 border-none rounded-md text-gray-500 cursor-pointer outline-none">
                        <option><?= $currentYear ?></option>
                    </select>
                </div>
                <div class="flex-1 w-full relative px-4 pb-4">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                    <h3 class="font-bold text-astral mb-4 flex items-center gap-2">
                        <i data-lucide="history" size="18" class="text-gray-400"></i> Últimos Atendimentos
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-500">
                            <thead class="text-xs uppercase bg-gray-50 text-gray-400 font-bold">
                                <tr><th class="p-3 rounded-l-lg">Cliente</th><th>Serviço</th><th class="p-3 rounded-r-lg">Status</th></tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach($recentApps as $app): ?>
                                <tr>
                                    <td class="p-3 font-medium text-astral"><?= htmlspecialchars($app['client_name']) ?></td>
                                    <td class="p-3"><?= htmlspecialchars($app['service_name']) ?></td>
                                    <td class="p-3">
                                        <?php if($app['status'] == 'completed'): ?>
                                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Concluído</span>
                                        <?php else: ?>
                                            <span class="px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-700">Agendado</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 h-full">
                    <h3 class="font-bold text-astral mb-4 flex items-center gap-2">
                        <i data-lucide="cake" size="18" class="text-pink-500"></i> Aniversariantes
                    </h3>
                    
                    <?php if(empty($birthdays)): ?>
                        <div class="h-40 flex flex-col items-center justify-center text-gray-400 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-xs">Nenhum aniversariante.</p>
                        </div>
                    <?php else: ?>
                        <div class="space-y-3">
                            <?php foreach($birthdays as $bday): ?>
                            <div class="flex items-center gap-3 p-3 bg-pink-50/50 rounded-xl border border-pink-100 hover:bg-pink-50 transition">
                                <div class="h-8 w-8 bg-white text-pink-500 rounded-full flex items-center justify-center font-bold shadow-sm text-xs">
                                    <?= substr($bday['name'], 0, 1) ?>
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-astral text-sm truncate"><?= htmlspecialchars($bday['name']) ?></p>
                                    <p class="text-[10px] text-pink-500 font-medium">Dia <?= $bday['day'] ?></p>
                                </div>
                                <button class="p-1.5 text-gray-400 hover:text-green-500 transition"><i data-lucide="message-circle" size="14"></i></button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>

        <div class="space-y-6 flex flex-col">
            
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-astral capitalize"><?= $meses[$currentMonth] ?> <?= $currentYear ?></h3>
                    <div class="flex gap-1">
                        <button class="p-1 hover:bg-gray-100 rounded text-gray-400"><i data-lucide="chevron-left" size="16"></i></button>
                        <button class="p-1 hover:bg-gray-100 rounded text-gray-400"><i data-lucide="chevron-right" size="16"></i></button>
                    </div>
                </div>
                
                <div class="grid grid-cols-7 gap-1 text-center text-xs text-gray-400 font-medium mb-2">
                    <div>D</div><div>S</div><div>T</div><div>Q</div><div>Q</div><div>S</div><div>S</div>
                </div>

                <div class="grid grid-cols-7 gap-1 text-center text-sm font-medium text-astral">
                    <?php for($i = 0; $i < $dayOfWeek; $i++): ?>
                        <div class="p-2"></div>
                    <?php endfor; ?>

                    <?php foreach ($calendarData as $day => $data): ?>
                        <?php 
                            $classes = "hover:bg-gray-50 transition cursor-default";
                            if ($data['is_today']) $classes .= " border-2 border-galactic text-galactic font-bold";
                            
                            if ($data['status'] === 'blocked') $classes .= " bg-red-100 text-red-600";
                            elseif ($data['status'] === 'busy') $classes .= " bg-summer/20 text-astral font-bold";
                        ?>

                        <div class="p-2 rounded-lg relative group <?= $classes ?>">
                            <?= $day ?>
                            <?php if($data['info']): ?>
                                <span class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1 hidden group-hover:block bg-astral text-white text-[10px] px-2 py-1 rounded whitespace-nowrap z-10 shadow-lg">
                                    <?= htmlspecialchars($data['info']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-4 flex gap-3 text-[10px] text-gray-400 justify-center">
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-summer/50"></span> Agendado</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-300"></span> Bloqueado</div>
                    <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full border border-galactic"></span> Hoje</div>
                </div>
            </div>

            <div class="bg-astral text-milk p-6 rounded-2xl shadow-lg relative overflow-hidden flex flex-col h-[420px]">
                <div class="absolute top-0 right-0 p-4 opacity-10 pointer-events-none"><i data-lucide="list-todo" size="64"></i></div>
                
                <h3 class="font-bold mb-4 relative z-10 flex justify-between items-center shrink-0">
                    Anotações de Hoje
                    <span class="text-[10px] bg-white/10 px-2 py-1 rounded text-gray-300 font-normal">
                        <?= date('d/m') ?>
                    </span>
                </h3>
                
                <div class="space-y-3 relative z-10 flex-1 overflow-y-auto custom-scrollbar pr-2" id="todo-list">
                    <?php if (empty($notes)): ?>
                        <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-50" id="empty-msg">
                            <i data-lucide="feather" size="24" class="mb-2"></i>
                            <p class="text-xs italic">O dia é uma folha em branco.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($notes as $note): ?>
                            <div class="flex items-center gap-3 group animate-fade-in" id="note-<?= $note['id'] ?>">
                                <input type="checkbox" 
                                       onchange="toggleNote(<?= $note['id'] ?>, this)"
                                       class="shrink-0 cursor-pointer appearance-none w-5 h-5 border-2 border-gray-500 rounded checked:bg-summer checked:border-summer transition-all relative after:content-['✓'] after:absolute after:text-astral after:text-xs after:font-bold after:top-[1px] after:left-[3px] after:opacity-0 checked:after:opacity-100"
                                       <?= $note['is_completed'] ? 'checked' : '' ?>>
                                
                                <span class="text-sm flex-1 break-words transition-all <?= $note['is_completed'] ? 'line-through text-gray-500' : 'text-gray-200' ?>" id="text-<?= $note['id'] ?>">
                                    <?= htmlspecialchars($note['content']) ?>
                                </span>

                                <button onclick="deleteNote(<?= $note['id'] ?>)" class="opacity-0 group-hover:opacity-100 text-gray-500 hover:text-red-400 transition-opacity">
                                    <i data-lucide="trash-2" size="14"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="flex gap-2 mt-4 border-t border-white/10 pt-3 relative z-10 shrink-0">
                    <input type="text" id="new-note-input" 
                           placeholder="Nova tarefa mágica..." 
                           onkeypress="if(event.key === 'Enter') addNote()"
                           class="bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-xs w-full text-white placeholder-gray-500 focus:outline-none focus:border-summer focus:bg-white/10 transition-colors">
                    
                    <button onclick="addNote()" class="text-summer hover:text-white transition-transform active:scale-90">
                        <i data-lucide="plus-circle" size="24"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    
</main>

<?php include 'src/layout/modals/loader.php'; ?>

<script>
    lucide.createIcons();

    // Configuração do Gráfico (Chart.js)
    const ctx = document.getElementById('revenueChart').getContext('2d');
    
    // Gradiente bonito
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(71, 46, 151, 0.5)'); // Galactic Purple
    gradient.addColorStop(1, 'rgba(71, 46, 151, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
            datasets: [{
                label: 'Faturamento',
                
                // AQUI: Agora a variável JS recebe o valor correto do PHP
                data: <?= $chartDataJSON ?>, 
                
                borderColor: '#472E97', // Galactic Purple
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4, // Curva suave
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#472E97',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<?php include 'src/layout/mobile-nav.php'; ?>
</body>

</html>
