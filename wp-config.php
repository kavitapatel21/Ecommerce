<?php
define( 'WP_CACHE', false /* Modified by NitroPack */ );
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
define( 'DB_NAME', 'clgpro' );

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
define( 'AUTH_KEY',         '}qtp^%_GoSQ~=FA((]*_f3~;DMJBp2nyjr86+RFL<+MBY,hBF`,j_I3|%aZ#foD,' );
define( 'SECURE_AUTH_KEY',  '37Co~k.%=`[YU(CCmg;cywHOju=+;!*^sS@n:CS4MHEEk2;K,bPvj[`2)nd`cmUZ' );
define( 'LOGGED_IN_KEY',    '3_ZZT$$rm&=h$Vleq?I>n*Zj<qqRsq/2N(e2vV;c-8O].DC3PPX|P0*)&6=q[Tm+' );
define( 'NONCE_KEY',        '4)o37W^tL&|nMz/iOxX,!/b<`%HXIYBC8A{{&(Ou}f!<8L1-2YS(Wm42-Nxz65s=' );
define( 'AUTH_SALT',        'Gt!60Ib,|4LTWN0v5L.za>;ht=PPN&52Y4LdC)fIbv#p=~h+lsWCB;a6hAHzZGpW' );
define( 'SECURE_AUTH_SALT', 'T77l2dea=jk0hBz|YYnGdO{~}Uui5],|oaE=%/wc!a@&Tt>|[pbL?3Z(G}o[4`qh' );
define( 'LOGGED_IN_SALT',   ';4JG+`Edi|ojp?rj1Rjxt>2t_aCe>mhM^$5%l$xme9[{b<)fw^(O:Zl!P[Z9$pX4' );
define( 'NONCE_SALT',       'NV}h%{>!Apsdl74=onXWKgpvLqD-GDI rL!-<bd=Ym7$1gafa9s(xFi2/ w0H(D3' );

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
