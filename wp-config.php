<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'bubaonli_wor1');

/** MySQL database username */
define('DB_USER', 'bubaonli_wor1');

/** MySQL database password */
define('DB_PASSWORD', 'H2G0ujrd');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '(e}[Km_d5>$nFAJX-*%^=]-@5CN/}-K1]K+~X{E7iect@|+Dv($++<++EInL|SHs');
define('SECURE_AUTH_KEY',  ';6MD.9G[LNq--i>)Fi&t6$PSv.(pO,ubIPB*#]!l7-#qn&TzFthbksFZRffT(VRS');
define('LOGGED_IN_KEY',    'SH/OlHi8HJ#yzgQbSL4o3Ts+y5p5En>{w75}?K-0K/^17E5- m%zdf02*w2Zvke:');
define('NONCE_KEY',        'ljXR3%RK3&$fd.+/wck&lYC`N.MnF~N{yB2xjlZ4gqr<<v7%Jx>PG:P!G&XwmVa0');
define('AUTH_SALT',        'Ry}^B.L$!u<-ZNJe^U{WAac|WA!5^RS&aERE,g^%Q$kWycuAqKqi:?0{p-51d.m!');
define('SECURE_AUTH_SALT', 'g.?TO.x=21|o:EFntyBccQ=9sYw<X[qyv04rl`|M7+yPp6mQ0H)=C_);%GoK|kv2');
define('LOGGED_IN_SALT',   '$m;uxl9wG@~i!9vh+x+ [>._++C#jYj%w5ERH?[JX!+>g8}1,4DA(-Pj5[0DbI:@');
define('NONCE_SALT',       'f,/QF:BwxaswZE=-dP9A/BI,n|}edi_q1+[(VpaUe3akzK$c.>Y]JD6MJXvM`_@w');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'xir_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
