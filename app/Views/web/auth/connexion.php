<?= $this->extend('web/layouts/app') ?>

<?= $this->section('content') ?>
<div class="flex items-center justify-center min-h-[85vh] bg-gray-50/50 px-4">
    <div class="w-full max-w-[420px] flex flex-col items-center">
        
        <div class="mb-8 gap-4 text-center">
            <div class="flex items-center gap-3">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-blue-600 text-white mx-auto *mb-3 shadow-sm">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">directions_bus</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">KishalaTrans</h1>
            </div>
            <p class="text-sm font-medium text-gray-500 mt-1">Connexion</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full shadow-sm transition-all">
            <form action="/login" method="post" class="space-y-5">
                
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700" for="username">
                        Nom d'utilisateur
                    </label>
                    <div class="relative">
                        <input
                            class="w-full rounded-xl border-gray-300 border px-4 py-2.5 text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-900 placeholder:text-gray-400 transition-all"
                            id="username" 
                            name="username" 
                            placeholder="votre identifiant" 
                            required 
                            type="text" 
                        />
                    </div>
                </div>
                
                <div class="space-y-1.5">
                    <label class="block text-sm font-medium text-gray-700" for="password">
                        Mot de passe
                    </label>
                    <div class="relative flex items-center">
                        <input
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl border-gray-300 border text-sm shadow-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none text-gray-900 placeholder:text-gray-400 transition-all"
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            required 
                            type="password" 
                        />
                        
                        <button type="button" id="togglePassword" class="absolute right-3.5 text-gray-400 hover:text-blue-600 transition-colors cursor-pointer select-none flex items-center p-1 rounded-lg hover:bg-gray-50">
                            <span class="material-symbols-outlined text-[20px]">visibility</span>
                        </button>
                    </div>
                </div>

                <button
                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-xl hover:bg-blue-700 transition-all shadow-sm cursor-pointer mt-2"
                    type="submit">
                    <span>Se connecter</span>
                    <span class="material-symbols-outlined text-[18px]">login</span>
                </button>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const passwordInput = document.getElementById('password');
        const togglePasswordButton = document.getElementById('togglePassword');
        const eyeIcon = togglePasswordButton.querySelector('.material-symbols-outlined');

        togglePasswordButton.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.textContent = isPassword ? 'visibility_off' : 'visibility';
        });
    });
</script> 
<?= $this->endSection() ?>