
<?php

require 'config/app.php';

// Set page title
$title = 'Find Your Coffee';


// =========================================
// 1. FETCH AVAILABLE DRINKS
// =========================================

$items = $db->query(
    'SELECT *
     FROM menu_item
     WHERE availability = 1
     AND stock_quantity > 0
     ORDER BY menu_name'
)->fetch_all(MYSQLI_ASSOC);


// =========================================
// 2. INCLUDE HEADER
// =========================================

include 'includes/header.php';

?>


<!-- =========================================
     3. QUIZ HERO SECTION
========================================= -->

<section class="quiz-hero">

    <div class="quiz-hero-copy">

        <span class="eyebrow">
            THE STARCOFFEE FLAVOUR FINDER
        </span>

        <h1>
            What's your<br>
            <em>coffee mood?</em>
        </h1>

        <p>
            Four little questions. One delicious discovery.
            Let's find your perfect match.
        </p>

        <a href="#quiz-start" class="btn dark">
            Let's find out ↓
        </a>

    </div>

    <img
        src="assets/img/home-coffee.png"
        alt="Coffee drink"
        class="quiz-hero-drink"
    >

</section>


<!-- =========================================
     4. QUIZ JOURNEY SECTION
========================================= -->

<section class="quiz-journey" id="quiz-start">

    <!-- Quiz Introduction -->
    <div class="quiz-journey-head">

        <span class="eyebrow">
            YOUR FLAVOUR JOURNEY
        </span>

        <h2>
            A little about your taste...
        </h2>

        <p>
            Choose the answer that feels most like you.
        </p>

        <!-- Progress Bar -->
        <div class="quiz-progress">
            <div id="quiz-progress-fill"></div>
        </div>

        <span id="quiz-step-label">
            QUESTION 01 / 04
        </span>

    </div>


    <!-- =====================================
         5. COFFEE QUIZ FORM
    ===================================== -->

    <form id="coffee-quiz" class="quiz-interactive">


        <!-- =================================
             QUESTION 1: TEMPERATURE
        ================================= -->

        <fieldset class="quiz-step current">

            <legend>
                How do you like your drinks?
            </legend>

            <p>
                Picture your perfect coffee moment.
            </p>

            <div class="quiz-options">

                <!-- Iced -->
                <label>

                    <input
                        type="radio"
                        name="temperature"
                        value="iced"
                        required
                    >

                    <span class="quiz-emoji">🧊</span>

                    <strong>Iced & refreshing</strong>

                    <small>
                        Cool, crisp, and energizing
                    </small>

                </label>


                <!-- Hot -->
                <label>

                    <input
                        type="radio"
                        name="temperature"
                        value="hot"
                    >

                    <span class="quiz-emoji">☕</span>

                    <strong>Warm & cozy</strong>

                    <small>
                        A comforting little ritual
                    </small>

                </label>


                <!-- Either -->
                <label>

                    <input
                        type="radio"
                        name="temperature"
                        value="either"
                    >

                    <span class="quiz-emoji">✨</span>

                    <strong>Surprise me</strong>

                    <small>
                        I'm open to anything
                    </small>

                </label>

            </div>

        </fieldset>


        <!-- =================================
             QUESTION 2: FLAVOUR
        ================================= -->

        <fieldset class="quiz-step">

            <legend>
                Pick your flavour vibe.
            </legend>

            <p>
                What sounds delicious right now?
            </p>

            <div class="quiz-options">

                <!-- Chocolate -->
                <label>

                    <input
                        type="radio"
                        name="flavour"
                        value="chocolate"
                        required
                    >

                    <span class="quiz-emoji">🍫</span>

                    <strong>Chocolate</strong>

                    <small>
                        Rich and indulgent
                    </small>

                </label>


                <!-- Vanilla and Caramel -->
                <label>

                    <input
                        type="radio"
                        name="flavour"
                        value="vanilla"
                    >

                    <span class="quiz-emoji">🍮</span>

                    <strong>Vanilla & caramel</strong>

                    <small>
                        Soft and comforting
                    </small>

                </label>


                <!-- Classic Coffee -->
                <label>

                    <input
                        type="radio"
                        name="flavour"
                        value="classic"
                    >

                    <span class="quiz-emoji">☕</span>

                    <strong>Classic coffee</strong>

                    <small>
                        Pure coffee goodness
                    </small>

                </label>


                <!-- Matcha -->
                <label>

                    <input
                        type="radio"
                        name="flavour"
                        value="matcha"
                    >

                    <span class="quiz-emoji">🌸</span>

                    <strong>Matcha</strong>

                    <small>
                        Something a little different
                    </small>

                </label>

            </div>

        </fieldset>


        <!-- =================================
             QUESTION 3: SWEETNESS
        ================================= -->

        <fieldset class="quiz-step">

            <legend>
                How sweet is your sweet spot?
            </legend>

            <p>
                There's no wrong answer.
            </p>

            <div class="quiz-options">

                <!-- Low Sweetness -->
                <label>

                    <input
                        type="radio"
                        name="sweetness"
                        value="low"
                        required
                    >

                    <span class="quiz-emoji">🤎</span>

                    <strong>Just a hint</strong>

                    <small>
                        Let the coffee shine
                    </small>

                </label>


                <!-- Medium Sweetness -->
                <label>

                    <input
                        type="radio"
                        name="sweetness"
                        value="medium"
                    >

                    <span class="quiz-emoji">🍯</span>

                    <strong>A little sweetness</strong>

                    <small>
                        Balanced and mellow
                    </small>

                </label>


                <!-- High Sweetness -->
                <label>

                    <input
                        type="radio"
                        name="sweetness"
                        value="high"
                    >

                    <span class="quiz-emoji">🧁</span>

                    <strong>Sweet treat</strong>

                    <small>
                        Make it a dessert moment
                    </small>

                </label>

            </div>

        </fieldset>


        <!-- =================================
             QUESTION 4: COFFEE STRENGTH
        ================================= -->

        <fieldset class="quiz-step">

            <legend>
                And your ideal coffee strength?
            </legend>

            <p>
                One last thing before your match!
            </p>

            <div class="quiz-options">

                <!-- Strong Coffee -->
                <label>

                    <input
                        type="radio"
                        name="strength"
                        value="strong"
                        required
                    >

                    <span class="quiz-emoji">⚡</span>

                    <strong>Bold & strong</strong>

                    <small>
                        Bring on the coffee flavour
                    </small>

                </label>


                <!-- Mild Coffee -->
                <label>

                    <input
                        type="radio"
                        name="strength"
                        value="mild"
                    >

                    <span class="quiz-emoji">☁️</span>

                    <strong>Mild & creamy</strong>

                    <small>
                        Soft and silky
                    </small>

                </label>


                <!-- No Coffee Taste -->
                <label>

                    <input
                        type="radio"
                        name="strength"
                        value="none"
                    >

                    <span class="quiz-emoji">🌙</span>

                    <strong>No coffee taste</strong>

                    <small>
                        I'd rather try something else
                    </small>

                </label>

            </div>

        </fieldset>


        <!-- =================================
             6. QUIZ NAVIGATION BUTTONS
        ================================= -->

        <div class="quiz-controls">

            <!-- Previous Question -->
            <button
                type="button"
                id="quiz-back"
                class="quiz-back"
                hidden
            >
                ← Previous
            </button>


            <!-- Next Question -->
            <button
                type="button"
                id="quiz-next"
                class="btn dark"
            >
                Next question →
            </button>


            <!-- Submit Quiz -->
            <button
                type="submit"
                id="quiz-submit"
                class="btn dark"
                hidden
            >
                Reveal my match ✨
            </button>

        </div>

    </form>


    <!-- =====================================
         7. QUIZ RESULT SECTION
    ===================================== -->

    <div
        id="quiz-result"
        class="quiz-result"
        hidden
        aria-live="polite"
    ></div>

