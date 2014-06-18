Qiwi Gate Emulator
=========

Сервер эмулирует работу сервера QIWI через REST протокол.
Обращаться к серверу QIWI можно используя пакеты эмуляции интернет магизина и SDK:

- QIWI-shop: https://github.com/fintech-fab/qiwi-shop
- QIWI-SDK: https://github.com/fintech-fab/qiwi-sdk

# Требования

- php >=5.3.0
- Laravel Framework >= 4.1.*
- MySQL Database
- Laravel queue driver configuration
- User auth identifier in your web project

# Используется

- bootstrap cdn
- jquery cdn

# Установка

## Composer

Только пакет:

    {
        "require": {
        	"fintech-fab/qiwi-gate": "dev-master"
    },
    }

Пакет с зависимостями:

    {
        "require": {
	        "php": ">=5.4.0",
	        "laravel/framework": ">=4.1",
            "fintech-fab/qiwi-gate": "dev-master"
        },
	    "require-dev": {
		    "phpunit/phpunit": "4.3.*@dev"
	    },
    }

Запустите:

	composer update
	php artisan dump-autoload

## Локальные настройки

Добавьте service provider в `config/app.php`:

	'providers' => array(
		'FintechFab\QiwiGate\QiwiGateServiceProvider'
	)

### Соединение для очереди назовите  'ff-qiwi-gate', например в iron:

Добавьте в `config/#env#/queue.php`:

```PHP
'connections' => array(
	'ff-qiwi-gate' => array(
		'driver'  => 'iron',
		'project' => 'your-iron-project-id',
		'token'   => 'your-iron-token',
		'queue'   => 'your-iron-queue',
	),
),
```

Запустите обработчик очередей:

	php artisan queue:listen --queue="ff-qiwi-gate" ff-qiwi-gate


### Соединение с базой данных назовите 'ff-qiwi-gate'

Добавьте в `config/#env#/database.php`:

```PHP
'connections' => array(
	'ff-qiwi-gate' => array(
		'driver'    => 'mysql',
		'host'      => 'your-mysql-host',
		'database'  => 'your-mysql-database',
		'username'  => 'root',
		'password'  => 'your-mysql-password',
		'charset'   => 'utf8',
		'collation' => 'utf8_unicode_ci',
		'prefix'    => 'your-table-prefix',
	),

),
```

## Миграции

Выполните миграции базы:

	php artisan migrate --package="fintech-fab/qiwi-gate" --database="ff-qiwi-gate"

### Получение id пользователя для авторизации:

По умолчанию id пользователя определяется `Auth::user()->getAuthIdentifier()`.
Вы можете установить целочисленное значение (например `'user_id' => 1`), или использовать какую-то вашу функцию
определения id пользователя.

Для этого опубликуйте настройки из пакета:

	php artisan config:publish --path=vendor/fintech-fab/qiwi-gate/src/config fintech-fab/qiwi-gate

И измените настройки получения id пользователя для вашего проекта `app/config/packages/fintech-fab/qiwi-gate/config.php`:

	'user_id' => 'user-auth-identifier',

## Использование

Теперь пакет полностью готов к работе.

Подробнее о работе пакета -  /qiwi/gate/about

Аккаунт пользователя  - /qiwi/gate/account

Таблица счетов - /qiwi/gate/account/billsTable


## Для разработчиков

### Workbench migrations

	php artisan migrate:reset --database="ff-qiwi-gate"
	php artisan migrate --bench="fintech-fab/qiwi-gate" --database="ff-qiwi-gate"

	php artisan migrate:reset --database="ff-qiwi-gate" --env="testing"
	php artisan migrate --bench="fintech-fab/qiwi-gate" --database="ff-qiwi-gate" --env="testing"

### Package migrations

	php artisan migrate:reset --database="ff-qiwi-gate"
	php artisan migrate --package="fintech-fab/qiwi-gate" --database="qiwi-gate"

	php artisan migrate:reset --database="ff-qiwi-gate" --env="testing"
	php artisan migrate --package="fintech-fab/qiwi-gate" --database="ff-qiwi-gate" --env="testing"

### Workbench publish

	php artisan config:publish --path=workbench/fintech-fab/qiwi-gate/src/config fintech-fab/qiwi-gate


