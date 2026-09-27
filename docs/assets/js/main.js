document.addEventListener("DOMContentLoaded", function() {
    // Mobile navigation: bottom sheet
    var mobileToggle = document.querySelector('.mobile-toggle');
    var navLinks = document.querySelector('.nav-links');
    var navBackdrop = document.getElementById('nav-backdrop');
    var sheetClose = document.getElementById('sheet-close');

    function openMenu() {
        if (!navLinks) return;
        navLinks.classList.add('open');
        if (navBackdrop) navBackdrop.classList.add('open');
        document.body.style.overflow = 'hidden';
        if (mobileToggle) mobileToggle.innerHTML = '<i class="fas fa-times"></i>';
    }

    function closeMenu() {
        if (!navLinks) return;
        navLinks.classList.remove('open');
        if (navBackdrop) navBackdrop.classList.remove('open');
        document.body.style.overflow = '';
        if (mobileToggle) mobileToggle.innerHTML = '<i class="fas fa-bars"></i>';
    }

    if (mobileToggle && navLinks) {
        mobileToggle.addEventListener('click', function() {
            if (navLinks.classList.contains('open')) {
                closeMenu();
            } else {
                openMenu();
            }
        });

        if (navBackdrop) navBackdrop.addEventListener('click', closeMenu);
        if (sheetClose) sheetClose.addEventListener('click', closeMenu);

        // Close menu when clicking a link
        navLinks.querySelectorAll('.nav-link:not(#ministries-trigger):not(#finance-trigger), .dropdown-content a, .btn').forEach(link => {
            link.addEventListener('click', closeMenu);
        });

        // Mobile Dropdown Toggles
        const dropdownTriggers = [document.getElementById('ministries-trigger'), document.getElementById('finance-trigger')];
        dropdownTriggers.forEach(trigger => {
            if (trigger) {
                trigger.addEventListener('click', function(e) {
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const parent = this.parentElement;
                        parent.classList.toggle('active');
                    }
                });
            }
        });
    }

    // Dynamic 3-Image Slider for public pages (.hero-premium)
    var heroPremiumElements = document.querySelectorAll('.hero-premium');
    heroPremiumElements.forEach(function(heroPremium) {
        // Skip if this section already has a custom slider (like home.php)
        if (heroPremium.querySelector('.hero-slider-wrapper')) {
            return;
        }

        // Clear any inline background for transparency to show slider underneath
        heroPremium.style.background = 'transparent';
        heroPremium.style.position = 'relative';
        
        // Ensure inner container floats above slider
        var innerContainer = heroPremium.querySelector('.container');
        if (innerContainer) {
            innerContainer.style.position = 'relative';
            innerContainer.style.zIndex = '2';
        }

        // Selected sliding images (must exist in assets/images/other)
        var baseUrl = document.querySelector('link[rel="manifest"]')
            ? document.querySelector('link[rel="manifest"]').href.replace(/manifest\.json$/, '')
            : './';
        var slideImages = [
            baseUrl + 'assets/images/other/church.jpeg',
            baseUrl + 'assets/images/other/mass.jpeg',
            baseUrl + 'assets/images/other/homepage.jpg'
        ];

        var sliderWrapper = document.createElement('div');
        sliderWrapper.className = 'hero-slider-wrapper';
        sliderWrapper.style.zIndex = '0'; // Behind the container
        
        // Dark overlay to ensure text remains readable
        var overlay = document.createElement('div');
        overlay.style.position = 'absolute';
        overlay.style.inset = '0';
        overlay.style.background = 'linear-gradient(rgba(15,23,42,0.85), rgba(15,23,42,0.85))';
        overlay.style.zIndex = '1';
        sliderWrapper.appendChild(overlay);

        slideImages.forEach(function(src, index) {
            var slide = document.createElement('div');
            slide.className = 'hero-slide-3';
            slide.style.backgroundImage = 'url("' + src + '")';
            slide.style.animationDelay = (index * 5) + 's';
            sliderWrapper.appendChild(slide);
        });

        // Prepend slider wrapper
        heroPremium.insertBefore(sliderWrapper, heroPremium.firstChild);
    });

    /**
     * Global Spinner Loader Utilities
     */
    const injectLoader = () => {
        if (!document.getElementById('global-portal-loader')) {
            const overlay = document.createElement('div');
            overlay.id = 'global-portal-loader';
            overlay.className = 'loader-overlay';
            overlay.innerHTML = '<div class="loader"></div>';
            document.body.appendChild(overlay);
        }
    };

    window.showLoader = function() {
        injectLoader();
        document.getElementById('global-portal-loader').style.display = 'flex';
    };

    window.hideLoader = function() {
        const loader = document.getElementById('global-portal-loader');
        if (loader) loader.style.display = 'none';
    };

    // Auto-show loader on all form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            // Only show if the form doesn't have a special 'no-loader' attribute
            if (!this.hasAttribute('data-no-loader')) {
                window.showLoader();
            }
        });
    });

    /**
     * PWA Install Prompt Logic
     */
    let deferredPrompt;
    const installBtn = document.createElement('button');
    installBtn.id = 'pwa-install-btn';
    installBtn.innerHTML = '<i class="fas fa-download"></i> Install App';
    installBtn.style.cssText = `
        position: fixed;
        bottom: 100px;
        right: 20px;
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        display: none;
        z-index: 9998;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
    `;
    document.body.appendChild(installBtn);

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;
        // Update UI notify the user they can install the PWA
        installBtn.style.display = 'flex';

        installBtn.addEventListener('click', (e) => {
            // hide our user interface that shows our A2HS button
            installBtn.style.display = 'none';
            // Show the prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            deferredPrompt.userChoice.then((choiceResult) => {
                if (choiceResult.outcome === 'accepted') {
                    console.log('User accepted the A2HS prompt');
                } else {
                    console.log('User dismissed the A2HS prompt');
                }
                deferredPrompt = null;
            });
        });
    });

    window.addEventListener('appinstalled', (evt) => {
        console.log('St. Charles Lwanga Regiment Portal was installed');
        installBtn.style.display = 'none';
    });
});
