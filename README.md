# service-counter

Не было сделано, но необходимо:
1. Редис кластер, для работы с несколькими инстансами приложения, в текущей реализации Редис сингл (как демо вариант)
2. Добавить систему обработки параллельных запросов для одного `country_code`, класть в очередь или блокировку через `redlock`
3. Добавить `rate-лимиты` на уровне `nginx`
4. Обработку на лимитов на уровне проложения (для конкретных юзеров, например через IP)
5. Добавить прелоад, сейчас контейнер уже компилится
6. Добавить Lazy load для некоторых сервисов в DI
7. Тесты и всякие линтеры

## Запуск
```
docker-compose up -d
```

Стартует на дефолтном порту

