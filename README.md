# API

Общее описание реализации

Реализована система принятия и обработки заявок пользователей через публичное API:

    Отправка заявки: Любой пользователь может отправить данные через публичное API. Для этого достаточно передать имя, email и текст заявки.
    Обработка заявки: Ответственное лицо имеет возможность просматривать список заявок, фильтровать их по статусу и дате создания.
    Ответ на заявку: Ответственное лицо может установить статус заявки как "Завершено", оставив обязательный комментарий. После обработки пользователь получает уведомление с ответом на указанный email.


Установка

    Клонируйте репозиторий:

git clone https://your-repository-url.git
cd your-project-folder

Установите зависимости с помощью Composer:

    composer install

Настройте параметры подключения к базе данных в файле config/db.php.

Примените миграции для создания таблиы:

    php yii migrate

Запустите проект на сервере:

    php yii serve

Откройте API в браузере по адресу: http://localhost:8080.

в params.php можно настроить mailer . по дефолту он выключен в         'useFileTransport' => true,  в  web.php

    Токен : 1xFf3sjJWfccOCBZCk3yEC7vvdrj2tJRH0paDjQx5KO6MeiSbGWzVl8TH3VSJFfc