</section>


<!-- =========================================
     8. JAVASCRIPT - QUIZ RECOMMENDATION
========================================= -->

<script>

    // =====================================
    // 8.1 GET AVAILABLE DRINKS FROM PHP
    // =====================================

    const drinks = <?= json_encode(
        array_map(
            fn($m) => [
                'id' => (int) $m['menu_id'],
                'name' => $m['menu_name'],
                'image' => item_picture($m),
                'description' => $m['description']
            ],
            $items
        ),
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ) ?>;


    // =====================================
    // 8.2 HANDLE QUIZ SUBMISSION
    // =====================================

    document.getElementById('coffee-quiz')
        .addEventListener('submit', e => {

            e.preventDefault();

            // Get selected answers
            const a = Object.fromEntries(
                new FormData(e.target)
            );


            // =================================
            // 8.3 CALCULATE DRINK MATCH SCORE
            // =================================

            const score = d => {

                let n = d.name.toLowerCase();
                let s = 0;


                // FLAVOUR PREFERENCE
                if (a.flavour === 'matcha') {

                    s += n.includes('matcha') ? 15 : -10;

                } else if (a.flavour === 'chocolate') {

                    s += /mocha|chocolate/.test(n) ? 12 : 0;

                } else if (a.flavour === 'vanilla') {

                    s += /vanilla|caramel|spanish|hazelnut/.test(n)
                        ? 12
                        : 0;

                } else {

                    s += /americano|classic|flat white|cappuccino/.test(n)
                        ? 10
                        : 0;

                }


                // TEMPERATURE PREFERENCE
                if (a.temperature === 'iced') {

                    s += /iced|ice|frappe|cold/.test(n) ? 5 : 0;

                } else if (a.temperature === 'hot') {

                    s += !/iced|ice|frappe|cold/.test(n) ? 4 : 0;

                }


                // SWEETNESS PREFERENCE
                if (a.sweetness === 'high') {

                    s += /frappe|mocha|caramel|vanilla/.test(n)
                        ? 4
                        : 0;

                }

                if (a.sweetness === 'low') {

                    s += /americano|classic|flat white/.test(n)
                        ? 4
                        : 0;

                }


                // COFFEE STRENGTH PREFERENCE
                if (a.strength === 'strong') {

                    s += /americano|classic|espresso|mocha/.test(n)
                        ? 4
                        : 0;

                }

                if (a.strength === 'mild') {

                    s += /latte|cappuccino|flat white/.test(n)
                        ? 4
                        : 0;

                }

                if (a.strength === 'none') {

                    s += /matcha/.test(n) ? 9 : 0;

                }

                return s;

            };


            // =================================
            // 8.4 FIND THE BEST MATCH
            // =================================

            const winner = [...drinks].sort(
                (a, b) => score(b) - score(a)
            )[0];

            const box = document.getElementById('quiz-result');


            // =================================
            // 8.5 DISPLAY QUIZ RESULT
            // =================================

            if (!winner) {

                box.textContent =
                    'No available drinks at the moment.';

            } else {

                // Clear previous result
                box.replaceChildren();


                // Drink Name
                const h = document.createElement('h2');

                h.textContent = 'Your match: ' + winner.name;


                // Drink Image
                const img = document.createElement('img');

                img.src = winner.image;
                img.alt = winner.name;


                // Drink Description
                const desc = document.createElement('p');

                desc.textContent =
                    winner.description ||
                    'Made just for your taste.';


                // View Drink Button
                const link = document.createElement('a');

                link.href = 'drink.php?id=' + winner.id;
                link.className = 'btn dark';
                link.textContent = 'View drink & customize →';


                // Append Result Elements
                box.append(h, img, desc, link);

            }


            // =================================
            // 8.6 SHOW RESULT AND SCROLL
            // =================================

            box.hidden = false;

            box.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

        });

</script>


<!-- =========================================
     9. QUIZ NAVIGATION JAVASCRIPT
========================================= -->

<script src="assets/quiz-journey.js" defer></script>


<!-- =========================================
     10. FOOTER
========================================= -->

<?php include 'includes/footer.php'; ?>
