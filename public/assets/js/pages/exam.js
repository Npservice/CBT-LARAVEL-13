/**
 * Exam Page - Student Exam Interface
 * Page-specific logic for exam/welcome page
 */

const ExamPage = {
    currentQuestion: 1,
    totalQuestions: 40,
    questions: {},
    marked: new Set(),
    answered: new Set(),

    init: function() {
        this.renderQuestionGrid();
        this.renderQuestion(1);
        this.startTimer();
    },

    /**
     * Render question grid
     */
    renderQuestionGrid: function() {
        let html = '';
        for (let i = 1; i <= this.totalQuestions; i++) {
            const isCurrentClass = i === this.currentQuestion ? 'bg-sky-500 text-white border-sky-500' : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50';
            const isAnswered = this.answered.has(i) ? 'bg-emerald-50 border-emerald-500 text-emerald-700' : '';
            const isMarked = this.marked.has(i) ? 'bg-indigo-50 border-indigo-500 text-indigo-700' : '';

            const statusClass = i === this.currentQuestion ? isCurrentClass : (isAnswered || isMarked || '');

            html += `
                <button onclick="ExamPage.goToQuestion(${i})" class="aspect-square flex items-center justify-center text-xs font-semibold rounded-md border-2 transition ${statusClass}">
                    ${String(i).padStart(2, '0')}
                </button>
            `;
        }
        $('#questionGrid').html(html);
    },

    /**
     * Render question
     */
    renderQuestion: function(number) {
        // Demo data - in production, fetch from API
        const questionData = {
            number: number,
            text: `Pertanyaan ${number}: Ini adalah contoh soal ujian yang akan ditampilkan untuk siswa.`,
            score: 15,
            options: [
                'Pilihan A - Mempercepat proses coding',
                'Pilihan B - Memaksa database agar berjalan lebih lambat',
                'Pilihan C - Menghilangkan skema proteksi tabel'
            ]
        };

        $('#questionNumber').text(String(number).padStart(2, '0'));
        $('#questionScore').text(questionData.score);
        $('#questionText').text(questionData.text);

        let optionsHtml = '';
        questionData.options.forEach((option, index) => {
            optionsHtml += `
                <label class="flex items-center p-3.5 rounded-lg border border-slate-200 hover:bg-slate-50 hover:border-slate-300 transition-all cursor-pointer group">
                    <input type="radio" name="answer_${number}" value="${index}" class="w-4 h-4 text-sky-500 border-slate-300">
                    <span class="ml-3 text-xs sm:text-sm font-medium text-slate-600 group-hover:text-slate-900">
                        ${option}
                    </span>
                </label>
            `;
        });

        $('#optionsContainer').html(optionsHtml);

        // Add change event
        $(`input[name="answer_${number}"]`).on('change', function() {
            ExamPage.answerQuestion(number);
        });

        this.currentQuestion = number;
    },

    /**
     * Answer question
     */
    answerQuestion: function(number) {
        this.answered.add(number);
        $('#answeredCount').text(this.answered.size);
        this.renderQuestionGrid();
    },

    /**
     * Toggle mark on question
     */
    toggleMark: function() {
        if (this.marked.has(this.currentQuestion)) {
            this.marked.delete(this.currentQuestion);
            $('#markText').text('Tandai');
        } else {
            this.marked.add(this.currentQuestion);
            $('#markText').text('Tandai (✓)');
        }

        $('#markedCount').text(this.marked.size);
        this.renderQuestionGrid();
    },

    /**
     * Go to specific question
     */
    goToQuestion: function(number) {
        if (number >= 1 && number <= this.totalQuestions) {
            this.renderQuestion(number);
            this.updateMarkButton();
        }
    },

    /**
     * Previous question
     */
    prevQuestion: function() {
        if (this.currentQuestion > 1) {
            this.goToQuestion(this.currentQuestion - 1);
        }
    },

    /**
     * Next question
     */
    nextQuestion: function() {
        if (this.currentQuestion < this.totalQuestions) {
            this.goToQuestion(this.currentQuestion + 1);
        }
    },

    /**
     * Update mark button text
     */
    updateMarkButton: function() {
        if (this.marked.has(this.currentQuestion)) {
            $('#markText').text('Tandai (✓)');
        } else {
            $('#markText').text('Tandai');
        }
    },

    /**
     * Start countdown timer
     */
    startTimer: function() {
        let timeRemaining = (1 * 60 * 60) + (45 * 60) + 22; // 1:45:22

        setInterval(() => {
            timeRemaining--;

            const hours = Math.floor(timeRemaining / 3600);
            const minutes = Math.floor((timeRemaining % 3600) / 60);
            const seconds = timeRemaining % 60;

            const timeString = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            $('#timer').text(timeString);

            // Warn when 5 minutes left
            if (timeRemaining === 300) {
                Toast.warning('Sisa waktu ujian 5 menit!');
            }

            // Submit when time's up
            if (timeRemaining <= 0) {
                this.submitExam();
            }
        }, 1000);
    },

    /**
     * Submit exam
     */
    submitExam: function() {
        Toast.error('Waktu habis! Ujian Anda akan disubmit.');
        // In production, submit to API
        console.log('Submitting exam...');
    }
};

// Initialize when document ready
$(document).ready(function() {
    ExamPage.init();
});
