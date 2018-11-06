<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'kattegatdykkerne_dk_db');

/** MySQL database username */
define('DB_USER', 'kattegatdyk_dk');

/** MySQL database password */
define('DB_PASSWORD', 'skriver1806');

/** MySQL hostname */
define('DB_HOST', 'mysql21.unoeuro.com');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '[F}l1Y1&e>yKfmrWfMCu_hNXvgZ~uHBs<*ZAM[UcEe|MD3H35mXn@,ZLez.e7z+t');
define('SECURE_AUTH_KEY',  'gDcpCEU`(}X=Ako~7&5;;Q=Sg_V%jgi!yKRPg=`p9*|io?#s%vTE?H|C|PEs8VN$');
define('LOGGED_IN_KEY',    'bb.`6P0@^ecm!m}j(^xV)y~;w]bF(+NIaHzB9$I#qZKth_J{8+P|ZwBG||n$q2Bk');
define('NONCE_KEY',        '*os0@9><FAhoZ,d6<m X7Z&,v@!%BiY@tC_@>5]&kxtd8.5!)]7zyF6)Q.`.2y.b');
define('AUTH_SALT',        '`Bv]=b_p9y)[~TDX*j#028MnL&m(1)J70NZlGw*!>#QM%75Ua=gP$K{F|6>3F@Z9');
define('SECURE_AUTH_SALT', 'bdq=uj1}W/:=hPsY[c/HizP(9?6:B^?;iICnhD`Ut;p_{NEa(k#?wNfY4W(6)~AZ');
define('LOGGED_IN_SALT',   'V.Z#,ZD;xjuADu_B[]YKZJaV&CrOZ7Z$<@nnYQ?7k>wG^w5=IZ|eaGql<-5F#Tt0');
define('NONCE_SALT',       '9RyWD[g8j&M*.~kZm41X$dB!J!bEV.[.jpk*D)dXRocz</zy]=wvzks{:18:/=4I');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
