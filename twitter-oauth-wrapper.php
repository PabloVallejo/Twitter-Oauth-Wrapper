<?php
/**
* Twitter wrapper 0.1
* Small wrapper on top of Abraham Williams Twitter's OAuth class
*
* MIT Licenced.
* (c) 2013, Pablo Vallejo - https://github.com/PabloVallejo
*/

/**
 * Include Abraham Williams Twitter's OAuth class
 */
include 'lib/TwitterOAuth/twitteroauth.php';


/**
* Twitter wrapper class
* Wrapper to interact with twitter's API.
*
* <code>
*
*		// Get authorize URL and take user to such URL.
*		$url = Twitter::authorize_url();
*
*		// Once user is redirected from authorize URL, get access token.
*		$access_token = Twitter::access_token();
*
*		// Create an OAuth object with the access token.
*		$twitter = Twitter::oauth( $access_token );
*
*		// Query Twitter's API.
*		$tweets = $twitter->get( 'statuses/user_timeline', array( 'screen_name' => 'githubstatus' ) );
*
*		// Publish a tweet.
*		$twitter->post( 'statuses/update', array( 'status' => 'My status description.' ) );
*
*		// Follow a user by username.
*		$twitter->post( 'friendships/create', array( 'screen_name' => 'codepen', 'follow' => true ) );
*
* </code>
*/
class Twitter {

	/**
	* Consumer key
	* @var str
	*/
	static $consumer_key = TWITTER_CONSUMER_KEY;

	/**
	* Consumer Secret
	* @var str
	*/
	static $consumer_secret = TWITTER_CONSUMER_SECRET;

	/**
	* Callback URL, where users are redirected after
	* authorization with twitter.
	* @var str
	*/
	var $oauth_callback = TWIITER_OAUTH_CALLBACK;


	/**
	* Get authorize URL, URL in twitter's sever where
	* users authorize our application. Additionally, get data can be passed.
	*
	* <code>
	*
	*		// Get authorize URL.
	*		Twitter::authorize_url();
	*
	*		// Optionally, add additional argument to authorize URL.
	*		Twitter::authorize_url( 'id', 10 );
	*
	*		// Add several arguments to authorize URL.
	*		Twitter::authorize_url( array( 'id' => 10, 'custom_arg' => 'my_value' ) );
	*
	* </code>
	*
	* @param { arr || str } array of "key" => "value" arguments or argument "key".
	* @param { int || str } arg value if first argument is argument name.
	* @return { str } Authorize URL.
	*/
	public static function authorize_url( $arg = null, $val = null ) {

		// Reset session
        session_start();
        session_destroy();

        // Start session
        session_start();

        // Make connection
        $conn = new TwitterOAuth( self::$consumer_key, self::$consumer_secret );

        // Add arguments
        $t = new self();
        $t->add_callback_arg( $arg, $val );

        // Get request token
        $request_token = $conn->getRequestToken( $t->oauth_callback );

        // Save Tokens in _SESSION
        $_SESSION[ 'oauth_token' ] = $token = $request_token[ 'oauth_token' ];
        $_SESSION[ 'oauth_token_secret' ] = $request_token[ 'oauth_token_secret' ];


        // Get Authorization link
        if ( $conn->http_code == 200 ) {

            // Url to request authorization
            $url = $conn->getAuthorizeURL( $token );
            return $url;
        }

        // Invalid http code.
        return false;
	}


