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
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

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
define('AUTH_KEY',         'qCF6s65gPCL1NQBNjfvaHh2GpzhQv9NZ+khDxF4FjwmesJkYCbdMDPEZegu4+hyZPuTfVDlQWOaoCpOhUkSyWw==');
define('SECURE_AUTH_KEY',  'xeI0Ld5J/QcpwcV5k4uv4klIoHoeIuD0F9+9Pno7sed0fNzQvF0E6DjYooeejZ7czL05PtNc9jDL6ohidDwQzQ==');
define('LOGGED_IN_KEY',    'f8DB5USlAGQ6EFl6JWDDK435eaZm2KEhu0xK8vcp1qHkRnfpg5oOC/wsZgUgyhy1Xbwj3NKyocorUwX1EeTepQ==');
define('NONCE_KEY',        'kM8hiUPBsG0Nje0KOej/EdOqY3sYV8YOUdqBlT6huZwZyNtL6OyIggbeLRDMv1yd7BWdOLjMwqI8QWfgiAiwLw==');
define('AUTH_SALT',        'QeK9XE6W9bjrNgnqvAxNdQDwO+aHIC6fNnkxuA95SJZ/nCPke0rdepiuJUzksv5DdczIkA7smPYBPtUXY9Uifw==');
define('SECURE_AUTH_SALT', 'PqtZzbTUQp1SemMgF6oh0Bv0eV78LXNRRZKRkI+b/9AbDCRAyFXgpBgwejg6OIvMKwLaBT2zHyd6DZJJw+gTwQ==');
define('LOGGED_IN_SALT',   'XYf/R7ia3SO0qsDvM889i7+iQ6csh9irpPs0Vz/XCmCW6Q1rGj7jE1+ffebQOaY0TEgIEAb52Ca8FwBIHbqcAg==');
define('NONCE_SALT',       '9mhLMXiELm5zJtH/lfgn0mQbsT72/qEew083ZMpVZFp4TluU/ZxIQRjBaaFg9zvB/uMUN3cVQ6xHRDEBK+Etjw==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_djrkh7m0h0_';





/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
