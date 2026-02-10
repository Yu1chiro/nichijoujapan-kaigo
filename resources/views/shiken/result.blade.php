<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil - {{ $deck->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
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

<body class="wabi-sabi-bg text-stone-700 antialiased" x-data="resultApp()" x-cloak>

    <div class="max-w-3xl mx-auto px-6 py-12 md:py-20">

        <header class="text-center mb-16">
            <p class="text-[10px] font-bold tracking-[0.3em] text-stone-400 uppercase mb-4">Ringkasan Hasil</p>
            <h1 class="text-2xl font-bold text-stone-800 mb-8">{{ $deck->title }}</h1>

            <div class="inline-flex flex-col items-center justify-center">
                <div class="text-6xl font-light text-stone-800 mb-2">
                    <span x-text="score.correct"></span><span class="text-stone-300 text-4xl mx-2">/</span><span
                        class="text-stone-400 text-4xl" x-text="rawData.total"></span>
                </div>
                <div class="h-0.5 w-12 bg-stone-200 rounded-full mb-4"></div>
                <p class="text-sm font-medium text-stone-400 italic">"Tetap tenang, setiap kesalahan adalah guru."</p>
            </div>
        </header>

        <div class="flex flex-col md:flex-row justify-center gap-4 mb-16">
            <a href="{{ route('dashboard') }}"
                class="px-8 py-3 rounded-xl bg-stone-800 text-white text-xs font-bold uppercase tracking-widest text-center shadow-lg shadow-stone-200 hover:bg-stone-700 transition-all">Portal
                Siswa</a>
            <a href="{{ route('home') }}"
                class="px-8 py-3 rounded-xl bg-white border border-stone-200 text-xs font-bold uppercase tracking-widest text-stone-600 text-center hover:bg-stone-50 transition-all">Kembali
                Beranda</a>
        </div>

        <section class="space-y-10">
            <h2
                class="text-[11px] font-bold text-stone-400 uppercase tracking-[0.3em] text-center mb-10 border-b border-stone-200 pb-4">
                Daftar Pembahasan</h2>

            <template x-for="(res, index) in rawData.details" :key="index">
                <div class="wabi-sabi-card rounded-2xl p-6 md:p-10 mb-8 transition-hover">
                    <div class="flex items-center space-x-3 mb-6">
                        <span class="text-[9px] font-bold px-3 py-1 rounded-md uppercase tracking-tighter"
                            :class="res.is_correct ? 'bg-stone-100 text-stone-600' : 'bg-stone-200 text-stone-500'"
                            x-text="res.is_correct ? 'Tepat' : 'Kurang Tepat'"></span>
                        <span class="text-[10px] font-bold text-stone-300 uppercase tracking-widest">Soal #<span
                                x-text="index+1"></span></span>
                    </div>

                    <div class="prose prose-stone prose-sm max-w-none mb-8">
                        <div class="text-base md:text-lg font-semibold text-stone-800 leading-relaxed" x-html="res.q">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                        <div class="p-5 rounded-xl border border-stone-100"
                            :class="!res.is_correct ? 'bg-red-50/20 border-red-100' : 'bg-stone-50/50'">
                            <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-3">Pilihan Anda
                            </p>
                            <p class="text-sm font-bold" :class="!res.is_correct ? 'text-red-500' : 'text-stone-700'"
                                x-text="getOptionLabel(res.user_ans, res.options)"></p>
                        </div>

                        <div class="p-5 rounded-xl border border-stone-100 bg-stone-50/80">
                            <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-3">Kunci Jawaban
                            </p>
                            <p class="text-sm font-bold text-stone-800"
                                x-text="getOptionLabel(res.correct_ans, res.options)">
                            </p>
                        </div>
                    </div>

                    <template x-if="res.feedback">
                        <div class="bg-stone-100/50 p-6 rounded-xl border-l-2 border-stone-200">
                            <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-3">Penjelasan
                                Singkat</p>
                            <div class="text-sm text-stone-600 leading-relaxed italic" x-html="res.feedback"></div>
                        </div>
                    </template>
                </div>
            </template>
        </section>

        <footer class="mt-20 text-center text-stone-300 text-[10px] font-medium uppercase tracking-[0.3em]">
            &mdash; Selesai &mdash;
        </footer>
    </div>

    <script>
        function resultApp() {
            const data = JSON.parse(localStorage.getItem('cbt_last_result')) || {
                details: [],
                score: 0,
                total: 0
            };
            return {
                rawData: data,
                score: {
                    correct: data.score,
                    wrong: data.total - data.score
                },
                getOptionLabel(index, options) {
                    if (index === null || index === undefined) return 'Kosong';
                    const letter = String.fromCharCode(65 + parseInt(index));
                    const text = options[index]?.text || '';
                    return `${letter}. ${text}`;
                }
            }
        }
    </script>
</body>

</html>