	/**
	* Add a parameter or aparameters to the callback URL.
	*
	* <code>
	*
	*		// Create instance
	*		$t = new Twitter;
	*
	*		// Add additional argument to authorize URL.
	*		$t->add_callback_arg( 'id', 10 );
	*
	*		// Add several arguments to authorize URL.
	*		$t->add_callback_arg( array( 'id' => 10, 'type' => 'normal' ) );
	*
	* </code>
	*
	* @param { arr || str } array of "key" => "value" arguments or argument "key".
	* @param { int || str } arg value if first argument is argument name.
	* @return { bool } true on success, false otherwise.
	*/
	protected function add_callback_arg( $arg = null, $val = null ) {

		$query = false;

        // Single arg
        if ( is_string( $arg ) || is_numeric( $arg ) )
            if ( is_string( $val ) || is_numeric( $val ) )
                $query = self::add_query_arg( $arg, $val, $this->oauth_callback );


        // Array of args
        if ( is_array( $arg ) )
            $query = self::add_query_arg( $arg, $this->oauth_callback );

        if ( $query === false )
            return false;

        // Update callback
        $this->oauth_callback = $query;
        return true;

	}


	/**
	* Return an object with which we can query the Twitter API.
	*
	* <code>
	*
	*		// Get Twitter OAuth instance, with access token.
	*		$twitter = Twitter::oauth( $access_token );
	*
	*		// Get Twitter OAuth instance and take access token from _SESSION
	*		$twitter = Twitter::oauth();
	*
	*		// Query Twitter's API.
	*		$twitter->post( 'statuses/update', array( 'status' => 'My first status.' ) );
	*
	*		// Get the latest user tweets, given user "username".
	*		$twitter->get( 'statuses/user_timeline', array( 'screen_name' => 'githubstatus' ) );
	*
	* </code>
	*
	* @param { arr || null } Access token arr of null to take the one previusly set in _SESSION
    * @return { obj || false } Object instance or false if there were no SESSION vars
	*/
	public static function oauth( $access_token = null ) {

		// Start session
		session_id() ? null : session_start();

        if ( ! is_array( $access_token ) )
            $access_token = $_SESSION[ 'access_token' ];

        // Create an object for us to query twitter's API
        $querier = new TwitterOauth( self::$consumer_key, self::$consumer_secret
            , $access_token[ 'oauth_token' ], $access_token[ 'oauth_token_secret' ]
        );

        return $querier;

	}


	/**
	* Get access token in order for us to create Twitter's Oauth object
	* which allows to que query API.
	*
	* <code>
	*
	*		// Get access token when user is redirected from Twitter.
	*		$access_token = Twitter::access_token();
	*
	*		// The next step will be to create an OAuth objet so that
	*		// we can query the API.
	*		$twitter = Twitter::oauth( $access_token );
	*
	*		// Now, the API can be queried.
	*		$twitter->post( 'statuses/update', array( 'status' => 'My first status.' ) );
	*
	*		// Returns a collection of the most recent Tweets using user ID.
	*		$twitter->get( 'statuses/user_timeline', array( 'user_id', '512846496' ) );
	*
	* </code>
	*
	* @param { arr }
	* @return { arr } array of access token attributes.
	*/
	public static function access_token( $oauth_verifier = null ) {

		// Start session
        session_start();

        if ( is_null( $oauth_verifier ) )
            $oauth_verifier = $_REQUEST[ 'oauth_verifier' ];

        // Request access token
        $connection = new TwitterOAuth( self::$consumer_key, self::$consumer_secret
            , $_SESSION[ 'oauth_token' ], $_SESSION[ 'oauth_token_secret' ]
        );

        $access_token = $connection->getAccessToken( $oauth_verifier );

        return $access_token;
	}

	/**
	 * Add GET parameters to the given URL.
	 *
	 * <code>
	 *
	 *		// Assign arguments
	 * 		$args = array( 'id' => 1, 'name' => 'My name' );
	 *
	 * 		// Add arguments to URL
	 * 		$url = Twitter::add_query_arg( 'http://myurl.com', $args );
	 *
	 * </code>
	 *
	 * @param { str } url to add parameters to
	 * @param { arr } array of arguments to add
	 * @return { str } parsed url
	 */
	public static function add_query_arg( $url, $args ) {

		$query = http_build_query( $args );

		return parse_url( $url, PHP_URL_QUERY ) ? $url . '&' . $query
			: $url . '?' . $query;

	}

}


?>