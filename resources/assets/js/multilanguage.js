class MultiLanguage {
    static instance = null;

    constructor() {
        if (MultiLanguage.instance) {
            return MultiLanguage.instance;
        }

        // Пробуем получить CSRF токен из meta-тега
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Если нет в meta, пробуем из cookie (стандартный Laravel способ)
        if (!this.csrfToken) {
            this.csrfToken = this.getCsrfFromCookie();
        }

        this.preloader = document.getElementById('preloader');
        this.localeUrl = window.localeUrl || '/admin/locale';
        this.formData = new Map();
        this.activeElement = null;

        this.init();
        MultiLanguage.instance = this;

        // console.log('🔍 CSRF Token source:', {
        //     fromMeta: !!document.querySelector('meta[name="csrf-token"]')?.content,
        //     fromCookie: !!this.getCsrfFromCookie(),
        //     final: this.csrfToken ? 'found' : 'missing'
        // });
    }

    // Получить CSRF токен из cookie XSRF-TOKEN
    getCsrfFromCookie() {
        const cookie = document.cookie.split(';').find(c => c.trim().startsWith('XSRF-TOKEN='));
        if (cookie) {
            return decodeURIComponent(cookie.split('=')[1]);
        }
        return null;
    }

    init() {
        this.bindEvents();
        this.restoreFormData();
    }

    bindEvents() {
        // Обработка формы логина
        const form = document.getElementById('myForm');
        if (form) {
            form.addEventListener('submit', () => this.showPreloader());
        }

        // Обработка select на странице логина
        const localeSelect = document.getElementById('locale');
        if (localeSelect) {
            localeSelect.addEventListener('change', (e) => {
                this.changeLocale(e.target.value, true);
            });
        }

        // Привязываем обработчики к элементам меню
        this.attachClickHandlers();
    }

    attachClickHandlers() {
        document.querySelectorAll('.language').forEach(element => {
            // Удаляем старый обработчик если есть
            if (element._mlHandler) {
                element.removeEventListener('click', element._mlHandler);
            }

            // Создаем новый обработчик
            const handler = (e) => {
                e.preventDefault();
                e.stopPropagation();

                const locale = element.dataset.id;
                const direction = element.dataset.direction;

                this.activeElement = element;
                element.classList.add('loading');

                if (direction) {
                    document.documentElement.dir = direction;
                }

                this.changeLocale(locale, false);
            };

            element._mlHandler = handler;
            element.addEventListener('click', handler);
        });
    }

    async changeLocale(locale, saveFormData = true) {
        if (!locale) return;

        this.showPreloader();

        try {
            const formData = new FormData();
            formData.append('locale', locale);

            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            // Добавляем CSRF токен если он есть
            if (this.csrfToken) {
                headers['X-CSRF-TOKEN'] = this.csrfToken;
            }

            const response = await fetch(this.localeUrl, {
                method: 'POST',
                headers: headers,
                body: formData,
                credentials: 'same-origin' // Важно для передачи cookie
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            if (this.activeElement) {
                this.activeElement.classList.remove('loading');
            }

            window.location.reload();

        } catch (error) {
            console.error('Locale change failed:', error);

            if (this.activeElement) {
                this.activeElement.classList.remove('loading');
            }

            this.hidePreloader();
            alert('Failed to change language');
        }
    }

    showPreloader() {
        if (this.preloader) {
            this.preloader.style.display = 'block';
            this.preloader.style.opacity = '1';
        }
    }

    hidePreloader() {
        if (this.preloader) {
            this.preloader.style.opacity = '0';
            setTimeout(() => {
                this.preloader.style.display = 'none';
            }, 300);
        }
    }

    restoreFormData() {
        // Для страницы логина
        const saved = sessionStorage.getItem('multi-language-form');
        if (saved) {
            try {
                const data = JSON.parse(saved);
                Object.entries(data).forEach(([key, value]) => {
                    const input = document.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = value;
                    }
                });
                sessionStorage.removeItem('multi-language-form');
            } catch (e) {}
        }
    }

    static init() {
        return new MultiLanguage();
    }
}

// Автоматическая инициализация
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => MultiLanguage.init());
} else {
    MultiLanguage.init();
}
