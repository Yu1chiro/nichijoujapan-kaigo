<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nichijou Japan ID </title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .wabi-sabi-bg {
            background-color: #F4F1EA;
        }

        .wabi-sabi-card {
            background-color: #FCFAFA;
            border: 1px solid #E5E1D8;
        }

        ::-webkit-scrollbar {
            width: 4px;
        }

        ::-webkit-scrollbar-track {
            background: #F4F1EA;
        }

        ::-webkit-scrollbar-thumb {
            background: #D1CEC5;
            border-radius: 10px;
        }
    </style>
</head>

<body class="wabi-sabi-bg text-stone-800 font-sans antialiased" x-data="{
    activeCategory: 'Semua',
    visibleCount: 6,
    decks: {{ $decks->toJson() }},
    get filteredDecks() {
        if (this.activeCategory === 'Semua') return this.decks;
        return this.decks.filter(d => d.category === this.activeCategory);
    }
}" x-cloak>

    <nav class="bg-white/80 backdrop-blur-md border-b border-stone-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8 flex justify-between h-16 items-center">
            <span class="text-xl font-bold text-stone-800 tracking-widest uppercase">NICHIJOU JAPAN ID <span
                    class="text-stone-400">CONTEXTUAL TEST</span></span>
            <div class="flex items-center space-x-6">
                <a href="{{ route('dashboard') }}"
                    class="text-xs font-bold text-stone-500 hover:text-stone-800 uppercase tracking-widest transition-colors">Student
                    Dashboard</a>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-6 py-16">
        <header class="text-center mb-12">
            <h1 class="text-3xl font-bold text-stone-800 tracking-tight">Pilih Deck Belajar Kamu</h1>
            <p class="text-stone-400 text-sm mt-3 font-medium">Filter materi berdasarkan contextual </p>
        </header>

        <div class="flex flex-wrap justify-center gap-3 mb-16">
            <template
                x-for="cat in ['Semua', 'Kaigo', 'Pengolahan Makanan', 'Genba/kontruksi', 'Restoran', 'Manufaktur']">
                <button @click="activeCategory = cat; visibleCount = 6"
                    :class="activeCategory === cat ? 'bg-stone-800 text-white border-stone-800' :
                        'bg-white text-stone-500 border-stone-200 hover:border-stone-400'"
                    class="px-5 py-2.5 rounded-xl border text-[10px] font-bold uppercase tracking-widest transition-all"
                    x-text="cat"></button>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <template x-for="(deck, index) in filteredDecks.slice(0, visibleCount)" :key="deck.id">
                <div
                    class="wabi-sabi-card rounded-2xl overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="relative group">
                        <img :src="deck.thumbnail_url"
                            class="w-full h-56 object-cover grayscale-[20%] group-hover:grayscale-0 transition-all duration-500">
                        <div class="absolute top-4 left-4">
                            <span
                                class="bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-[9px] font-bold text-stone-600 uppercase tracking-tighter"
                                x-text="deck.category"></span>
                        </div>
                    </div>

                    <div class="p-8">
                        <h3 class="text-lg font-bold text-stone-800 mb-3" x-text="deck.title"></h3>
                        <p class="text-stone-500 text-xs leading-relaxed line-clamp-2 mb-8"
                            x-text="deck.description || 'Simulasi ujian profesional.'"></p>

                        <a :href="'/shiken/' + deck.id"
                            class="block text-center bg-stone-800 text-white font-bold py-4 rounded-xl text-xs uppercase tracking-[0.2em] hover:bg-stone-700 shadow-md shadow-stone-200 transition-all">
                            Mulai Sekarang
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <template x-if="visibleCount < filteredDecks.length">
            <div class="mt-20 text-center">
                <button @click="visibleCount += 3"
                    class="px-10 py-4 bg-white border border-stone-200 text-stone-600 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-stone-50 transition-all shadow-sm">
                    Tampilkan Lebih Banyak
                </button>
            </div>
        </template>

        <template x-if="filteredDecks.length === 0">
            <div class="py-20 text-center">
                <p class="text-stone-300 text-[10px] font-bold uppercase tracking-[0.2em]">Belum ada materi untuk
                    kategori ini</p>
            </div>
        </template>
    </main>

    <footer class="py-12 text-center text-stone-300 text-[10px] font-bold uppercase tracking-[0.3em]">
        &mdash; Contextual Learning Platform &mdash;
    </footer>
</body>

</html>
