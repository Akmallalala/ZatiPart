document.addEventListener('DOMContentLoaded', () => {

    // 1. SCROLL REVEAL ANIMATION

    const obs = new IntersectionObserver(entries =>
        entries.forEach(e => {

            if (e.isIntersecting) {
                e.target.classList.add('visible');
                obs.unobserve(e.target);
            }

        }),
        { threshold: .1 }
    );

    document.querySelectorAll('.reveal').forEach(e => {
        obs.observe(e);
    });


    // 2. CUSTOMER FAVOURITES SLIDER

    const track = document.querySelector('#favourites-track');

    document.querySelectorAll('[data-slide]').forEach(b => {

        b.addEventListener('click', () => {

            track?.scrollBy({
                left: Number(b.dataset.slide) * 330,
                behavior: 'smooth'
            });

        });

    });


    // 3. ADD DRINK TO CART USING AJAX

    document.querySelectorAll('[data-add-cart]').forEach(form => {

        form.addEventListener('submit', async e => {

            e.preventDefault();

            const btn = form.querySelector(
                '[type=submit],button:not([type])'
            );

            if (btn) {
                btn.disabled = true;
            }

            try {

                // Get submitted form data
                const data = new FormData(form);
                data.set('ajax', '1');

                // Send request to cart.php
                const r = await fetch(
                    form.getAttribute('action') || 'cart.php',
                    {
                        method: 'POST',
                        body: data,
                        credentials: 'same-origin'
                    }
                );

                if (!r.ok) {
                    throw Error('Unable to update cart');
                }

                const j = await r.json();


                // 3.1 DISPLAY CART NOTIFICATION

                const toast = document.querySelector('#cart-toast');

                if (toast) {

                    toast.textContent = j.message;
                    toast.classList.add('show');

                    setTimeout(() => {
                        toast.classList.remove('show');
                    }, 3200);

                } else {

                    alert(j.message);

                }


                // 3.2 UPDATE CART BADGE

                document.querySelectorAll('.cart-badge').forEach(el => {
                    el.textContent = j.count;
                });

            } catch (err) {

                alert('Unable to add this drink. Please try again.');

            } finally {

                if (btn) {
                    btn.disabled = false;
                }

            }

        });

    });


    // 4. UPDATE CART ITEM QUANTITY

    document.querySelectorAll('.qty-change').forEach(b => {

        b.addEventListener('click', () => {

            const form = b.closest('form');

            const input = form.querySelector(
                'input[name=quantity]'
            );

            input.value = Math.max(
                0,
                Math.min(
                    99,
                    Number(input.value) + Number(b.dataset.step)
                )
            );

            form.requestSubmit();

        });

    });

});


// 5. WELCOME DRINK QUIZ POPUP

document.addEventListener('DOMContentLoaded', () => {

    const offer = document.getElementById('welcome-quiz');

    // Show quiz popup once per browser session
    if (offer && !sessionStorage.getItem('brewsip-quiz-invited')) {

        sessionStorage.setItem('brewsip-quiz-invited', '1');

        setTimeout(() => {
            offer.showModal();
        }, 1100);

    }


    // 5.1 CLOSE QUIZ POPUP USING BUTTON

    offer?.querySelectorAll('[data-close-offer]').forEach(b => {

        b.addEventListener('click', () => {
            offer.close();
        });

    });


    // 5.2 CLOSE QUIZ POPUP BY CLICKING OUTSIDE

    offer?.addEventListener('click', e => {

        if (e.target === offer) {
            offer.close();
        }

    });

});


// 6. MOBILE NAVIGATION DROPDOWN

document.addEventListener('DOMContentLoaded', () => {

    const menuToggle = document.querySelector('.mobile-toggle');
    const navLinks = document.querySelector('#mobile-nav');

    if (!menuToggle || !navLinks) {
        return;
    }


    // OPEN AND CLOSE MOBILE MENU

    menuToggle.addEventListener('click', () => {

        const isOpen = navLinks.classList.toggle('open');

        menuToggle.setAttribute(
            'aria-expanded',
            String(isOpen)
        );

        const icon = menuToggle.querySelector('i');

        if (icon) {

            icon.className = isOpen
                ? 'ri-close-line'
                : 'ri-menu-line';

        }

    });


    // CLOSE MENU WHEN NAVIGATION LINK IS CLICKED

    navLinks.querySelectorAll('a').forEach(link => {

        link.addEventListener('click', () => {

            navLinks.classList.remove('open');

            menuToggle.setAttribute(
                'aria-expanded',
                'false'
            );

            const icon = menuToggle.querySelector('i');

            if (icon) {
                icon.className = 'ri-menu-line';
            }

        });

    });


    // CLOSE MENU WHEN CLICKING OUTSIDE

    document.addEventListener('click', e => {

        if (
            !menuToggle.contains(e.target) &&
            !navLinks.contains(e.target)
        ) {

            navLinks.classList.remove('open');

            menuToggle.setAttribute(
                'aria-expanded',
                'false'
            );

            const icon = menuToggle.querySelector('i');

            if (icon) {
                icon.className = 'ri-menu-line';
            }

        }

    });


    // CLOSE MENU WHEN SCREEN SIZE CHANGES

    window.addEventListener('resize', () => {

        if (window.innerWidth > 768) {

            navLinks.classList.remove('open');

            menuToggle.setAttribute(
                'aria-expanded',
                'false'
            );

            const icon = menuToggle.querySelector('i');

            if (icon) {
                icon.className = 'ri-menu-line';
            }

        }

    });

});
