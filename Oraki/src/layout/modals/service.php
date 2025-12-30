<div id="modal-service" class="hidden fixed inset-0 z-50 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 scale-95">
    <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl pointer-events-auto overflow-hidden">
        
        <div class="bg-white p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-astral flex items-center gap-2"><i data-lucide="sparkles" class="text-summer"></i> Novo Serviço</h3>
            <button onclick="closeModal('modal-service')" class="text-gray-400 hover:text-red-500"><i data-lucide="x"></i></button>
        </div>

        <div class="p-6">
            <form action="actions/create_service.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Nome do Serviço <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="Ex: Tarot Terapêutico">
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Valor (R$)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400">R$</span>
                        <input type="number" step="0.01" name="price" required class="w-full pl-10 p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="0.00">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Descrição/Anotações</label>
                    <textarea name="description" rows="3" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="O que está incluso?"></textarea>
                </div>

                <button type="submit" class="w-full bg-summer text-astral font-bold py-3 rounded-xl shadow-md hover:brightness-105 transition-all">Criar Serviço</button>
            </form>
        </div>
    </div>
</div>