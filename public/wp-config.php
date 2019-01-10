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

// ** MySQL settings ** //

// local.php lives in the 'public' folder
if (file_exists(dirname(__FILE__) . '/local.php')) {
	// Local DB Settings
	define( 'DB_NAME', 'local' );
	define( 'DB_USER', 'root' );
	define( 'DB_PASSWORD', 'root' );
	define( 'DB_HOST', 'localhost' );
} else {
	// Live DB Settings - siteground.com (web host) creditentials
	define( 'DB_NAME', 'ericp153_universitydata' );
	define( 'DB_USER', 'ericp153_wp251' );
	define( 'DB_PASSWORD', '37(_f]QM=IaC' );
	define( 'DB_HOST', 'localhost' );
}


/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '+AjAV9pbXLUClPVB073oFcCWyhGcjeB6C2Nha6tL76+ZrjTfNfCJKYh15uHrV3Aa6lLz80ULhWkvaEjnaOAtzQ==');
define('SECURE_AUTH_KEY',  'rKKn/ZG6c0MJW7uLU/9swpj80vc7Ot4O8k5wWdiWBERrl0ScDuoaqs6vlL8Vfznp3brqOo6zAgO78j17gb9qMg==');
define('LOGGED_IN_KEY',    'mRb9wYN5dhXuaVRNIKi+KH5YHV14gWv9fLUGXl+aBQjJmHm0Kha2nKLJwVnblcb5gWUGISRZFFZMVE/89UpCJw==');
define('NONCE_KEY',        'UJa3+qafLyLVYiq6uZSyFVL2I6yTI7JDanRMGfhIs0Smk0Cf7zF9yGkCefemp2Q3lPZ9287xd40qdoXOmuggfw==');
define('AUTH_SALT',        'DaA1ppEfDLXrQ13uKOS77CAb56fOCptmGKMpfJ5d2BR8YQGYB6Bs5d1OalKwO3XiT7TZSQC+mfm+hqYQSIcalA==');
define('SECURE_AUTH_SALT', 'frh9nMv46CE1Vfb+Wwis7t585GsXMKc7CyLCDK9fxXMg+b9Hy8WnSJVFn3h+dHxHuxL/7Gvp/nWu6pejJDh13A==');
define('LOGGED_IN_SALT',   'SZXuUJrnoSVT6y9LzV1r1tFux6Ka7+bZJLXqmuUOm7hNyr6cFyQdEJ3tl75b1PkTOpp8sCjWghr0YcrEYKCcpA==');
define('NONCE_SALT',       '3gcpNTLaBrAzgWa9tgKlKSbb6NOu1ofKaoMhyUq9OvcEzKU1lvJ1E290hzmTYQkM4Ji3lKbcxA21GFm1aRLXHQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
