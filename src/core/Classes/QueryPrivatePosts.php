<?php
/**
 * @package     MultipleAuthors
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.1.0
 */

namespace MultipleAuthors\Classes;

use MultipleAuthors\Classes\Objects\Author;
use WP_Query;

/**
 * Modifications to the main query, and helper query methods
 *
 * Based on Bylines.
 *
 * @package MultipleAuthors\Classes
 */
class QueryPrivatePosts
{
    const REQUEST_QUERY_GROUP = 'QueryPrivatePosts:RequestQueryGroup';
    /**
     * Modify the WHERE clause on private posts query
     *
     * @param string $request
     * @param WP_Query $query Query object.
     *
     * @return string
     */
    public static function filter_posts_request($request, $query)
    {
        if (!is_admin() || !is_user_logged_in() || !isset($query->query['post_type'])) {
            return $request;
        }

        global $wpdb;

        $userId = get_current_user_id();

        if (false === strpos($request, "OR {$wpdb->posts}.post_author = $userId AND {$wpdb->posts}.post_status = ")) {
            return $request;
        }

        $author = Author::get_by_user_id($userId);

        if (empty($author) || is_wp_error($author)) {
            return $request;
        }

        $cacheKey = md5($request);
        $cachedRequest = wp_cache_get($cacheKey, self::REQUEST_QUERY_GROUP);
        if (false !== $cachedRequest) {
            return $cachedRequest;
        }
        // JOIN
        $join = " LEFT JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id )";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} ON ( {$wpdb->term_relationships}.term_taxonomy_id = {$wpdb->term_taxonomy}.term_taxonomy_id )";

        // 4.6+ uses a LEFT JOIN for tax queries so we need to check for both.
        $request = str_replace('FROM wp_posts  WHERE', "FROM wp_posts {$join} WHERE", $request);

        // WHERE
        $privateStatuses = get_post_stati(array('private' => true));

        foreach ((array)$privateStatuses as $status) {
            $stringToReplace = "OR {$wpdb->posts}.post_author = $userId AND {$wpdb->posts}.post_status = '$status'";

            if (substr_count($request, $stringToReplace)) {
                $ternsImplode = "OR ({$wpdb->posts}.post_author = $userId AND {$wpdb->posts}.post_status = '$status') ";
                $ternsImplode .= " OR ({$wpdb->term_taxonomy}.taxonomy = 'author' AND {$wpdb->term_taxonomy}.term_id = '{$author->term_id}' AND {$wpdb->posts}.post_status = '$status')";

                $query->authors_having_terms = " {$wpdb->term_taxonomy}.term_id = {$author->term_id}";

                $request = str_replace($stringToReplace, $stringToReplace . $ternsImplode, $request);
            }
        }

        // GROUP BY
        $having  = "MAX( IF ({$wpdb->term_taxonomy}.taxonomy = 'author', IF ( {$query->authors_having_terms},2,1 ),0 ) ) <> 1 ";
        $groupBy = "{$wpdb->posts}.ID HAVING {$having}";

        $request = str_replace(' ORDER BY', " GROUP BY {$groupBy} ORDER BY", $request);

        wp_cache_set($cacheKey, self::REQUEST_QUERY_GROUP);

        return $request;
    }
}
