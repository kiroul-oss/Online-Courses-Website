<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'online-courses' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '}P~R5SRrxZa[4(}smQM}Mg80+D-#+`Bl(J b6lVub!.}Fe+jyBJyMmtj[OI[I9Jk' );
define( 'SECURE_AUTH_KEY',  'S=_}}2<KQ@!vK2la}7F)A| xXx9}$!4mT{5[!S7eSeJ7y&Q78Go,63.|OF^iF!Z.' );
define( 'LOGGED_IN_KEY',    '4*9oN(i1@roj}td}[1>*LxA.REsDFk4MFo}):+-<mly[NU/HDBCQId(A<7)Mn]?*' );
define( 'NONCE_KEY',        '&.xxV~{PZvs#~$j?,K0%8pt;`sUoUtJwywHM+jEYi/ Xm8|UZ:05&`@[mM})Pgi@' );
define( 'AUTH_SALT',        '=yk,8$p#IO6<tKGC}c-^C.AC}$RLNCs|F0Z(m8KBgDs1 A@~g8[.PF0W`BwUHW+*' );
define( 'SECURE_AUTH_SALT', '6mE>;pIcJ+zXo_@&N.2_ypvf=jOs9zqAIXaQ3$?u/UPG8v^pp(JC&^Dx)#C}oQ@z' );
define( 'LOGGED_IN_SALT',   'qW&H0MdX5]u4bwieVDioy4(wGvpND3XEbe7#=13<Kv*wMBCX^R>,Fk/Ks}yX>6ga' );
define( 'NONCE_SALT',       'sii%J+>gY|P(7bBj33<2. k2KBwVWs2eT2<F<QA.TYTFn9-64!}#a ?h/K?x%#` ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
