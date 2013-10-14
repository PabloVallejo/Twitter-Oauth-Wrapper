<?php

/**
 * Define constants
 */
define( 'TWITTER_CONSUMER_KEY', '' );
define( 'TWITTER_CONSUMER_SECRET', '' );
define( 'TWIITER_OAUTH_CALLBACK', '' );

/**
 * Get twitter wrapper
 */
include 'twitter-wrapper.php';

// Autorize URL
$authorize_url = empty($_GET ) ? Twitter::authorize_url() : '';

// Get access token once were redirected from twitter
$access_token = empty( $_GET ) ? '' : Twitter::access_token();

// Get twitter querier
$twitter = empty( $_GET ) ? '' : Twitter::oauth( $access_token );

// Get Tweets
$tweets = empty( $_GET ) ? '' : $twitter->get( 'statuses/user_timeline', array( 'screen_name' => 'githubstatus' ) );

?>

<!DOCTYPE html>
<html>
<head>
	<title>Twitter</title>
	<style type="text/css">
		body {
			background: #ccc;
		}

		.container {
			margin: 70px auto;
			width: 930px;
			padding: 15px;
			min-height: 400px;
			background: #f8f8f8;
			border-radius: 2px;
			box-shadow: 0 1px 2px rgba(0,0,0,.1);
		}

		ul {
			list-style: none;
		}
		li {
			border-bottom: 1px solid #ccc;
		}
		li:last-child {
			border-bottom: none;
		}

		h4 span {
			color: #aaa;
		}

	</style>
</head>
<body>

	<div class="container">

		<?php if ( empty( $_GET ) ): ?>

			<a href="<?php echo $authorize_url; ?>">Authorize Application</a>
		<?php else: ?>
			<h1>Tweets from <strong>GitHub Status</strong></h1>
			<ul>

				<?php foreach ( $tweets as $tweet ): ?>
					<?php $date = new DateTime( $tweet->created_at ); ?>
					<li>
						<h4><?php echo $tweet->user->name; ?> - <span><?php echo $date->format( 'M d' );  ?></span></h4>
						<p><?php echo $tweet->text; ?></p>
					</li>

				<?php endforeach ?>

			</ul>

		<?php endif; ?>

	</div>

</body>
</html>