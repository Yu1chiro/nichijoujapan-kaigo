<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Peserta - CBT System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
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

<body class="wabi-sabi-bg font-sans antialiased text-stone-800" x-data="dashboardApp()" x-cloak>

    <nav class="bg-white/80 backdrop-blur-md border-b border-stone-200 py-4 px-6 sticky top-0 z-30 shadow-sm">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="font-bold text-base text-stone-800 tracking-widest uppercase">Portal<span
                    class="text-stone-400">Peserta</span></h1>
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}"
                    class="text-xs font-bold text-stone-400 hover:text-stone-800 transition-colors uppercase tracking-widest">Beranda</a>
                <button @click="logout"
                    class="text-xs font-bold text-red-400 hover:text-red-600 uppercase tracking-widest">Keluar</button>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto py-10 px-6">

        <div class="mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-stone-800 leading-tight">
                Halo, <span class="text-stone-500" x-text="user.name || 'Peserta'"></span>.
            </h2>
            <p class="text-stone-400 text-sm mt-2 font-medium">Berikut adalah catatan perjalanan dan peningkatan belajar
                Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <div class="wabi-sabi-card p-8 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3">Total Simulasi</p>
                <p class="text-3xl font-bold text-stone-800" x-text="history.length"></p>
            </div>
            <div class="bg-stone-800 p-8 rounded-2xl shadow-xl text-white">
                <p class="text-[10px] font-bold opacity-50 uppercase tracking-widest mb-3">Rata-rata Akurasi (%)</p>
                <p class="text-3xl font-bold" x-text="avgScore"></p>
            </div>
            <div class="wabi-sabi-card p-8 rounded-2xl shadow-sm">
                <p class="text-[10px] font-bold text-stone-400 uppercase tracking-widest mb-3">Access Key Terakhir</p>
                <p class="text-sm font-mono font-bold text-stone-600" x-text="user.access_key || '-'"></p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="wabi-sabi-card rounded-2xl shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-stone-100 flex justify-between items-center bg-stone-50/30">
                    <h3 class="font-bold text-stone-700 text-xs uppercase tracking-widest">Riwayat Simulasi</h3>
                    <button @click="clearHistory"
                        class="text-[10px] text-red-400 font-bold uppercase tracking-tighter hover:underline">Hapus
                        Semua</button>
                </div>
                <div class="overflow-y-auto max-h-[500px]">
                    <template x-for="(item, idx) in history.slice().reverse()" :key="idx">
                        <div
                            class="p-6 border-b border-stone-50 hover:bg-stone-50 transition-colors flex justify-between items-center group">
                            <div class="flex-grow">
                                <p class="font-bold text-stone-700 text-sm group-hover:text-stone-900 transition-colors"
                                    x-text="item.deck_title"></p>
                                <div class="flex items-center space-x-3 mt-1">
                                    <p class="text-[10px] text-stone-300 font-bold" x-text="item.date"></p>
                                    <button @click="viewDetail(item)"
                                        class="text-[9px] bg-stone-100 text-stone-500 px-2 py-0.5 rounded uppercase font-bold hover:bg-stone-800 hover:text-white transition-all">Lihat
                                        Pembahasan</button>
                                </div>
                            </div>
                            <div class="text-right ml-4">
                                <p class="text-lg font-bold text-stone-800"><span x-text="item.score"></span><span
                                        class="text-stone-200 text-xs mx-1">/</span><span class="text-stone-400 text-xs"
                                        x-text="item.total"></span></p>
                                <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest">Poin</p>
                            </div>
                        </div>
                    </template>
                    <template x-if="history.length === 0">
                        <div class="p-20 text-center">
                            <p class="text-stone-300 text-[10px] font-bold uppercase tracking-[0.2em]">Belum ada catatan
                                simulasi</p>
                        </div>
                    </template>
                </div>
            </div>

            <div class="wabi-sabi-card rounded-2xl shadow-sm p-8 flex flex-col">
                <h3 class="font-bold text-stone-700 text-xs uppercase tracking-widest mb-10">Analisis Peningkatan (%)
                </h3>
                <div id="progressChart"></div>
                <template x-if="history.length === 0">
                    <p class="text-center text-stone-300 text-[10px] font-bold uppercase tracking-widest mt-10 italic">
                        Data tidak cukup untuk grafik</p>
                </template>
            </div>
        </div>
    </main>

    <div x-show="selectedResult" class="fixed inset-0 z-[100] overflow-y-auto"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

        <div class="fixed inset-0 bg-stone-900/40 backdrop-blur-sm" @click="selectedResult = null"></div>

        <div class="relative min-h-screen flex items-center justify-center p-4 md:p-8">
            <div
                class="wabi-sabi-card w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div
                    class="p-6 md:p-8 border-b border-stone-100 flex justify-between items-center bg-white sticky top-0 z-10">
                    <div>
                        <h3 class="font-bold text-stone-800 text-base md:text-lg" x-text="selectedResult?.deck_title">
                        </h3>
                        <p class="text-[10px] text-stone-400 font-bold uppercase tracking-widest"
                            x-text="selectedResult?.date"></p>
                    </div>
                    <button @click="selectedResult = null"
                        class="p-2 hover:bg-stone-100 rounded-full text-stone-400 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="p-6 md:p-10 overflow-y-auto flex-grow bg-stone-50/20">
                    <template x-for="(res, index) in selectedResult?.details" :key="index">
                        <div class="mb-10 pb-10 border-b border-stone-100 last:border-0 last:mb-0 last:pb-0">
                            <div class="flex items-center space-x-3 mb-4">
                                <span class="text-[9px] font-bold px-2 py-0.5 rounded uppercase"
                                    :class="res.is_correct ? 'bg-stone-800 text-white' : 'bg-stone-200 text-stone-500'"
                                    x-text="res.is_correct ? 'Tepat' : 'Kurang Tepat'"></span>
                                <span class="text-[10px] font-bold text-stone-300 uppercase tracking-widest">Soal
                                    #<span x-text="index+1"></span></span>
                            </div>

                            <div class="prose prose-stone prose-sm max-w-none mb-6">
                                <div class="text-base font-semibold text-stone-800 leading-relaxed" x-html="res.q">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="p-4 rounded-xl border border-stone-100 bg-white shadow-sm">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-2">
                                        Pilihan Anda</p>
                                    <p class="text-sm font-bold"
                                        :class="!res.is_correct ? 'text-red-500' : 'text-stone-700'"
                                        x-text="getOptionLabel(res.user_ans, res.options)"></p>
                                </div>
                                <div class="p-4 rounded-xl border border-stone-100 bg-stone-100/50">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-2">Kunci
                                        Jawaban</p>
                                    <p class="text-sm font-bold text-stone-800"
                                        x-text="getOptionLabel(res.correct_ans, res.options)"></p>
                                </div>
                            </div>

                            <template x-if="res.feedback">
                                <div class="bg-stone-100/30 p-5 rounded-xl border-l-2 border-stone-200">
                                    <p class="text-[9px] font-bold text-stone-400 uppercase tracking-widest mb-2">
                                        Penjelasan</p>
                                    <div class="text-sm text-stone-600 leading-relaxed italic" x-html="res.feedback">
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <script>
        function dashboardApp() {
            const history = JSON.parse(localStorage.getItem('shiken_history') || '[]');
            const userData = JSON.parse(localStorage.getItem('cbt_user') || '{}');
            const scores = history.map(h => ((h.score / h.total) * 100).toFixed(0));

            return {
                history: history,
                user: userData,
                selectedResult: null,
                avgScore: history.length ?
                    (scores.reduce((a, b) => parseFloat(a) + parseFloat(b), 0) / scores.length).toFixed(0) : 0,

                init() {
                    if (history.length > 0) {
                        this.$nextTick(() => {
                            this.renderChart(scores);
                        });
                    }
                },

                viewDetail(item) {
                    this.selectedResult = item;
                },

                getOptionLabel(index, options) {
                    if (index === null || index === undefined) return 'Kosong';
                    const letter = String.fromCharCode(65 + parseInt(index));
                    const text = options[index]?.text || '';
                    return `${letter}. ${text}`;
                },

                renderChart(data) {
                    new ApexCharts(document.querySelector("#progressChart"), {
                        series: [{
                            name: 'Skor Akurasi',
                            data: data
                        }],
                        chart: {
                            type: 'bar',
                            height: 350,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'Plus Jakarta Sans, sans-serif'
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 8,
                                columnWidth: '40%',
                                distributed: true,
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        colors: ['#3C3C3C', '#575757', '#737373', '#8E8E8E', '#AAAAAA'],
                        xaxis: {
                            categories: history.map((_, i) => `S-${i+1}`),
                            labels: {
                                style: {
                                    colors: '#A8A29E',
                                    fontWeight: 600,
                                    fontSize: '10px'
                                }
                            }
                        },
                        yaxis: {
                            max: 100,
                            labels: {
                                style: {
                                    colors: '#A8A29E',
                                    fontWeight: 600,
                                    fontSize: '10px'
                                }
                            }
                        },
                        grid: {
                            borderColor: '#E5E1D8',
                            strokeDashArray: 4
                        },
                        legend: {
                            show: false
                        }
                    }).render();
                },

                clearHistory() {
                    if (confirm('Hapus seluruh riwayat belajar dari perangkat ini?')) {
                        localStorage.removeItem('shiken_history');
                        localStorage.removeItem('cbt_last_result');
                        location.reload();
                    }
                },

                logout() {
                    if (confirm('Keluar dari portal?')) {
                        localStorage.removeItem('cbt_user');
                        window.location.href = "{{ route('home') }}";
                    }
                }
            }
        }
    </script>
</body>

</html>
