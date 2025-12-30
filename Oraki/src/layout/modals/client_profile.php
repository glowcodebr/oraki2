<div id="modal-client-profile" class="hidden fixed inset-0 z-50 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 scale-95">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl pointer-events-auto overflow-hidden flex flex-col max-h-[90vh]">
        
        <div class="bg-astral p-6 text-white relative">
            <button onclick="closeModal('modal-client-profile')" class="absolute top-4 right-4 text-gray-400 hover:text-white"><i data-lucide="x"></i></button>
            
            <div id="profile-loading" class="animate-pulse flex gap-4">
                <div class="h-16 w-16 bg-white/20 rounded-full"></div>
                <div class="space-y-2 pt-2">
                    <div class="h-4 w-40 bg-white/20 rounded"></div>
                    <div class="h-3 w-20 bg-white/20 rounded"></div>
                </div>
            </div>

            <div id="profile-header-content" class="hidden flex items-center gap-4">
                <div class="h-16 w-16 bg-summer text-astral rounded-full flex items-center justify-center font-bold text-2xl shadow-lg border-2 border-white" id="cp-avatar">
                    A
                </div>
                <div>
                    <h2 class="text-2xl font-bold" id="cp-name">Carregando...</h2>
                    <p class="text-sm text-gray-300 flex items-center gap-2">
                        <i data-lucide="star" size="12" class="text-summer"></i>
                        <span id="cp-age">-- anos</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="flex border-b border-gray-100">
            <button onclick="switchTab('info')" id="tab-btn-info" class="flex-1 py-4 text-sm font-bold text-galactic border-b-2 border-galactic bg-indigo-50/50 transition-colors">
                Informações
            </button>
            <button onclick="switchTab('journey')" id="tab-btn-journey" class="flex-1 py-4 text-sm font-bold text-gray-400 hover:text-astral transition-colors">
                Jornada
            </button>
            <button onclick="switchTab('invest')" id="tab-btn-invest" class="flex-1 py-4 text-sm font-bold text-gray-400 hover:text-astral transition-colors">
                Investimentos
            </button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-milk">
            
            <div id="tab-content-info" class="space-y-4 animate-fade-in">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white p-4 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Telefone</p>
                        <p class="text-astral font-medium" id="cp-phone">--</p>
                    </div>
                    <div class="bg-white p-4 rounded-xl border border-gray-100">
                        <p class="text-xs text-gray-400 uppercase font-bold mb-1">Nascimento</p>
                        <p class="text-astral font-medium" id="cp-birth">--</p>
                    </div>
                </div>
                
                <div class="bg-white p-4 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Origem do Contato</p>
                    <div class="flex items-center gap-2 text-astral font-medium">
                        <i data-lucide="map-pin" size="16" class="text-summer"></i>
                        <span id="cp-source">--</span>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-xl border border-gray-100">
                    <p class="text-xs text-gray-400 uppercase font-bold mb-1">Anotações Gerais</p>
                    <p class="text-sm text-gray-600 italic mt-1" id="cp-notes">Nenhuma anotação.</p>
                </div>
            </div>

            <div id="tab-content-journey" class="hidden space-y-4 animate-fade-in">
                <div class="relative pl-4 border-l-2 border-gray-200 space-y-6" id="cp-journey-list">
                    <p class="text-gray-400 text-sm">Carregando histórico...</p>
                </div>
            </div>

            <div id="tab-content-invest" class="hidden animate-fade-in">
                <div class="grid grid-cols-1 gap-4">
                    <div class="bg-astral p-6 rounded-2xl text-white relative overflow-hidden">
                         <div class="absolute right-0 top-0 w-24 h-24 bg-summer blur-[50px] opacity-20"></div>
                         <p class="text-sm text-gray-300">Total Investido</p>
                         <h3 class="text-3xl font-bold mt-1" id="cp-total-spent">R$ 0,00</h3>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-400 uppercase font-bold">Atendimentos</p>
                            <h4 class="text-xl font-bold text-astral mt-1" id="cp-total-apps">0</h4>
                        </div>
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <p class="text-xs text-gray-400 uppercase font-bold">Ticket Médio</p>
                            <h4 class="text-xl font-bold text-emerald-600 mt-1" id="cp-avg-ticket">R$ 0</h4>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Lógica para abrir o perfil e buscar dados
    async function openClientProfile(clientId) {
        openModal('modal-client-profile');
        
        // Reset visual (Loading state)
        document.getElementById('profile-loading').classList.remove('hidden');
        document.getElementById('profile-header-content').classList.add('hidden');
        switchTab('info'); // Volta para a primeira aba

        try {
            // Chama a API
            const response = await fetch(`actions/get_client_data.php?id=${clientId}`);
            const data = await response.json();

            if (data.error) throw new Error(data.error);

            // 1. Preenche Header
            const p = data.profile;
            document.getElementById('cp-name').innerText = p.name;
            document.getElementById('cp-avatar').innerText = p.name.charAt(0);
            document.getElementById('cp-age').innerText = p.age ? `${p.age} anos` : 'Idade n/a';
            
            // 2. Preenche Info Tab
            document.getElementById('cp-phone').innerText = p.phone || '---';
            document.getElementById('cp-birth').innerText = p.birth_date ? new Date(p.birth_date).toLocaleDateString('pt-BR') : '---';
            document.getElementById('cp-source').innerText = p.email || 'Não informado'; // Usando email como source temp
            document.getElementById('cp-notes').innerText = p.notes || 'Nenhuma anotação.';

            // 3. Preenche Jornada (Loop)
            const list = document.getElementById('cp-journey-list');
            list.innerHTML = ''; // Limpa
            
            // Loop dos Atendimentos
            if (data.journey.length > 0) {
                data.journey.forEach(app => {
                    const date = new Date(app.start_time).toLocaleDateString('pt-BR');
                    const time = new Date(app.start_time).toLocaleTimeString('pt-BR', {hour: '2-digit', minute:'2-digit'});
                    const statusColor = app.status === 'completed' ? 'bg-green-500' : 'bg-blue-400';
                    const item = `
                        <div class="relative pb-6 border-l-2 border-gray-100 last:border-0 ml-2 pl-6">
                            <span class="absolute -left-[9px] top-1 h-4 w-4 rounded-full ${statusColor} ring-4 ring-white"></span>
                            <p class="text-sm font-bold text-astral">${app.service_name}</p>
                            <p class="text-xs text-gray-500">${date} às ${time} • ${app.status === 'completed' ? 'Concluído' : 'Agendado'}</p>
                            ${app.notes ? `<p class="text-xs text-gray-400 italic mt-1 bg-gray-50 p-2 rounded">"${app.notes}"</p>` : ''}
                        </div>
                    `;
                    list.insertAdjacentHTML('beforeend', item);
                });
            }

            // --- NOVO: MARCO ZERO (DATA DE CADASTRO) ---
            if (p.created_at) {
                const regDate = new Date(p.created_at).toLocaleDateString('pt-BR');
                const regItem = `
                    <div class="relative ml-2 pl-6 pt-2">
                        <span class="absolute -left-[9px] top-1 h-4 w-4 bg-galactic rounded-full ring-4 ring-white flex items-center justify-center">
                            <i data-lucide="flag" size="8" class="text-white"></i>
                        </span>
                        <p class="text-sm font-bold text-gray-400">Início da Jornada</p>
                        <p class="text-xs text-gray-400">Cliente cadastrada em ${regDate}</p>
                    </div>
                `;
                list.insertAdjacentHTML('beforeend', regItem);
            }
            
            // Atualiza ícones (para o ícone da bandeira aparecer)
            lucide.createIcons();
            
            // 4. Preenche Investimentos
            const inv = data.investments;
            const formatter = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
            document.getElementById('cp-total-spent').innerText = formatter.format(inv.total_spent);
            document.getElementById('cp-total-apps').innerText = inv.total_apps;
            document.getElementById('cp-avg-ticket').innerText = formatter.format(inv.avg_ticket);

            // Mostra conteúdo final
            document.getElementById('profile-loading').classList.add('hidden');
            document.getElementById('profile-header-content').classList.remove('hidden');

        } catch (error) {
            console.error(error);
            alert('Erro ao carregar dados do cliente.');
            closeModal('modal-client-profile');
        }
    }

    // Lógica das Abas
    function switchTab(tabName) {
        // Esconde todos
        ['info', 'journey', 'invest'].forEach(t => {
            document.getElementById(`tab-content-${t}`).classList.add('hidden');
            const btn = document.getElementById(`tab-btn-${t}`);
            btn.classList.remove('text-galactic', 'border-b-2', 'border-galactic', 'bg-indigo-50/50');
            btn.classList.add('text-gray-400');
        });

        // Mostra o atual
        document.getElementById(`tab-content-${tabName}`).classList.remove('hidden');
        const activeBtn = document.getElementById(`tab-btn-${tabName}`);
        activeBtn.classList.remove('text-gray-400');
        activeBtn.classList.add('text-galactic', 'border-b-2', 'border-galactic', 'bg-indigo-50/50');
    }
</script>