<?php
require_once 'src/layout/auth_guard.php';
require_once 'src/services/ClientService.php';

// Verifica o modo de visualização
$viewType = $_GET['view'] ?? 'active';
$isInactiveView = ($viewType === 'inactive');

$pageTitle = $isInactiveView ? 'Campanha de Retorno' : 'Base de Clientes';
$active = $isInactiveView ? 'clients.inactive' : 'clients.list';

$clientService = new ClientService();
$clients = $clientService->getClientsByStatus($currentUserId, $viewType);

$title = "$pageTitle - Oraki";
include 'src/layout/head.php';
include 'src/layout/sidebar.php';
?>

<main class="flex-1 overflow-y-auto p-4 md:p-8 custom-scrollbar bg-milk">
    
    <?php include 'src/layout/topbar.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <div id="alert-box" class="mb-6 bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm animate-bounce-in">
            <i data-lucide="check-circle"></i> <span>Ação realizada com sucesso!</span>
            <button onclick="document.getElementById('alert-box').remove()" class="ml-auto"><i data-lucide="x" size="16"></i></button>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-astral flex items-center gap-2">
                <?php if($isInactiveView): ?>
                    <i data-lucide="sparkles" class="text-summer"></i> Campanha de Retorno
                <?php else: ?>
                    <i data-lucide="users" class="text-galactic"></i> Lista de Clientes
                <?php endif; ?>
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                <?= count($clients) ?> almas encontradas.
            </p>
        </div>

        <?php if(!$isInactiveView): ?>
            <button onclick="openNewClientModal()" class="bg-galactic text-white px-6 py-3 rounded-xl shadow-lg hover:bg-crowberry transition-all flex items-center gap-2 font-bold">
                <i data-lucide="user-plus"></i> Novo Cliente
            </button>
        <?php endif; ?>
    </div>

    <?php if (!$isInactiveView): ?>
        
        <?php if (empty($clients)): ?>
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-gray-200">
                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i data-lucide="ghost" size="32"></i>
                </div>
                <p class="text-gray-500 font-medium">Nenhum cliente ativo encontrado.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach($clients as $client): ?>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:shadow-md transition-all group relative">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="h-14 w-14 bg-gradient-to-br from-indigo-50 to-purple-50 text-galactic rounded-full flex items-center justify-center font-bold text-xl border border-indigo-100">
                                <?= substr($client['name'], 0, 1) ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-astral text-lg truncate w-40"><?= htmlspecialchars($client['name']) ?></h3>
                                <span class="text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700">Ativo</span>
                            </div>
                        </div>
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center gap-2 text-sm text-gray-500"><i data-lucide="phone" size="14"></i> <?= $client['phone'] ?: 'Sem telefone' ?></div>
                            <div class="flex items-center gap-2 text-sm text-gray-500"><i data-lucide="cake" size="14"></i> <?= $client['birth_date'] ? date('d/m/Y', strtotime($client['birth_date'])) : '--/--/----' ?></div>
                        </div>
                        <div class="flex gap-2 mt-auto">
                            <button onclick="openClientProfile(<?= $client['id'] ?>)" class="flex-1 bg-astral text-white text-sm font-bold py-2 rounded-lg hover:bg-crowberry transition-colors">Ver Perfil</button>
                            <button onclick="openEditClient(<?= $client['id'] ?>)" class="p-2 text-gray-400 hover:text-galactic border border-gray-200 rounded-lg transition-colors"><i data-lucide="pencil" size="16"></i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <div class="flex gap-6 border-b border-gray-200 mb-6">
            <button onclick="switchCampaignTab('list')" id="tab-camp-list" class="pb-3 text-sm font-bold border-b-2 border-galactic text-galactic transition-colors flex items-center gap-2">
                <i data-lucide="list"></i> Lista de Sumidos
            </button>
            <button onclick="switchCampaignTab('editor')" id="tab-camp-editor" class="pb-3 text-sm font-bold border-b-2 border-transparent text-gray-400 hover:text-astral transition-colors flex items-center gap-2">
                <i data-lucide="message-square-plus"></i> Editor de Mensagem
            </button>
        </div>

        <div id="content-camp-list" class="animate-fade-in">
            <?php if (empty($clients)): ?>
                <div class="bg-green-50 p-8 rounded-2xl border border-green-100 text-center">
                    <h3 class="text-green-800 font-bold text-lg">Parabéns!</h3>
                    <p class="text-green-600">Todos os seus clientes estão ativos e radiantes.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($clients as $client): ?>
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex flex-col relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 <?= $client['days_since_last_app'] > 90 ? 'bg-red-400' : 'bg-orange-400' ?>"></div>
                            
                            <div class="ml-2 flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="font-bold text-astral"><?= htmlspecialchars($client['name']) ?></h3>
                                    <p class="text-xs text-gray-400">Última visita: <?= $client['last_appointment_date'] ? date('d/m/Y', strtotime($client['last_appointment_date'])) : 'Nunca' ?></p>
                                </div>
                                <div class="bg-gray-100 px-2 py-1 rounded text-xs font-bold text-gray-600">
                                    <?= $client['days_since_last_app'] ? $client['days_since_last_app'] . ' dias' : 'N/D' ?>
                                </div>
                            </div>

                            <button onclick="sendWhatsappTo(<?= $client['id'] ?>, '<?= $client['name'] ?>', '<?= $client['phone'] ?>')" 
                                    class="mt-auto w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded-lg flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-lg shadow-green-200">
                                <i data-lucide="send" size="16"></i> Chamar no Zap
                            </button>
                            
                            <div class="mt-2 text-center">
                                <button onclick="openEditClient(<?= $client['id'] ?>)" class="text-xs text-gray-400 hover:text-galactic underline">Editar Cadastro</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="content-camp-editor" class="hidden animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100">
                    <h3 class="font-bold text-astral mb-4 flex items-center gap-2"><i data-lucide="edit-3"></i> Criar Mensagem Mágica</h3>
                    
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Variáveis e Emojis</label>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="insertText('{nome}')" class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-100 border border-indigo-200">{nome}</button>
                            <button onclick="insertText('🔮')" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">🔮</button>
                            <button onclick="insertText('✨')" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">✨</button>
                            <button onclick="insertText('🌙')" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">🌙</button>
                            <button onclick="insertText('🌻')" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">🌻</button>
                            <button onclick="insertText('🕯️')" class="px-3 py-1 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200">🕯️</button>
                        </div>
                    </div>

                    <textarea id="message-template" rows="8" 
                              class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic text-gray-700 text-sm leading-relaxed resize-none"
                              placeholder="Olá {nome}, tudo bem? As cartas sentiram sua falta..."
                              oninput="updatePreview()"></textarea>
                    
                    <p class="text-xs text-gray-400 mt-2 italic">Dica: O texto é salvo automaticamente no seu navegador.</p>
                </div>

                <div class="flex justify-center">
                    <div class="w-[320px] bg-[#ECE5DD] rounded-[30px] border-8 border-gray-800 shadow-2xl overflow-hidden relative h-[500px] flex flex-col">
                        <div class="bg-[#075E54] p-3 flex items-center gap-2 text-white shrink-0">
                            <div class="w-8 h-8 bg-gray-300 rounded-full overflow-hidden"><img src="https://ui-avatars.com/api/?name=Cliente&background=random" alt=""></div>
                            <div>
                                <p class="text-sm font-bold">Cliente</p>
                                <p class="text-[10px] opacity-80">visto por último hoje às 10:00</p>
                            </div>
                        </div>

                        <div class="flex-1 p-4 overflow-y-auto" style="background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');">
                            <div class="bg-[#DCF8C6] p-3 rounded-lg rounded-tr-none shadow-sm max-w-[90%] ml-auto relative">
                                <p class="text-xs text-gray-800 whitespace-pre-wrap" id="preview-text">Sua mensagem aparecerá aqui...</p>
                                <div class="text-[9px] text-gray-500 text-right mt-1 flex justify-end gap-1 items-center">
                                    18:30 <i data-lucide="check-check" size="10" class="text-blue-500"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-2 flex items-center gap-2 shrink-0">
                            <div class="w-6 h-6 rounded-full border border-gray-400"></div>
                            <div class="flex-1 h-8 bg-gray-100 rounded-full"></div>
                            <div class="w-6 h-6 rounded-full bg-[#075E54]"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    <?php endif; ?>

