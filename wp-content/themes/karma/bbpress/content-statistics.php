<?php

/**
 * Statistics Content Part
 *
 * @package bbPress
 * @subpackage Theme
 */

// Get the statistics
$stats = bbp_get_statistics(); ?>

<dl role="main">

	<?php do_action( 'bbp_before_statistics' ); ?>

	<div style="clear: both;">
		<dd>
			<strong><?php echo esc_html( $stats['user_count'] ); ?></strong>
		</dd>
		<dt><?php _e( 'Registered Users', 'bbpress' ); ?></dt>
	</div>

	<div style="clear: both;">
		<dd>
			<strong><?php echo esc_html( $stats['forum_count'] ); ?></strong>
		</dd>
		<dt><?php _e( 'Forums', 'bbpress' ); ?></dt>
	</div>

	<div style="clear: both;">
		<dd>
			<strong><?php echo esc_html( $stats['topic_count'] ); ?></strong>
		</dd>
		<dt><?php _e( 'Topics', 'bbpress' ); ?></dt>
	</div>

	<div style="clear: both;">
		<dd>
			<strong><?php echo esc_html( $stats['reply_count'] ); ?></strong>
		</dd>
		<dt><?php _e( 'Replies', 'bbpress' ); ?></dt>
	</div>

	<div style="clear: both;">
		<dd>
			<strong><?php echo esc_html( $stats['topic_tag_count'] ); ?></strong>
		</dd>
		<dt><?php _e( 'Topic Tags', 'bbpress' ); ?></dt>
	</div>

	<?php if ( !empty( $stats['empty_topic_tag_count'] ) ) : ?>

		<div style="clear: both;">
			<dd>
				<strong><?php echo esc_html( $stats['empty_topic_tag_count'] ); ?></strong>
			</dd>
			<dt><?php _e( 'Empty Topic Tags', 'bbpress' ); ?></dt>
		</div>

	<?php endif; ?>

	<?php if ( !empty( $stats['topic_count_hidden'] ) ) : ?>

		<div style="clear: both;">
			<dd>
				<strong>
					<abbr title="<?php echo esc_attr( $stats['hidden_topic_title'] ); ?>"><?php echo esc_html( $stats['topic_count_hidden'] ); ?></abbr>
				</strong>
			</dd>
			<dt><?php _e( 'Hidden Topics', 'bbpress' ); ?></dt>
		</div>

	<?php endif; ?>

	<?php if ( !empty( $stats['reply_count_hidden'] ) ) : ?>

		<div style="clear: both;">
			<dd>
				<strong>
					<abbr title="<?php echo esc_attr( $stats['hidden_reply_title'] ); ?>"><?php echo esc_html( $stats['reply_count_hidden'] ); ?></abbr>
				</strong>
			</dd>
			<dt><?php _e( 'Hidden Replies', 'bbpress' ); ?></dt>
		</div>

	<?php endif; ?>

	<?php do_action( 'bbp_after_statistics' ); ?>

</dl>

<?php unset( $stats );