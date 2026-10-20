
document.addEventListener('DOMContentLoaded', () => {

    // 1. GET QUIZ ELEMENTS

    const form = document.getElementById('coffee-quiz');

    if (!form) {
        return;
    }

    const steps = [...form.querySelectorAll('.quiz-step')];

    const next = document.getElementById('quiz-next');
    const back = document.getElementById('quiz-back');
    const submit = document.getElementById('quiz-submit');

    const fill = document.getElementById('quiz-progress-fill');
    const label = document.getElementById('quiz-step-label');

    let i = 0;


    // 2. UPDATE QUIZ PROGRESS

    function update() {

        // Display the current question
        steps.forEach((s, n) => {
            s.classList.toggle('current', n === i);
        });

        // Update progress bar
        fill.style.width = ((i + 1) / steps.length * 100) + '%';

        // Update question number
        label.textContent =
            'QUESTION ' +
            String(i + 1).padStart(2, '0') +
            ' / 04';

        // Show or hide navigation buttons
        back.hidden = i === 0;

        next.hidden = i === steps.length - 1;

        submit.hidden = i !== steps.length - 1;

    }


    // 3. NEXT QUESTION BUTTON

    next.addEventListener('click', () => {

        // Check whether an answer has been selected
        if (!steps[i].querySelector('input:checked')) {

            steps[i].classList.add('needs-choice');
            return;

        }

        // Remove validation warning
        steps[i].classList.remove('needs-choice');

        // Move to the next question
        i = Math.min(i + 1, steps.length - 1);

        update();

    });


    // 4. PREVIOUS QUESTION BUTTON

    back.addEventListener('click', () => {

        // Return to the previous question
        i = Math.max(i - 1, 0);

        update();

    });


    // 5. REMOVE VALIDATION WARNING AFTER SELECTION

    steps.forEach(s => {

        s.querySelectorAll('input').forEach(r => {

            r.addEventListener('change', () => {

                s.classList.remove('needs-choice');

            });

        });

    });


    // 6. VALIDATE ANSWERS BEFORE SUBMISSION

    form.addEventListener('submit', e => {

        // Find the first unanswered question
        const missing = steps.findIndex(
            s => !s.querySelector('input:checked')
        );

        // Prevent submission if a question is unanswered
        if (missing !== -1) {

            e.preventDefault();
            e.stopImmediatePropagation();

            // Return to the unanswered question
            i = missing;

            update();

            // Highlight the question
            steps[i].classList.add('needs-choice');

            return;

        }


        // 7. HIDE QUIZ AFTER SUCCESSFUL VALIDATION

        form.style.display = 'none';

        document.querySelector('.quiz-journey-head').style.display = 'none';

    }, true);


    // 8. INITIALIZE QUIZ

    update();

});
