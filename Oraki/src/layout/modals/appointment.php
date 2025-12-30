<div id="modal-appointment" class="hidden fixed inset-0 z-50 flex items-center justify-center pointer-events-none opacity-0 transition-all duration-300 scale-95">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl pointer-events-auto overflow-hidden flex flex-col max-h-[90vh]">
        
        <div class="bg-astral p-4 flex justify-between items-center text-white">
            <h3 class="font-bold flex items-center gap-2"><i data-lucide="calendar-plus" class="text-summer"></i> Novo Agendamento</h3>
            <button onclick="closeModal('modal-appointment')" class="text-gray-400 hover:text-white"><i data-lucide="x"></i></button>
        </div>

        <div class="p-6 overflow-y-auto custom-scrollbar">
            <form action="actions/create_appointment.php" method="POST" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Cliente</label>
                    <select name="client_id" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic focus:ring-1 focus:ring-galactic">
                        <option value="" disabled selected>Selecione um cliente...</option>
                        <?php foreach($clientsList as $client): ?>
                            <option value="<?= $client['id'] ?>"><?= htmlspecialchars($client['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Data</label>
                        <input type="datetime-local" name="start_time" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Valor (R$)</label>
                        <input type="number" step="0.01" name="price" id="service_price" placeholder="0.00" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic font-bold text-astral">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Serviço</label>
                    <select name="service_id" id="service_select" onchange="updatePrice()" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic focus:ring-1 focus:ring-galactic">
                        <option value="" disabled selected>Qual será o atendimento?</option>
                        
                        <?php foreach($servicesList as $service): ?>
                            <option value="<?= $service['id'] ?>" data-price="<?= $service['price'] ?>">
                                <?= htmlspecialchars($service['name']) ?> 
                            </option>
                        <?php endforeach; ?>
                        
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Pagamento</label>
                        <select name="payment_method" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic">
                            <option value="pix">Pix</option>
                            <option value="credit_card">Cartão</option>
                            <option value="cash">Dinheiro</option>
                            <option value="payment_link">Link de Pagamento</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-astral uppercase mb-1">Status</label>
                        <select name="status" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic">
                            <option value="scheduled">Agendado</option>
                            <option value="completed">Concluído</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-astral uppercase mb-1">Anotações</label>
                    <textarea name="notes" rows="2" class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-galactic" placeholder="Algum detalhe importante?"></textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-galactic text-white font-bold py-3 rounded-xl shadow-lg hover:bg-crowberry transition-all">Confirmar Agendamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function updatePrice() {
        // 1. Pega os elementos do HTML
        const select = document.getElementById('service_select');
        const priceInput = document.getElementById('service_price');
        
        // 2. Descobre qual opção foi selecionada
        const selectedOption = select.options[select.selectedIndex];
        
        // 3. Pega o valor do atributo 'data-price'
        const price = selectedOption.getAttribute('data-price');
        
        // 4. Se existir preço, atualiza o input
        if (price) {
            priceInput.value = price;
        }
    }
</script>