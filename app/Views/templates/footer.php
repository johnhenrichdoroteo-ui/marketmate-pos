    </main>
    <footer class="site-footer">
        <div class="container">
            <p>MarketMate POS · A clearer way to manage the day.</p>
        </div>
    </footer>
    <script>
        const menuToggle = document.querySelector('.menu-toggle');
        const primaryNavigation = document.querySelector('#primary-navigation');

        menuToggle?.addEventListener('click', () => {
            const isOpen = menuToggle.getAttribute('aria-expanded') === 'true';
            menuToggle.setAttribute('aria-expanded', String(!isOpen));
            primaryNavigation?.classList.toggle('is-open', !isOpen);
        });
    </script>
</body>
</html>
