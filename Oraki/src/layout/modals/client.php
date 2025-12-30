<div id="modal-client" class="hidden fixed inset-0 z-50 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 scale-95">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl pointer-events-auto overflow-hidden">
        
        <div class="bg-white p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 id="modal-client-title" class="font-bold text-astral flex items-center gap-2">
                <i data-lucide="user-plus" class="text-galactic"></i> Novo Cliente
            </h3>
            <button onclick="closeModal('modal-client')" class="text-gray-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>

        <div class="p-6">
            <form id="form-client" action="actions/create_client.php" method="POST" class="space-y-4">
                
                <input type="hidden" name="id" id="input-client-id">

                <div id="field-status-container" class="hidden bg-gray-50 p-3 rounded-xl border border-gray-200">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Status do Cliente</label>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="active" id="status-active" class="accent-galactic w-4 h-4">
                            <span class="text-sm font-medium text-astral">✨ Ativo</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="status" value="inactive" id="status-inactive" class="accent-gray-500 w-4 h-4">
                            <span class="text-sm font-medium text-gray-500">💤 Inativo</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Nome Completo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="input-client-name" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic placeholder-gray-400" placeholder="Ex: Maria Silva">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Nascimento</label>
                        <input type="date" name="birth_date" id="input-client-birth" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Telefone</label>
                        <input type="tel" name="phone" id="input-client-phone" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="(00) 00000-0000">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Origem do Contato</label>
                    <select name="source" id="input-client-source" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic">
                        <option value="instagram">Instagram</option>
                        <option value="whatsapp">WhatsApp</option>
                        <option value="site">Site/Google</option>
                        <option value="indication">Indicação</option>
                        <option value="other">Outro</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Anotações</label>
                    <textarea name="notes" id="input-client-notes" rows="2" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="Preferências, histórico..."></textarea>
                </div>

                <button type="submit" id="btn-save-client" class="w-full bg-astral text-white font-bold py-3 rounded-xl shadow-md hover:bg-crowberry transition-all">Salvar Cliente</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openNewClientModal() {
        document.getElementById('form-client').reset();
        document.getElementById('form-client').action = 'actions/create_client.php';
        document.getElementById('input-client-id').value = '';
        
        // ESCONDE O STATUS (Cliente novo nasce ativo)
        document.getElementById('field-status-container').classList.add('hidden');
        document.getElementById('status-active').checked = true; 
        
        document.getElementById('modal-client-title').innerHTML = '<i data-lucide="user-plus" class="text-galactic"></i> Novo Cliente';
        document.getElementById('btn-save-client').innerText = 'Salvar Cliente';
        
        openModal('modal-client');
    }

    async function openEditClient(clientId) {
        openModal('modal-client');
        document.getElementById('modal-client-title').innerHTML = '<i data-lucide="pencil" class="text-galactic"></i> Editando...';
        
        try {
            const response = await fetch(`actions/get_client_data.php?id=${clientId}`);
            const data = await response.json();
            
            if(data.error) throw new Error(data.error);

            const p = data.profile;

            document.getElementById('input-client-id').value = p.id;
            document.getElementById('input-client-name').value = p.name;
            document.getElementById('input-client-birth').value = p.birth_date;
            document.getElementById('input-client-phone').value = p.phone;
            document.getElementById('input-client-source').value = p.email;
            document.getElementById('input-client-notes').value = p.notes;

            // MOSTRA E DEFINE O STATUS
            document.getElementById('field-status-container').classList.remove('hidden');
            if (p.status === 'inactive') {
                document.getElementById('status-inactive').checked = true;
            } else {
                document.getElementById('status-active').checked = true;
            }

            document.getElementById('form-client').action = 'actions/update_client.php';
            document.getElementById('btn-save-client').innerText = 'Atualizar Dados';
            
            lucide.createIcons();

        } catch (error) {
            alert('Erro ao carregar dados para edição.');
            closeModal('modal-client');
        }
    }
</script>