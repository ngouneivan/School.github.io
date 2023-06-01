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
define( 'DB_NAME', 'school' );

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
define( 'AUTH_KEY',         'UegE(4#V|pRQ[$1sB+u|*y!!o3Q3.L2@cc}K!H4NzbUY*?_$4O%!|dz>&Jveww,N' );
define( 'SECURE_AUTH_KEY',  'Z4tMQ/M~]Xk|M|qt~^d3WF|sUo3rxVC$n%)dYp_X{c$4lrhbfvqBYj~p?!p?nnKl' );
define( 'LOGGED_IN_KEY',    '@|+08`@k`>ZB>}>A{*S#K,j]*7N(*)-d>G}rF7hUd;/&i_$,*p6#?$hiaH^E3->@' );
define( 'NONCE_KEY',        'I2l5:(N^V-A`B}`i_T9;G4JtJTT3|P4S);+t!0qU6pxb:a:#~gbUM})7A;@l^cwi' );
define( 'AUTH_SALT',        '?(W(nWvp+W3O>j[{MDaK6/c6AXV4[6+ig1NyrRUVSs+azXh(T6f_BEd(b1OZ6=R+' );
define( 'SECURE_AUTH_SALT', '(a@?;[C?9f}ynhh`]%q&k,>:GW`+{%#Tx[u~;4K-d<i;s|$i0V]MGY<W),Ug6_p ' );
define( 'LOGGED_IN_SALT',   '5g:xS/m7`^.-JT)3PCk,MiQ`f<M=^2uQ27g5g=z9.G`/C (ru(=-jj=;_tq~~p0Q' );
define( 'NONCE_SALT',       ')t#@:#p]hC.DI;zpr~|O~nx9lgy&g@y4C}.UW2chfy+W]CU g*L_[.G;6]02g+?w' );

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
