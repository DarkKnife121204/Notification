# Notification Service

Микросервис массовой отправки Email и SMS уведомлений.

Сервис поддерживает:

- массовую отправку уведомлений;
- приоритезацию transactional и marketing сообщений;
- Kafka для асинхронной обработки;
- retry с backoff через Redis;
- идемпотентность запросов;
- историю изменения статусов сообщений;
- Email и SMS провайдеры;
- интеграционные тесты;
- Swagger/OpenAPI документацию.

## Стек

- PHP 8.4
- Laravel 12
- PostgreSQL
- Apache Kafka
- Redis
- Nginx
- Docker Compose
- Scramble OpenAPI
- Mailhog

## Запуск проекта
1. Клонировать репозиторий
```
git clone

cd notification-service
```
2. Создать .env
```
cp .env.example .env
```
3. Запустить проект
```
docker compose up -d --build
```
### Проект запускается одной командой через Docker Compose.

При старте backend выполняются миграции и seeder.

4. Проверить контейнеры
```
docker compose ps
```
## Доступные сервисы
### Сервис	Адрес
- API	http://localhost:8080
- Swagger / OpenAPI	http://localhost:8080/docs/api
- OpenAPI JSON	http://localhost:8080/docs/api.json
- Kafka UI	http://localhost:8081
- Mailhog	http://localhost:8025
## API
Создание массовой рассылки
POST /api/batch

### Полный URL:

http://localhost:8080/api/batch

Пример Email-запроса:
```
{
    "channel": "email",
    "priority": "transactional",
    "idempotency_key": "notification-email-001",
    "message": "Test email notification",
    "recipient_ids": [
        1,2
    ]
}
```
Пример SMS-запроса:
```
{
    "channel": "sms",
    "priority": "marketing",
    "idempotency_key": "notification-sms-001",
    "message": "Test SMS notification",
    "recipient_ids": [
        1,2
    ]
}
```
### Пример ответа:
```
{
    "data": {
        "id": 1,
        "channel": "email",
        "priority": "transactional",
        "message": "Test email notification",
        "idempotency_key": "notification-email-001",
        "messages_count": 2,
        "created_at": "2026-07-07T10:00:00.000000Z"
    }
}
```
## Получение истории уведомлений получателя
```
GET /api/recipients/{external_id}/messages
```
### Полный пример:
```
http://localhost:8080/api/recipients/1/messages
```
### Пример ответа:
```
{
    "data": [
        {
            "id": 1,
            "batch_id": 1,
            "channel": "email",
            "priority": "transactional",
            "status": "delivered",
            "message": "Test email notification",
            "attempts": 1,
            "sent_at": "2026-07-07T10:00:01.000000Z",
            "delivered_at": "2026-07-07T10:00:01.000000Z",
            "dropped_at": null,
            "error_message": null,
            "logs": [
                {
                    "from_status": null,
                    "to_status": "queued",
                    "created_at": "2026-07-07T10:00:00.000000Z"
                },
                {
                    "from_status": "queued",
                    "to_status": "sent",
                    "created_at": "2026-07-07T10:00:01.000000Z"
                },
                {
                    "from_status": "sent",
                    "to_status": "delivered",
                    "created_at": "2026-07-07T10:00:01.000000Z"
                }
            ]
        }
    ]
}
```
## Статусы сообщений
- queued — сообщение принято и ожидает отправки
- sent — сообщение передано провайдеру
- delivered — доставка подтверждена
- dropped — сообщение окончательно отброшено после неуспешных попыток

## Приоритезация

### Используются два Kafka topic:

- notifications.transactional
- notifications.marketing

Transactional сообщения обрабатываются отдельным consumer и не ожидают завершения marketing рассылок.
## Тесты

Запуск всех тестов:
```
docker exec -it notification_backend php artisan test
```
### Основные тестируемые сценарии:

- идемпотентность batch
- публикация сообщений в Kafka topics по приоритету
- обработка сообщения Kafka consumer
- вызов Email provider
- изменение статусов сообщений
- retry после ошибки провайдера
- перевод сообщения в dropped после 3 попыток
- сохранение retry в Redis
- backoff retry
## Остановка проекта
```
docker compose down
```
