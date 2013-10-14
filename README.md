Twitter-Aauth-Wrapper
=====================

Small wrapper for API v1.1 on top of Abraham Williams Twitter OAuth library to simplify the process of interacting with Twitter's API by usin fewer lines of code and not worrying about sessions or GET variables.


## Quick Start

Define your application `consumer_key`, `consumer_secret` and `oauth_callback` and then include the library.

```php

// Twitter application constants
define( 'TWITTER_CONSUMER_KEY', '<your-consumer-key>' );
define( 'TWITTER_CONSUMER_SECRET', '<your-consumer-secret>' );
define( 'TWIITER_OAUTH_CALLBACK', '<http://your-callback>' );

// Include wrapper
include 'twitter-oauth-wrapper.php';
```

## Usage


The process of getting access to the Twitter API has 4 steps:

**1. Get authorize url**
```php
<?php

	// Get authorize URL for the user to authorize the app on Twitter.
	$url = Twitter::authorize_url();

	// Or get URL with data added to the callback.
	$url = Twitter::authorize_url( array( 'post_id' => 10 ) );

```

**2. Get access token**

```php
<?php
	// Once user is redirected from authorize URL, get access token from URL.
	$access_token = Twitter::access_token();
```

**3. Get a connection object with which query Twitter's API**

```php
<?php

	// Create an OAuth object with the access token.
	$twitter = Twitter::oauth( $access_token );
```

**4. Query Twitter's API**

```php
<?php

	// Get tweets.
	$tweets = $twitter->get( 'statuses/user_timeline', array( 'screen_name' => 'githubstatus' ) );

	// Publish a tweet.
	$twitter->post( 'statuses/update', array( 'status' => 'My status description.' ) );

```


## Contributing

Pull requests and issue reports are very appreciated.
