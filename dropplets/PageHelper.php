<?php

namespace Dropplets;

class PageHelper {
    
    private $settings = null;
    private $posts = null;
    
    public function __construct() {
        $this->settings = Settings::instance();
		$this->posts = new PostHelper ();
    }
    
    
    public function homepage($category = null) {
	$index_cache = $this->settings->get ( 'index_cache' );
        
        
		$page = (isset ( $_GET ['page'] ) && is_numeric ( $_GET ['page'] ) && $_GET ['page'] > 1) ? $_GET ['page'] : 1;
		$offset = ($page == 1) ? 0 : ($page - 1) * $posts_per_page;
		
		// Index page cache file name, will be used if index_cache = "on"
		
		$cachefile = $this->settings->config ( "cache_dir" ) . ($category ? $category : "index") . $page . '.html';
		
		// If index cache file exists, serve it directly wihout getting all posts
		if (file_exists ( $cachefile ) && $index_cache == 'true') {
			
			// Get the cached post.
			include $cachefile;
			exit ();
			
			// If there is a file for the selected permalink, display and cache the post.
		}
        
$homepage = parse_url($this->settings->get('blog_url'), PHP_URL_PATH);

// Get the current page.    
$currentpage  = $_SERVER["REQUEST_URI"];

// If is home.
$is_home = ($homepage==$currentpage);
define('IS_HOME', $is_home);
		
		if ($category) {
			$all_posts = $this->posts->get_posts_for_category ( $category );
		} else {
			$all_posts = $this->posts->get_all_posts ();
		}
		
		$posts_per_page = ($this->settings->get ( 'posts_per_page' ) != "") ? $this->settings->get ( 'posts_per_page' ) : 0;
		if ($posts_per_page > 0) {
			$pagination = ($this->settings->get ( 'pagination_on_off' ) != "off") ? get_pagination ( $page, round ( count ( $all_posts ) / $posts_per_page ) ) : "";
			define ( 'PAGINATION', $pagination );
		}
		$posts = ($this->settings->get ( 'pagination_on_off' ) != "off") ? array_slice ( $all_posts, $offset, ($posts_per_page > 0) ? $posts_per_page : null ) : $all_posts;
		
		if ($posts) {
			ob_start ();
			$content = '';
			foreach ( $posts as $post ) {
				
				// Get the post title.
				$post_title = str_replace ( array (
						"\n",
						'<h1>',
						'</h1>' 
				), '', $post ['post_title'] );
				
				// Get the post author.
				$post_author = $post ['post_author'];
				
				// Get the post author twitter id.
				$post_author_twitter = $post ['post_author_twitter'];
				
				// Get the published ISO date.
				$published_iso_date = $post ['post_date'];
				
				// Generate the published date.
				$date_format = $this->settings->get ( 'date_format' );
				$published_date = strftime ( $date_format, strtotime ( $published_iso_date ) );
				
				// Get the post category.
				$post_category = $post ['post_category'];
				
				// Get the post category link.
				$blog_url = $this->settings->get ( 'blog_url' );
				$post_category_link = $blog_url . 'category/' . urlencode ( trim ( strtolower ( $post_category ) ) );
				
				// Get the post status.
				$post_status = trim ( strtolower ( $post ['post_status'] ) );
				
				// Get the post intro.
				$post_intro = $post ['post_intro'];
				
				// Get the post content
				$post_content = $post ['post_content'];
				
				// Get the post link.
				if ($category) {
					$post_link = trim ( strtolower ( $post_category ) ) . '/' . str_replace ( $this->settings->get ( 'file_ext' ), '', $post ['fname'] );
				} else {
					$post_link = $blog_url . str_replace ( $this->settings->get ( 'file_ext' ), '', $post ['fname'] );
				}
				
				// Get the post image url.
				$post_image = $this->posts->get_post_image_url ( $post ['fname'] ) ?  : $this->posts->get_twitter_profile_img ( $post_author_twitter );
				
				if ($post_status == 'draft')
					continue;
					
					// Get the milti-post template file.
				$posts_file = 'templates/' . $this->settings->get ( 'template' ) . '/posts.php';
				include $posts_file;
			}
			echo $content;
			$content = ob_get_contents ();

            // Variables for template
            $blog_url = $this->settings->get ( 'blog_title' );
            $intro_title = $this->settings->get ( 'intro_title' );
            $intro_text = $this->settings->get ( 'intro_text' );
            $blog_url = $this->settings->get ( 'blog_url' );
            $blog_email = $this->settings->get ( 'blog_email' );
            $blog_title = $this->settings->get ( 'blog_title' );
            $blog_twitter = $this->settings->get ( 'blog_twitter' );
			$page_title = $blog_title;
			
			$blog_twitter = $this->settings->get ( 'blog_twitter' );
			
			$meta_description = $this->settings->get ( 'meta_description' );
			$blog_url = $this->settings->get ( 'blog_url' );
			
			$blog_image = 'https://api.twitter.com/1/users/profile_image?screen_name=' . $blog_twitter . '&size=bigger';
			
			// Get the page description and author meta.
			$get_page_meta [] = '<meta name="description" content="' . $meta_description . '">';
			$get_page_meta [] = '<meta name="author" content="' . $blog_title . '">';
			
			// Get the Twitter card.
			$get_page_meta [] = '<meta name="twitter:card" content="summary">';
			$get_page_meta [] = '<meta name="twitter:site" content="' . $blog_twitter . '">';
			$get_page_meta [] = '<meta name="twitter:title" content="' . $blog_title . '">';
			$get_page_meta [] = '<meta name="twitter:description" content="' . $meta_description . '">';
			$get_page_meta [] = '<meta name="twitter:creator" content="' . $blog_twitter . '">';
			$get_page_meta [] = '<meta name="twitter:image:src" content="' . $blog_image . '">';
			$get_page_meta [] = '<meta name="twitter:domain" content="' . $blog_url . '">';
			
			// Get the Open Graph tags.
			$get_page_meta [] = '<meta property="og:type" content="website">';
			$get_page_meta [] = '<meta property="og:title" content="' . $blog_title . '">';
			$get_page_meta [] = '<meta property="og:site_name" content="' . $blog_title . '">';
			$get_page_meta [] = '<meta property="og:url" content="' . $blog_url . '">';
			$get_page_meta [] = '<meta property="og:description" content="' . $meta_description . '">';
			$get_page_meta [] = '<meta property="og:image" content="' . $blog_image . '">';
			
			// Get all page meta.
			$page_meta = implode ( "\n", $get_page_meta );
			
			ob_end_clean ();
		} else {
			ob_start ();
			
			// Define the site title.
			$page_title = $error_title;
			$page_meta = '';
			
			// Get the 404 page template.
			include $not_found_file;
			
			// Get the contents
			$content = ob_get_contents ();
			
			// Flush the buffer so that we dont get the page 2x times
			ob_end_clean ();
		}
		ob_start ();
		
		$blog_url = $this->settings->get ( 'blog_url' );
		$template_dir_url = $blog_url . '/templates/' . $this->settings->get ( 'template' ) . '/';
		$template_dir = 'templates/' . $this->settings->get ( 'template' ) . '/';
		
		$index_file = $template_dir . 'index.php';
		
		// Get the index template file.
		ob_start ();
		include_once $index_file;
		$html = ob_get_clean ();
		echo $html;
		
		// Now that we have the whole index page generated, put it in cache folder
		if ($index_cache != 'off') {
			$fp = fopen ( $cachefile, 'w' );
			fwrite ( $fp, ob_get_contents () );
			fclose ( $fp );
		}
        
    }
    
    
    public function rss($type = null) {
        
        
		($type == 'rss') ? $feed = new \FeedWriter\RSS2 () : $feed = new \FeedWriter\ATOM ();
		
        // Variables for template
        $blog_title = $this->settings->get ( 'blog_title' );
        $blog_url = $this->settings->get ( 'blog_url' );
        $blog_email = $this->settings->get ( 'blog_email' );
        $meta_description = $this->settings->get ( 'meta_description' );
        $language = $this->settings->get ( 'language' );
        $feed_max_items = $this->settings->get ( 'feed_max_items' );

        if (!$feed_max_items) $feed_max_items = 10;
        if (!$language) $language = "en-gb";
        
		$feed->setTitle ( $blog_title );
		$feed->setLink ( $blog_url );
		
		if ($type == 'rss') {
			$feed->setDescription ( $meta_description );
			$feed->setChannelElement ( 'language', $language );
			$feed->setChannelElement ( 'pubDate', date ( DATE_RSS, time () ) );
		} else {
			$feed->setChannelElement ( 'author', $blog_title . ' - ' . $blog_email );
			$feed->setChannelElement ( 'updated', date ( DATE_RSS, time () ) );
		}
		
		$posts = $this->posts->get_all_posts ();
		
		if ($posts) {
			$c = 0;
			
			foreach ( $posts as $post ) {
				if ($c < $feed_max_items) {
					$item = $feed->createNewItem ();
					
					// Remove HTML from the RSS feed.
					$item->setTitle ( substr ( $post ['post_title'], 4, - 6 ) );
					$item->setLink ( rtrim ( $blog_url, '/' ) . '/' . str_replace ( $this->settings->get ( 'file_ext' ), '', $post ['fname'] ) );
					$item->setDate ( $post ['post_date'] );
					
					// Remove Meta from the RSS feed.
					$remove_metadata_from = file ( rtrim ( $this->settings->posts_dir, '/' ) . '/' . $post ['fname'] );
					
					if ($type == 'rss') {
						$item->addElement ( 'author', $blog_email . ' (' . str_replace ( '-', '', $remove_metadata_from [1] ) . ')' );
						$item->addElement ( 'guid', rtrim ( $blog_url, '/' ) . '/' . str_replace ( $this->settings->get ( 'file_ext' ), '', $post ['fname'] ) );
					}
					
					// Remove the metadata from the RSS feed.
					unset ( $remove_metadata_from [0], $remove_metadata_from [1], $remove_metadata_from [2], $remove_metadata_from [3], $remove_metadata_from [4], $remove_metadata_from [5] );
					$remove_metadata_from = array_values ( $remove_metadata_from );
					
					$item->setDescription ( \Michelf\Markdown::defaultTransform ( implode ( $remove_metadata_from ) ) );
					
					$feed->addItem ( $item );
					$c ++;
				}
			}
		}
		echo $feed->generateFeed ();
    }
    
}