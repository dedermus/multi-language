<div class="dropdown">
    <a class="nav-link dropdown-toggle" href="#" role="button" id="dropdownMenuLanguage" data-bs-toggle="dropdown"
       aria-expanded="false">
        {{ strtoupper(config('app.locale')) }}
    </a>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLanguage">
        @foreach($languages as $key => $language)
            <li>
                <a class="dropdown-item language" href="#" data-id="{{$key}}">
                    {{$language}}
                    @if($key == $current)
                        <i class="icon-check float-right"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Получение CSRF-токена из мета-тега для обеспечения безопасности запросов
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Обработчик клика по элементам с классом "language"
        document.querySelectorAll('.language').forEach(element => {
            element.addEventListener('click', async function () {
                // Получение идентификатора языка из атрибута data-id
                const id = this.getAttribute('data-id');

                try {
                    // Выполнение POST-запроса для изменения языка
                    const response = await fetch("{{ admin_url('/locale') }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken, // Установка CSRF-токена в заголовок
                            'Content-Type': 'application/x-www-form-urlencoded' // Установка типа контента
                        },
                        body: new URLSearchParams({ _token: csrfToken, locale: id }) // Формирование тела запроса
                    });

                    // Проверка успешности ответа
                    if (response.ok) {
                        location.reload(); // Перезагрузка страницы при успешном изменении языка
                    } else {
                        console.error('Ошибка при изменении языка');
                    }
                } catch (error) {
                    console.error('Ошибка сети:', error); // Обработка ошибок сети
                }
            });
        });
    });
</script>