</main>

<?php include 'src/layout/modals/loader.php'; ?>
<?php include 'src/layout/modals/client.php'; ?>
<?php include 'src/layout/modals/client_profile.php'; ?>
<?php include 'src/layout/mobile-nav.php'; ?>

<script>
    lucide.createIcons();

    // --- LÓGICA DE TABS DA CAMPANHA ---
    function switchCampaignTab(tab) {
        const contentList = document.getElementById('content-camp-list');
        const contentEditor = document.getElementById('content-camp-editor');
        const btnList = document.getElementById('tab-camp-list');
        const btnEditor = document.getElementById('tab-camp-editor');

        if (tab === 'list') {
            contentList.classList.remove('hidden');
            contentEditor.classList.add('hidden');
            
            btnList.classList.replace('border-transparent', 'border-galactic');
            btnList.classList.replace('text-gray-400', 'text-galactic');
            
            btnEditor.classList.replace('border-galactic', 'border-transparent');
            btnEditor.classList.replace('text-galactic', 'text-gray-400');
        } else {
            contentList.classList.add('hidden');
            contentEditor.classList.remove('hidden');

            btnEditor.classList.replace('border-transparent', 'border-galactic');
            btnEditor.classList.replace('text-gray-400', 'text-galactic');

            btnList.classList.replace('border-galactic', 'border-transparent');
            btnList.classList.replace('text-galactic', 'text-gray-400');
        }
    }

    // --- LÓGICA DO EDITOR DE MENSAGEM ---
    
    // 1. Carregar mensagem salva ao abrir
    document.addEventListener('DOMContentLoaded', () => {
        const savedMsg = localStorage.getItem('oraki_recall_msg');
        if (savedMsg) {
            document.getElementById('message-template').value = savedMsg;
            updatePreview();
        }
    });

    // 2. Atualizar Preview e Salvar
    function updatePreview() {
        const text = document.getElementById('message-template').value;
        const preview = document.getElementById('preview-text');
        
        // Simula a troca da variável {nome} para visualização
        let displayText = text.replace(/{nome}/g, "Maria");
        
        if (!displayText) displayText = "Sua mensagem aparecerá aqui...";
        
        preview.innerText = displayText;
        
        // Salva no navegador
        localStorage.setItem('oraki_recall_msg', text);
    }

    // 3. Inserir Variáveis/Emojis
    function insertText(text) {
        const textarea = document.getElementById('message-template');
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;

        const currentValue = textarea.value;
        const newValue = currentValue.substring(0, start) + text + currentValue.substring(end);
        
        textarea.value = newValue;
        textarea.focus();
        textarea.selectionStart = textarea.selectionEnd = start + text.length;
        
        updatePreview();
    }

    // --- LÓGICA DE ENVIO (ZAP) ---
    function sendWhatsappTo(id, name, phone) {
        let msg = localStorage.getItem('oraki_recall_msg');
        
        if (!msg) {
            alert("Por favor, crie uma mensagem na aba 'Editor' primeiro! 🔮");
            switchCampaignTab('editor');
            return;
        }

        // Limpa telefone (deixa só numeros)
        const cleanPhone = phone.replace(/\D/g, '');
        
        if (cleanPhone.length < 10) {
            alert("O telefone deste cliente parece inválido. Verifique o cadastro.");
            return;
        }

        // Personaliza a mensagem
        // Pega apenas o primeiro nome para ficar mais íntimo
        const firstName = name.split(' ')[0];
        const finalMsg = msg.replace(/{nome}/g, firstName);
        
        // Codifica para URL
        const encodedMsg = encodeURIComponent(finalMsg);
        
        // Abre WhatsApp
        window.open(`https://wa.me/55${cleanPhone}?text=${encodedMsg}`, '_blank');
    }
</script>
</body>
</html>