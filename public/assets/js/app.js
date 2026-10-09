(() => {
    const menuButton = document.querySelector('.menu-toggle');
    const navigation = document.querySelector('#primary-navigation');
    if (menuButton && navigation) {
        const closeMenu = () => {
            menuButton.setAttribute('aria-expanded', 'false');
            menuButton.setAttribute('aria-label', 'Open menu');
            navigation.classList.remove('is-open');
        };
        menuButton.addEventListener('click', () => {
            const open = menuButton.getAttribute('aria-expanded') !== 'true';
            menuButton.setAttribute('aria-expanded', String(open));
            menuButton.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
            navigation.classList.toggle('is-open', open);
        });
        navigation.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMenu));
        document.addEventListener('keydown', event => { if (event.key === 'Escape') closeMenu(); });
        document.addEventListener('click', event => {
            if (!navigation.contains(event.target) && !menuButton.contains(event.target)) closeMenu();
        });
        window.matchMedia('(min-width: 1141px)').addEventListener('change', closeMenu);
        window.matchMedia('(min-width: 801px)').addEventListener('change', () => {
            if (document.body.classList.contains('site-body--guest')) closeMenu();
        });
    }

    const search = document.querySelector('[data-catalog-search]');
    if (search) {
        const cards = [...document.querySelectorAll('[data-product-name]')];
        const emptyMessage = document.querySelector('[data-search-empty]');
        search.addEventListener('input', () => {
            const query = search.value.trim().toLocaleLowerCase();
            let visible = 0;
            cards.forEach(card => {
                const matches = card.dataset.productName.includes(query);
                card.hidden = !matches;
                if (matches) visible += 1;
            });
            if (emptyMessage) emptyMessage.hidden = visible !== 0;
        });
    }

    const productSelect = document.querySelector('[data-sale-product]');
    const quantityInput = document.querySelector('[data-sale-quantity]');
    if (productSelect && quantityInput) {
        const money = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP' });
        const productLabel = document.querySelector('[data-preview-product]');
        const quantityLabel = document.querySelector('[data-preview-quantity]');
        const stockLabel = document.querySelector('[data-preview-stock]');
        const totalLabel = document.querySelector('[data-preview-total]');
        const updatePreview = () => {
            const selected = productSelect.selectedOptions[0];
            const price = Number(selected?.dataset.price || 0);
            const stock = selected?.dataset.stock;
            const quantity = Math.max(0, Number(quantityInput.value) || 0);
            if (productLabel) productLabel.textContent = selected?.value ? selected.textContent.split(' · ')[0] : 'Not selected';
            if (quantityLabel) quantityLabel.textContent = String(quantity);
            if (stockLabel) stockLabel.textContent = stock === undefined ? '—' : `${stock} units`;
            if (totalLabel) totalLabel.textContent = money.format(price * quantity);
        };
        productSelect.addEventListener('change', updatePreview);
        quantityInput.addEventListener('input', updatePreview);
        updatePreview();
    }

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', event => {
            if (! window.confirm(form.dataset.confirm)) event.preventDefault();
        });
    });

    if ('IntersectionObserver' in window && ! window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const sections = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });
        sections.forEach(section => observer.observe(section));
        document.documentElement.classList.add('has-motion');
    }
})();
