<?php

/*
 * Common functions between admin and user handling of the stories.
 *
 * In other words, building and/or retrieving the cache.
 *
 */

function storybuilder_do_cache( $shortname ) {
	// Always executes the query to return the story posts. Used to build the cache.
	$story = new WP_Query(
		array(
			'meta_key' => 'story_shortname',
			'meta_value' => $shortname
		)
	);

	return $story;
}
