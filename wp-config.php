<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', '220-21_97421' );

/** Имя пользователя базы данных */
define( 'DB_USER', '220-21_97421' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', 'ebb7537d1a6c273a8a69' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '($Y<00B`d/:cB/cJ7R841Bzq5 <AUr|Y%M=i]$E0 @&dN)b(l_]S8z^5!GR/Mj[l' );
define( 'SECURE_AUTH_KEY',  '1E9rvkrd*tVCK_4VNiqJ;_0Klj0_G,;D_]_T BxSZ15H<gBo|GH.w>Rg KCgDN4D' );
define( 'LOGGED_IN_KEY',    'P{B(fUa~@6}S-efpLIFKgz0`%LQ}FitT<!Ee!dS7xJHMO$D%3y-ST;_1P=wxI4(b' );
define( 'NONCE_KEY',        '_^y:(*+n,9+z#{1MlAgV]QikHc#mxii|X=X2w BYjj7{.8>xZqdC>@#]c9*79rT[' );
define( 'AUTH_SALT',        'n~JZc;aHFBo/r_rn=/=S*k;a||ca3+1zQ)?zJZkJ$C6THY,Rn2.PoLAISi}E< hY' );
define( 'SECURE_AUTH_SALT', '5uz<dcxKh,GgA+KaOi{0whVp-TeKtQ]$X$NV2W7K=5wo9KHNOgNc%=f0d{RKf,U3' );
define( 'LOGGED_IN_SALT',   'u>uB<E63<0e`o^yVy(^!ayI-g/EYeHE_QGi:]R6R!Im7KEgU&n}p,~c[DHfP:79_' );
define( 'NONCE_SALT',       '-H^3WVMb|vYvM%5n9*PVNTHduqJeL0>HuHw3 2;>MLZF6d.jR|qw2alGCbR8WUY~' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = '6kQSn_';


/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';