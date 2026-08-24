// Layout Alpine Component
// Handles sidebar, dark mode, and responsive behavior

export function layout() {
    return {
        sidebarOpen: false,

        init() {
            this.applyTheme();
            this.$nextTick(() => {
                this.bindEvents();
            });
        },

        applyTheme() {
            const stored = localStorage.getItem('dark-mode');
            if (stored === 'true') {
                document.documentElement.classList.add('dark');
            } else if (stored === 'false') {
                document.documentElement.classList.remove('dark');
            }
        },

        bindEvents() {
            const sidebar = this.$el;
            const overlay = document.getElementById('sidebarOverlay');
            const hamburger = document.getElementById('hamburgerBtn');
            const darkToggle = document.getElementById('darkToggle');

            // Hamburger toggle
            if (hamburger) {
                hamburger.addEventListener('click', () => this.toggleSidebar());
            }

            // Overlay click to close
            if (overlay) {
                overlay.addEventListener('click', () => this.closeSidebar());
            }

            // Close on link/button click (mobile)
            sidebar.querySelectorAll('a, button[type="submit"]').forEach(el => {
                el.addEventListener('click', () => {
                    if (window.innerWidth < 1024) this.closeSidebar();
                });
            });

            // Resize handler
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) this.closeSidebar();
            });

            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.sidebarOpen) this.closeSidebar();
            });

            // Dark mode toggle
            if (darkToggle) {
                darkToggle.addEventListener('click', () => this.toggleDarkMode());
            }
        },

        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            this.updateOverlay();
        },

        closeSidebar() {
            this.sidebarOpen = false;
            this.updateOverlay();
        },

        updateOverlay() {
            const overlay = document.getElementById('sidebarOverlay');
            if (overlay) {
                overlay.classList.toggle('active', this.sidebarOpen);
            }
            const sidebar = document.getElementById('sidebar');
            if (sidebar) {
                sidebar.classList.toggle('open', this.sidebarOpen);
            }
        },

        toggleDarkMode() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('dark-mode', isDark);
        }
    };
}