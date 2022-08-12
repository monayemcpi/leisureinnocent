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
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */


define( 'DB_NAME', 'leisureinnocent_website' );
/** MySQL database username */
define( 'DB_USER', 'leisureinnocent_website_admin' );
/** MySQL database password */
define( 'DB_PASSWORD', '(xy#x0x*w_$s' );
/** MySQL hostname */
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
define( 'AUTH_KEY',         'v{!/jPr0h^^BJc^#7{[r-T=R)hhry}[RvE,)q{KV5aR@$ra)OlO&2`smczvK~beA' );
define( 'SECURE_AUTH_KEY',  'V{7LYlQFx4.k%`9Tp-1pK~$VbYGhN;rIYIU6;PSlAYDL;bGh_Jev+Bjav,;Kz>yw' );
define( 'LOGGED_IN_KEY',    'V`EsVk&5pM#A&0fAPB9.`>.m<|3`:5U/dD+KnU[pE>DnGc$j^{NAuw}eqqG<-IoW' );
define( 'NONCE_KEY',        'H0U,RvhbE>f|H&: )PY)~?f85*z}LZ8}#fj<Wc3eJ1}2oti<]!QNNGfoth;ftpnr' );
define( 'AUTH_SALT',        '.5Pyp0BDDq7zemul{41cg9)/,pLhV2L.:%Y&VG%LIo(A!(b-LshEfR`GL_zR/4vx' );
define( 'SECURE_AUTH_SALT', 'nxqBp9IL;U@X*EwS}7AjZI3I0F74KL}@O77hESm!C!(Y_^/yHq:UuP3Se]zpS~M%' );
define( 'LOGGED_IN_SALT',   'plTpNQw2_1Vu>VQ,:H&bidkI7C.I^C<)62]PSUz<Aw}sJbs63h=-i:R,VV-HCkDi' );
define( 'NONCE_SALT',       'HY@E6F2l;[rIl4`u~TcNld$&.)8_^G_Y&&waw~T/;>UF<3I$sv)JGUdAd*cLZ`XQ' );
/**#@-*/
/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'innocent_';
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