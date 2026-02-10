<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CBT Exam - {{ $deck->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Prometric Styles */
        .prometric-header {
            background-color: #f8f9fa;
            border-bottom: 3px solid #e9ecef;
        }

        .timer-box {
            background-color: #fff3cd;
            color: #856404;
            font-family: monospace;
            font-weight: bold;
        }

        /* Grid Navigation */
        .grid-box {
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #ced4da;
            background: white;
            transition: all 0.2s;
        }

        .grid-box:hover {
            background-color: #e2e6ea;
        }

        .grid-box.current {
            border: 2px solid #0d6efd;
            color: #0d6efd;
        }

        .grid-box.answered {
            background-color: #d1e7dd;
            color: #0f5132;
            border-color: #badbcc;
        }

        .grid-box.flagged {
            position: relative;
        }

        .grid-box.flagged::after {
            content: '⚑';
            position: absolute;
            top: -4px;
            right: 2px;
            color: #fd7e14;
            font-size: 10px;
        }
    </style>
</head>

<body x-data="prometricEngine()" x-init="initExam()" class="h-full flex flex-col font-sans text-sm text-gray-800" x-cloak>

    <header class="prometric-header h-14 flex justify-between items-center px-4 md:px-6 shrink-0 z-30 shadow-sm">
        <div class="font-bold text-gray-700 truncate text-base md:text-lg">
            {{ $deck->title }}
        </div>
        <div class="flex items-center h-full">
            <div class="hidden md:flex flex-col justify-center px-4 h-full border-l border-gray-300 bg-gray-50 text-xs">
                <span class="text-gray-500">Examinee:</span>
                <span class="font-bold text-gray-900 truncate max-w-[150px]" x-text="user.name || 'Peserta'"></span>
            </div>
            <div class="timer-box h-full px-4 flex items-center text-lg md:text-xl border-l border-gray-300">
                <span x-text="formatTimer(timerRemaining)">--:--</span>
            </div>
            <button @click="exitExam"
                class="ml-4 bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded text-xs font-bold uppercase tracking-wider transition-colors">
                Keluar
            </button>
        </div>
    </header>

    <main class="flex-grow flex flex-col md:flex-row overflow-hidden relative">

        <div
            class="w-full md:w-3/5 h-full overflow-y-auto p-4 md:p-8 bg-white border-r border-gray-200 order-2 md:order-1">
            <template x-if="questions.length > 0">
                <div class="max-w-4xl mx-auto pb-20">
                    <div class="flex justify-between items-center mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-bold text-gray-800">
                            Soal No. <span x-text="currentIndex + 1"></span>
                        </h2>
                        <button @click="toggleFlag"
                            class="flex items-center gap-2 px-3 py-1.5 rounded border transition-all text-xs font-bold uppercase"
                            :class="flags[currentIndex] ? 'bg-orange-50 text-orange-600 border-orange-200' :
                                'bg-white text-gray-500 border-gray-200 hover:bg-gray-50'">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 3a1 1 0 011-1h12a1 1 0 011 1v10a1 1 0 01-1 1H5a1 1 0 01-1-1V3zm2 1v8h10V4H5z" />
                            </svg>
                            <span>Tandai Ragu</span>
                        </button>
                    </div>

                    <template x-if="currentQ.thumbnail_url">
                        <div class="mb-6 p-2 border border-gray-200 rounded-lg bg-gray-50 flex justify-center">
                            <img :src="currentQ.thumbnail_url" class="max-h-[300px] object-contain rounded">
                        </div>
                    </template>

                    <template x-if="currentQ.audio_url">
                        <div
                            class="mb-6 bg-blue-50 border border-blue-100 p-4 rounded-xl flex items-center gap-4 shadow-sm">
                            <div class="bg-blue-600 p-2 rounded-full text-white shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z">
                                    </path>
                                </svg>
                            </div>
                            <div class="w-full">
                                <p class="text-xs font-bold text-blue-800 mb-1 uppercase tracking-wider">Listening
                                    Section</p>
                                <audio :src="currentQ.audio_url" controls controlsList="nodownload"
                                    class="w-full h-8"></audio>
                            </div>
                        </div>
                    </template>

                    <div class="prose prose-stone max-w-none text-gray-800 text-base leading-relaxed mb-8"
                        x-html="currentQ.question_text"></div>

                    <div class="block md:hidden mt-8 border-t border-gray-200 pt-6">
                        <h3 class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-4">Pilih Jawaban</h3>
                        <div class="space-y-3">
                            <template x-for="(option, idx) in currentQ.options" :key="'m' + idx">
                                <label class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all"
                                    :class="answers[currentIndex] === idx ? 'border-blue-600 bg-blue-50' :
                                        'border-gray-200 bg-white'">
                                    <input type="radio" :name="'qm' + currentIndex" class="hidden"
                                        :checked="answers[currentIndex] === idx" @change="selectAnswer(idx)">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm mr-3 shrink-0"
                                        :class="answers[currentIndex] === idx ? 'bg-blue-600 text-white' :
                                            'bg-gray-200 text-gray-600'"
                                        x-text="String.fromCharCode(65 + idx)"></div>
                                    <span class="text-sm font-medium" x-text="option.text"></span>
                                </label>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div
            class="hidden md:block w-2/5 h-full overflow-y-auto bg-gray-50 border-l border-gray-200 p-6 order-2 shadow-inner">
            <div class="sticky top-0">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3
                        class="font-bold text-gray-700 text-sm uppercase tracking-wider mb-6 pb-2 border-b border-gray-100">
                        Lembar Jawaban</h3>
                    <div class="space-y-3">
                        <template x-for="(option, idx) in currentQ.options" :key="'d' + idx">
                            <label class="flex cursor-pointer group select-none">
                                <input type="radio" :name="'q' + currentIndex" class="hidden"
                                    :checked="answers[currentIndex] === idx" @change="selectAnswer(idx)">

                                <span
                                    class="flex-1 flex items-center p-3 border-2 rounded-lg transition-all group-hover:border-blue-300"
                                    :class="answers[currentIndex] === idx ? 'bg-blue-600 border-blue-600 text-white shadow-md' :
                                        'bg-white border-gray-300 text-gray-700'">

                                    <span
                                        class="w-7 h-7 rounded-full border flex items-center justify-center font-bold text-xs mr-3"
                                        :class="answers[currentIndex] === idx ? 'border-white text-blue-600 bg-white' :
                                            'border-gray-400 text-gray-500'"
                                        x-text="String.fromCharCode(65 + idx)">A</span>

                                    <span class="text-sm font-medium" x-text="option.text"></span>
                                </span>
                            </label>
                        </template>
                    </div>
                </div>

                <div class="mt-6">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Navigasi Cepat</p>
                    <div class="grid grid-cols-5 gap-2">
                        <template x-for="(q, idx) in questions" :key="'nav' + idx">
                            <div class="h-8 rounded text-xs flex items-center justify-center cursor-pointer border font-bold"
                                :class="{
                                    'bg-blue-600 text-white border-blue-600': currentIndex === idx,
                                    'bg-green-100 text-green-700 border-green-200': answers[idx] !== null &&
                                        currentIndex !== idx,
                                    'bg-white text-gray-500 border-gray-200 hover:bg-gray-100': answers[idx] === null &&
                                        currentIndex !== idx,
                                    'ring-2 ring-orange-400': flags[idx]
                                }"
                                @click="jumpTo(idx)" x-text="idx + 1">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="h-16 bg-white border-t border-gray-200 flex justify-between items-center px-6 shrink-0 z-30">
        <button @click="showGridModal = true"
            class="md:hidden flex flex-col items-center text-gray-500 hover:text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                </path>
            </svg>
            <span class="text-[10px] font-bold uppercase mt-1">Navigasi</span>
        </button>

        <div class="flex gap-3 ml-auto md:ml-0 md:w-full md:justify-between">
            <button @click="prevBtn" :disabled="currentIndex === 0"
                class="px-6 py-2 rounded-lg border border-gray-300 text-gray-600 font-bold text-sm uppercase hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed">
                Sebelumnya
            </button>

            <button @click="nextBtn"
                class="px-8 py-2 rounded-lg bg-blue-600 text-white font-bold text-sm uppercase shadow-lg shadow-blue-200 hover:bg-blue-700 transition-all flex items-center gap-2">
                <span x-text="currentIndex === totalQuestions - 1 ? 'Selesai Ujian' : 'Berikutnya'"></span>
                <svg x-show="currentIndex !== totalQuestions - 1" class="w-4 h-4" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>
    </footer>

    <div x-show="!hasAccess"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/90 backdrop-blur-sm"
        x-transition.opacity>
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden">
            <div class="p-8 text-center">
                <div
                    class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Verifikasi Peserta</h2>
                <p class="text-gray-500 text-sm mb-6">Silakan masukkan nama dan kode akses ujian untuk memulai.</p>

                <form @submit.prevent="verifyAccess" class="space-y-4 text-left">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" x-model="inputName" required placeholder="Contoh: Budi Santoso"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Access Key
                            (Premium)</label>
                        <input type="text" x-model="inputKey" required placeholder="Masukkan Kunci Akses"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all outline-none font-mono font-bold tracking-widest text-center uppercase">
                    </div>

                    <div x-show="loginError"
                        class="p-3 bg-red-50 text-red-600 text-xs font-bold rounded-lg text-center"
                        x-text="loginError"></div>

                    <button type="submit" :disabled="isVerifying"
                        class="w-full bg-stone-800 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-stone-700 transition-all disabled:opacity-70 disabled:cursor-wait">
                        <span x-show="!isVerifying">Mulai Ujian</span>
                        <span x-show="isVerifying" class="animate-pulse">Memeriksa...</span>
                    </button>
                </form>
            </div>
            <div class="bg-gray-50 p-4 text-center border-t border-gray-100">
                <a href="{{ route('home') }}"
                    class="text-xs font-bold text-gray-400 hover:text-gray-600 uppercase">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <div x-show="showGridModal" class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm md:hidden flex items-end"
        @click.self="showGridModal = false" x-transition.opacity>
        <div class="bg-white w-full rounded-t-3xl p-6 max-h-[80vh] overflow-y-auto"
            x-transition:enter="transform transition ease-out duration-300"
            x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0">
            <div class="flex justify-between items-center mb-6">
                <h3 class="font-bold text-lg text-gray-800">Navigasi Soal</h3>
                <button @click="showGridModal = false" class="p-2 bg-gray-100 rounded-full text-gray-600"><svg
                        class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg></button>
            </div>
            <div class="grid grid-cols-5 gap-3">
                <template x-for="(q, idx) in questions" :key="'mobnav' + idx">
                    <div class="h-10 rounded-lg flex items-center justify-center font-bold text-sm border shadow-sm"
                        :class="{
                            'bg-blue-600 text-white border-blue-600': currentIndex === idx,
                            'bg-green-100 text-green-700 border-green-200': answers[idx] !== null && currentIndex !==
                                idx,
                            'bg-white text-gray-600 border-gray-200': answers[idx] === null && currentIndex !== idx,
                            'ring-2 ring-orange-400': flags[idx]
                        }"
                        @click="jumpTo(idx)" x-text="idx + 1">
                    </div>
                </template>
            </div>
        </div>
    </div>

    <script>
        function prometricEngine() {
            return {
                // Data dari Server (Blade Injection)
                hasAccess: {{ $hasAccess ? 'true' : 'false' }},
                questions: @json($questions),
                deckId: {{ $deck->id }},

                // User Input State
                inputName: localStorage.getItem('cbt_username') || '',
                inputKey: '',
                loginError: '',
                isVerifying: false,

                // Exam State
                currentIndex: 0,
                timerRemaining: {{ (int) $deck->timer_per_question }} * {{ $questions->count() ?: 1 }},
                timerInterval: null,
                answers: [],
                flags: [],
                showGridModal: false,
                user: {
                    name: localStorage.getItem('cbt_username') || 'Peserta'
                },

                get currentQ() {
                    return this.questions[this.currentIndex] || {};
                },
                get totalQuestions() {
                    return this.questions.length;
                },

                initExam() {
                    if (this.hasAccess && this.questions.length > 0) {
                        this.answers = new Array(this.totalQuestions).fill(null);
                        this.flags = new Array(this.totalQuestions).fill(false);
                        this.startTimer();

                        // Restore progress if needed (Optional)
                        // this.loadProgress(); 
                    }
                },

                // === FUNGSI VALIDASI KEY (FITUR UTAMA) ===
                async verifyAccess() {
                    this.isVerifying = true;
                    this.loginError = '';

                    try {
                        const response = await axios.post(`/shiken/${this.deckId}/verify`, {
                            name: this.inputName,
                            key: this.inputKey
                        });

                        if (response.data.success) {
                            localStorage.setItem('cbt_username', this.inputName);
                            // Reload halaman agar server mengirim soal (karena session sudah valid)
                            window.location.href = response.data.redirect_url;
                        }
                    } catch (error) {
                        this.loginError = error.response?.data?.message || 'Terjadi kesalahan jaringan.';
                        this.isVerifying = false;
                    }
                },

                startTimer() {
                    this.timerInterval = setInterval(() => {
                        if (this.timerRemaining > 0) {
                            this.timerRemaining--;
                        } else {
                            this.finishExam(true);
                        }
                    }, 1000);
                },

                formatTimer(seconds) {
                    if (isNaN(seconds)) return "00:00";
                    const m = Math.floor(seconds / 60);
                    const s = seconds % 60;
                    return `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
                },

                selectAnswer(idx) {
                    this.answers[this.currentIndex] = idx;
                    // Auto next on mobile preference? Maybe not for CBT standard.
                },

                nextBtn() {
                    if (this.currentIndex < this.totalQuestions - 1) {
                        this.currentIndex++;
                    } else {
                        this.finishExam();
                    }
                    this.scrollToTop();
                },

                prevBtn() {
                    if (this.currentIndex > 0) {
                        this.currentIndex--;
                        this.scrollToTop();
                    }
                },

                jumpTo(idx) {
                    this.currentIndex = idx;
                    this.showGridModal = false;
                    this.scrollToTop();
                },

                toggleFlag() {
                    this.flags[this.currentIndex] = !this.flags[this.currentIndex];
                },

                scrollToTop() {
                    const container = document.querySelector('.overflow-y-auto');
                    if (container) container.scrollTop = 0;
                },

                exitExam() {
                    if (confirm('Apakah Anda yakin ingin keluar? Progres ujian akan hilang.')) {
                        window.location.href = "{{ route('home') }}";
                    }
                },

                finishExam(auto = false) {
                    clearInterval(this.timerInterval);

                    if (!auto) {
                        const unanswered = this.answers.filter(a => a === null).length;
                        let msg = "Apakah Anda yakin ingin menyelesaikan ujian ini?";
                        if (unanswered > 0) msg += `\nMasih ada ${unanswered} soal yang belum dijawab!`;

                        if (!confirm(msg)) {
                            this.startTimer(); // Resume timer
                            return;
                        }
                    }

                    // Hitung Skor (Client Side Pre-calculation)
                    let score = 0;
                    const details = this.questions.map((q, i) => {
                        const correct = this.answers[i] == q.correct_answer;
                        if (correct) score++;
                        return {
                            q: q.question_text,
                            options: q.options,
                            user_ans: this.answers[i],
                            correct_ans: q.correct_answer,
                            is_correct: correct,
                            feedback: q.feedback_text
                        };
                    });

                    // Simpan ke LocalStorage untuk halaman Result
                    const resultData = {
                        deck_title: "{{ $deck->title }}",
                        date: new Date().toLocaleDateString('id-ID', {
                            weekday: 'long',
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        }),
                        score: score,
                        total: this.totalQuestions,
                        details: details
                    };

                    // Simpan ke History (Append)
                    const history = JSON.parse(localStorage.getItem('shiken_history') || '[]');
                    history.push(resultData);
                    localStorage.setItem('shiken_history', JSON.stringify(history));

                    // Simpan Last Result
                    localStorage.setItem('cbt_last_result', JSON.stringify(resultData));

                    // Redirect ke Result Page
                    window.location.href = "{{ route('shiken.result', $deck->id) }}";
                }
            }
        }
    </script>
</body>

</html>
