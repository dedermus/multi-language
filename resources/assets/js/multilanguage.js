document.addEventListener('DOMContentLoaded', () => {
    // Получение CSRF-токена из скрытого input-поля для обеспечения безопасности запросов
    const csrfToken = document.querySelector('input[name="_token"]').value;
    const preloader = document.getElementById('preloader');
    const form = document.getElementById('myForm');

    // Перехват нажатия кнопки на форме при ее отправке
    form.addEventListener('submit', function (event) {
        // Показать лоадер при отправке формы
        preloader.style.display = 'block';
    });

    // Обработчик изменения языка
    document.getElementById('locale').addEventListener('change', async function () {
        const locale = this.value;
        // Сбор данных формы
        const formData = new FormData(document.querySelector('form')); // Предполагается, что форма обернута в <form>

        // Показать лоадер
        preloader.style.display = 'block';

        try {
            // Выполнение POST-запроса для изменения языка
            const response = await fetch(window.localeUrl, { // Использование глобальной переменной
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken, // Установка CSRF-токена в заголовок
                    'Content-Type': 'application/x-www-form-urlencoded' // Установка типа контента
                },
                body: new URLSearchParams([...formData, ['locale', locale]]) // Добавление локали к данным формы
            });

            // Проверка успешности ответа
            if (response.ok) {
                location.reload(); // Перезагрузка страницы при успешном изменении языка
            } else {
                console.error('Ошибка при изменении языка');
            }
        } catch (error) {
            console.error('Ошибка сети:', error); // Обработка ошибок сети
        } finally {
            // Скрыть лоадер
            preloader.style.display = 'none';
        }
    });
});
