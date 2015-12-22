<?php

namespace Dropplets;
require_once ('./vendor/autoload.php');

use \Mibe\FeedWriter\RSS2;
use \Dropplets\SavePreferences;
use \Dropplets\PageHelper;
use \Dropplets\PostManager;

session_start ();

/* ----------------------------------------------------------------------------------- */
/*
 * If There's a Config Exists, Continue
 * /*-----------------------------------------------------------------------------------
 */

$settings = Settings::instance ();
$settings->init ();

$category = NULL;
$url = (isset($_GET ['filename']))? $_GET ['filename'] :"";
$filename = null;


if ($url == 'rss' || $url == 'atom' || $url == 'postman') {
	$filename = $_GET ['filename'];
} else if ($url == 'dropplets/save') {
	$save = new \Dropplets\SavePreferences ();
	$save->save ();
	exit ();
} elseif ($url !== "") {
	
	// Filename can be /some/blog/post-filename.md We should get the last part only
	$filename = explode ( '/', $url );
	
	// File name could be the name of a category
	if (count ( $filename ) > 1) {
		if ($filename [count ( $filename ) - 2] == "category") {
			$category = $filename [count ( $filename ) - 1];
			$filename = null;
		}
	} else {
		// Individual Post
		$filename = $settings->posts_dir . $filename [count ( $filename ) - 1] . $settings->get ( 'file_ext' );
	}
}


    $page = new PageHelper();


