<?php include 'src/layout/auth_head.php'; ?>

    <div class="bg-milk/5 backdrop-blur-lg border border-white/10 p-8 rounded-3xl w-full max-w-md shadow-2xl relative z-10">
        
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-milk">Crie sua conta</h1>
            <p class="text-gray-400 text-sm mt-1">Comece a organizar seus atendimentos hoje.</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-500/20 border border-red-500/50 text-red-200 text-sm p-3 rounded-lg mb-6 flex items-center gap-2">
                <i data-lucide="alert-circle" size="16"></i> <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="auth_action.php" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="register">
            
            <div>
                <label class="block text-sm font-medium text-palm mb-1">Seu Nome</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i data-lucide="user" size="18"></i>
                    </div>
                    <input type="text" name="name" required class="block w-full pl-10 pr-3 py-3 bg-astral/50 border border-crowberry rounded-xl text-milk placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-summer focus:border-transparent transition" placeholder="Maria Tarot">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-palm mb-1">E-mail</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i data-lucide="mail" size="18"></i>
                    </div>
                    <input type="email" name="email" required class="block w-full pl-10 pr-3 py-3 bg-astral/50 border border-crowberry rounded-xl text-milk placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-summer focus:border-transparent transition" placeholder="seu@email.com">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-palm mb-1">Senha</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <i data-lucide="lock" size="18"></i>
                    </div>
                    <input type="password" name="password" required minlength="6" class="block w-full pl-10 pr-3 py-3 bg-astral/50 border border-crowberry rounded-xl text-milk placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-summer focus:border-transparent transition" placeholder="Mínimo 6 caracteres">
                </div>
            </div>

            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-galactic hover:bg-crowberry focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-galactic transition-all mt-6">
                Cadastrar
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-sm text-gray-400">
                Já tem uma conta? 
                <a href="login.php" class="font-medium text-summer hover:text-palm transition">Faça login</a>
            </p>
        </div>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>