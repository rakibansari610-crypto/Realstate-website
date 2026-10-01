/* ==========================================================================
   VSERVE REAL ESTATE - MAIN JAVASCRIPT CONTROLLER
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sticky Navbar Shrink Effect
    const navbar = document.querySelector('.navbar-custom');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 40) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // 2. Country Switcher Logic (India / UAE)
    window.toggleCountryDropdown = function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }
        const menus = document.querySelectorAll('.country-dropdown-menu');
        menus.forEach(menu => menu.classList.toggle('show'));
    };

    window.switchCountry = function(code, name, flag) {
        document.querySelectorAll('#selectedCountryName').forEach(el => el.innerText = name);
        document.querySelectorAll('#selectedCountryFlag').forEach(el => el.innerText = flag);

        // Close all dropdown menus
        document.querySelectorAll('.country-dropdown-menu').forEach(menu => menu.classList.remove('show'));

        // Save preference
        localStorage.setItem('vserve_country', JSON.stringify({ code, name, flag }));
        
        // Show notification toast
        if (typeof showToast === 'function') {
            showToast(`Country switched to ${name} ${flag}`);
        }
    };

    // Close country dropdown on outside click
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.country-dropdown-wrapper')) {
            document.querySelectorAll('.country-dropdown-menu').forEach(menu => menu.classList.remove('show'));
        }
    });

    // Load saved country if available
    const savedCountry = localStorage.getItem('vserve_country');
    if (savedCountry) {
        try {
            const parsed = JSON.parse(savedCountry);
            document.querySelectorAll('#selectedCountryName').forEach(el => el.innerText = parsed.name);
            document.querySelectorAll('#selectedCountryFlag').forEach(el => el.innerText = parsed.flag);
        } catch (e) {
            console.error(e);
        }
    }

    // 3. Search Bar Filter Tabs Switcher
    const searchTabs = document.querySelectorAll('.search-tab-btn');
    searchTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            searchTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const category = tab.getAttribute('data-category');
            console.log('Search category set to:', category);
        });
    });

    // 4. Dynamic Property Filter Logic
    const filterCategorySelect = document.getElementById('filterCategory');
    const filterLocationSelect = document.getElementById('filterLocation');
    const filterBedsSelect = document.getElementById('filterBeds');
    const propertyCards = document.querySelectorAll('.property-card-wrapper');

    function applyPropertyFilters() {
        if (!propertyCards.length) return;

        const selectedCat = filterCategorySelect ? filterCategorySelect.value.toLowerCase() : 'all';
        const selectedLoc = filterLocationSelect ? filterLocationSelect.value.toLowerCase() : 'all';
        const selectedBeds = filterBedsSelect ? filterBedsSelect.value : 'all';

        propertyCards.forEach(card => {
            const cardCat = (card.getAttribute('data-category') || '').toLowerCase();
            const cardLoc = (card.getAttribute('data-location') || '').toLowerCase();
            const cardBeds = card.getAttribute('data-beds') || '';

            const matchesCat = (selectedCat === 'all' || cardCat.includes(selectedCat));
            const matchesLoc = (selectedLoc === 'all' || cardLoc.includes(selectedLoc));
            const matchesBeds = (selectedBeds === 'all' || cardBeds === selectedBeds);

            if (matchesCat && matchesLoc && matchesBeds) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    if (filterCategorySelect) filterCategorySelect.addEventListener('change', applyPropertyFilters);
    if (filterLocationSelect) filterLocationSelect.addEventListener('change', applyPropertyFilters);
    if (filterBedsSelect) filterBedsSelect.addEventListener('change', applyPropertyFilters);

    // 5. Contact / Inquiry Form Handler
    const inquiryForms = document.querySelectorAll('.inquiry-form');
    inquiryForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn ? btn.innerHTML : 'Submit';
            
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            }

            setTimeout(() => {
                showToast('Thank you! Your inquiry has been submitted successfully. Our team will contact you shortly.');
                form.reset();
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }

                // Close bootstrap modal if open
                const modalElem = form.closest('.modal');
                if (modalElem && window.bootstrap) {
                    const modalInstance = bootstrap.Modal.getInstance(modalElem);
                    if (modalInstance) modalInstance.hide();
                }
            }, 1200);
        });
    });

    // Helper Toast Notification System
    function showToast(message) {
        let toastContainer = document.getElementById('vserveToastContainer');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'vserveToastContainer';
            toastContainer.style.position = 'fixed';
            toastContainer.style.bottom = '30px';
            toastContainer.style.left = '30px';
            toastContainer.style.zIndex = '99999';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = 'vserve-toast';
        toast.style.background = '#0F172A';
        toast.style.color = '#FFFFFF';
        toast.style.borderLeft = '4px solid #C5A059';
        toast.style.padding = '14px 20px';
        toast.style.borderRadius = '8px';
        toast.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';
        toast.style.marginBottom = '10px';
        toast.style.fontSize = '0.95rem';
        toast.style.fontWeight = '600';
        toast.style.display = 'flex';
        toast.style.alignItems = 'center';
        toast.style.gap = '10px';
        toast.style.transition = 'all 0.3s ease';

        toast.innerHTML = `<i class="fas fa-check-circle" style="color: #C5A059;"></i> <span>${message}</span>`;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    }

    // 6. Section Toggle Filter Handlers (Featured Properties & Projects)
    const propFilterBtns = document.querySelectorAll('.prop-filter-btn');
    const propCards = document.querySelectorAll('.featured-prop-wrapper');

    propFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            propFilterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');

            propCards.forEach(card => {
                const category = card.getAttribute('data-category') || '';
                if (filter === 'all' || category.includes(filter)) {
                    card.style.display = 'block';
                    card.classList.remove('animate-fade-in');
                    void card.offsetWidth; // trigger reflow
                    card.classList.add('animate-fade-in');
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    const projectFilterBtns = document.querySelectorAll('.project-filter-btn');
    const projectCards = document.querySelectorAll('.featured-project-wrapper');

    projectFilterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            projectFilterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const filter = btn.getAttribute('data-filter');

            projectCards.forEach(card => {
                const category = card.getAttribute('data-category') || '';
                if (filter === 'all' || category.includes(filter)) {
                    card.style.display = 'block';
                    card.classList.remove('animate-fade-in');
                    void card.offsetWidth; // trigger reflow
                    card.classList.add('animate-fade-in');
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
