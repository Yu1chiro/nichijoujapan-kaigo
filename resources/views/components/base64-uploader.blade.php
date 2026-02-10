<div x-data="{
    state: $wire.entangle('{{ $getStatePath() }}'),
    isLoading: false,
    handleFile(event) {
        const file = event.target.files[0];
        if (!file) return;

        this.isLoading = true;
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = new Image();
            img.onload = () => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');

                // Kompresi ke max-width 800px (Sangat penting untuk limit Vercel)
                const MAX_WIDTH = 800;
                let width = img.width;
                let height = img.height;

                if (width > MAX_WIDTH) {
                    height *= MAX_WIDTH / width;
                    width = MAX_WIDTH;
                }

                canvas.width = width;
                canvas.height = height;
                ctx.drawImage(img, 0, 0, width, height);

                // Gunakan mime type asli (PNG tetap PNG, JPG tetap JPG)
                const mime = file.type || 'image/jpeg';
                const base64 = canvas.toDataURL(mime, 0.8);

                this.state = base64; // Masukkan ke state Filament
                this.isLoading = false;
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
}" class="space-y-3">
    <div class="flex items-center justify-center w-full">
        <label
            class="flex flex-col items-center justify-center w-full h-32 border-2 border-blue-400 border-dashed rounded-3xl cursor-pointer bg-blue-50/50 hover:bg-blue-100/50 transition-all overflow-hidden relative">
            <template x-if="isLoading">
                <div class="absolute inset-0 bg-white/80 flex items-center justify-center z-10">
                    <span class="text-xs font-bold text-blue-600 animate-pulse">MEMPROSES GAMBAR...</span>
                </div>
            </template>

            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                    </path>
                </svg>
                <p class="text-xs text-blue-600 font-bold uppercase tracking-wider">Pilih Gambar (PNG, JPG, JPEG)</p>
            </div>
            <input type="file" class="hidden" accept="image/png, image/jpeg, image/jpg" @change="handleFile" />
        </label>
    </div>

    <template x-if="state">
        <div class="relative w-full p-2 bg-white border border-blue-100 rounded-2xl shadow-sm">
            <img :src="state" class="w-full h-40 object-contain rounded-xl">
            <button type="button" @click="state = null"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 shadow-lg hover:bg-red-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </template>
</div>