if (file_exists ( './config.ini' )) {
	
	/* ----------------------------------------------------------------------------------- */
	/*
	 * Get Settings & Functions
	 * /*-----------------------------------------------------------------------------------
	 */
	
	include ('./dropplets/functions.php');
	
	$index_cache = $settings->get ( 'index_cache' );
	$post_cache = $settings->get ( 'post_cache' );
    $is_home = false;
	
	/* ----------------------------------------------------------------------------------- */
	/*
	 * The Home Page (All Posts)
	 * /*-----------------------------------------------------------------------------------
	 */
    
	if ($filename == NULL) {
        if ($category)
        $page->homepage($category);
        else
        $page->homepage();
    }
	
    if ($filename == 'rss' || $filename == 'atom') {
        $page->rss($filename);
	} 	
    
    elseif ($filename == 'postman') {
        $postMan = new PostManager();
        $postMan->invoke();
	} 	

	/* ----------------------------------------------------------------------------------- */
	/*
	 * Single Post Pages
	 * /*-----------------------------------------------------------------------------------
	 */
	
	else {
		ob_start ();
		
		// Define the post file.
		$fcontents = file ( $filename );
		$slug_array = explode ( "/", $filename );
		$slug_len = count ( $slug_array );
		
		// This was hardcoded array index, it should always return the last index.
		$slug = str_replace ( array (
				$settings->get ( 'file_ext' ) 
		), '', $slug_array [$slug_len - 1] );
		
		// Define the cached file.
		$cachefile = $settings->config ( "cache_dir" ) . $slug . '.html';
		
		// If there's no file for the selected permalink, grab the 404 page template.
		if (! file_exists ( $filename )) {
			
			// Change the cache file to 404 page.
			$cachefile = CACHE_DIR . '404.html';
			
			// Define the site title.
			$page_title = $error_title;
			
			// Get the 404 page template.
			include $not_found_file;
			
			// Get the contents.
			$content = ob_get_contents ();
			
			// Flush the buffer so that we dont get the page 2x times.
			ob_end_clean ();
			
			// Start new buffer.
			ob_start ();
			
			// Get the index template file.
			// include_once $index_file;
			
			// Cache the post on if caching is turned on.
			if ($settings->get ( 'post_cache' ) != 'off') {
				$fp = fopen ( $cachefile, 'w' );
				fwrite ( $fp, ob_get_contents () );
				fclose ( $fp );
			}
			
			// If there is a cached file for the selected permalink, display the cached post.
		} else if (file_exists ( $cachefile )) {
			
			// Define site title
			$page_title = str_replace ( '# ', '', $fcontents [0] );
			
			// Get the cached post.
			include $cachefile;
			
			exit ();
			
			// If there is a file for the selected permalink, display and cache the post.
		} else {
			
			// Variables for template
			$blog_url = $settings->get ( 'blog_title' );
			$intro_title = $settings->get ( 'intro_title' );
			$intro_text = $settings->get ( 'intro_text' );
			$blog_url = $settings->get ( 'blog_url' );
			$blog_email = $settings->get ( 'blog_email' );
			$blog_title = $settings->get ( 'blog_title' );
			$blog_twitter = $settings->get ( 'blog_twitter' );
			$date_format = $settings->get ( 'date_format' );
			
			$template_dir_url = $blog_url . '/templates/' . $settings->get ( 'template' ) . '/';
			$template_dir = 'templates/' . $settings->get ( 'template' ) . '/';
			
			$index_file = $template_dir . 'index.php';
			
			// Get the post title.
			$post_title = \Michelf\Markdown::defaultTransform ( $fcontents [0] );
			$post_title = str_replace ( array (
					"\n",
					'<h1>',
					'</h1>' 
			), '', $post_title );
			
			// Get the post intro.
			$post_intro = htmlspecialchars ( trim ( $fcontents [7] ) );
			
			// Get the post author.
			$post_author = str_replace ( array (
					"\n",
					'-' 
			), '', $fcontents [1] );
			
			// Get the post author Twitter ID.
			$post_author_twitter = str_replace ( array (
					"\n",
					'- ' 
			), '', $fcontents [2] );
			
			// Get the published date.
			$published_iso_date = str_replace ( '-', '', $fcontents [3] );
			
			// Generate the published date.
			$published_date = strftime ( $date_format, strtotime ( $published_iso_date ) );
			
			// Get the post category.
			$post_category = str_replace ( array (
					"\n",
					'-' 
			), '', $fcontents [4] );
			
			// Get the post status.
			$post_status = str_replace ( array (
					"\n",
					'- ' 
			), '', $fcontents [5] );
			
			// Get the post category link.
			$post_category_link = $blog_url . 'category/' . urlencode ( trim ( strtolower ( $post_category ) ) );
			
			// Get the post link.
			$post_link = $blog_url . str_replace ( array (
					$settings->get ( 'file_ext' ),
					$settings->posts_dir 
			), '', $filename );
			
			// Get the post image url.
		          $postHelper = new PostHelper ();
			$post_image = $postHelper->get_post_image_url ( $filename ) ?  : $postHelper->get_twitter_profile_img ( $post_author_twitter );
			
			// Get the post content
			$file_array = array_slice ( file ( $filename ), 7 );
			$post_content = \Michelf\Markdown::defaultTransform ( trim ( implode ( "", $file_array ) ) );
			
			// free memory
			unset ( $file_array );
			
			// Get the site title.
			$page_title = trim ( str_replace ( '# ', '', $fcontents [0] ) );
			
			// Generate the page description and author meta.
			$get_page_meta [] = '<meta name="description" content="' . $post_intro . '">';
			$get_page_meta [] = '<meta name="author" content="' . $post_author . '">';
			
			// Generate the Twitter card.
			$get_page_meta [] = '<meta name="twitter:card" content="summary">';
			$get_page_meta [] = '<meta name="twitter:site" content="' . $blog_twitter . '">';
			$get_page_meta [] = '<meta name="twitter:title" content="' . $page_title . '">';
			$get_page_meta [] = '<meta name="twitter:description" content="' . $post_intro . '">';
			$get_page_meta [] = '<meta name="twitter:creator" content="' . $post_author_twitter . '">';
			$get_page_meta [] = '<meta name="twitter:image:src" content="' . $post_image . '">';
			$get_page_meta [] = '<meta name="twitter:domain" content="' . $post_link . '">';
			
			// Get the Open Graph tags.
			$get_page_meta [] = '<meta property="og:type" content="article">';
			$get_page_meta [] = '<meta property="og:title" content="' . $page_title . '">';
			$get_page_meta [] = '<meta property="og:site_name" content="' . $page_title . '">';
			$get_page_meta [] = '<meta property="og:url" content="' . $post_link . '">';
			$get_page_meta [] = '<meta property="og:description" content="' . $post_intro . '">';
			$get_page_meta [] = '<meta property="og:image" content="' . $post_image . '">';
			
			// Generate all page meta.
			$page_meta = implode ( "\n\t", $get_page_meta );
			
			// Generate the post.
			$post = \Michelf\Markdown::defaultTransform ( join ( '', $fcontents ) );
			
			$post_file = $template_dir . 'post.php';
			
			// Get the post template file.
			include $post_file;
			
			$content = ob_get_contents ();
			ob_end_clean ();
			ob_start ();
			
			// Get the index template file.
			include_once $index_file;
			
			// Cache the post on if caching is turned on.
			if ($post_cache == 'true') {
				$fp = fopen ( $cachefile, 'w' );
				fwrite ( $fp, ob_get_contents () );
				fclose ( $fp );
			}
		}
	}
	
	/* ----------------------------------------------------------------------------------- */
	/*
	 * Run Setup if No Config
	 * /*-----------------------------------------------------------------------------------
	 */
} else {
    $install = new InstallHelper();
    $install->start();
}
