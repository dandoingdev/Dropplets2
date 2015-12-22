<?php

namespace Dropplets;

class SavePreferences {
	
	// File locations.
	private $settings_file = "./config.ini";
    
	private function settings_format($name, $value) {
		return sprintf ( "%s = \"%s\"", $name, $value );
	}
    
	public function save() {
		if ($_POST ["submit"] == "submit" && (! file_exists ( $this->settings_file ) || isset ( $_SESSION ['user'] ))) {
			
			$hasher = new \Phpass\Hash ();
			
			$settings = Settings::instance ();
			
			$blog_email = $settings->get ( 'blog_email' );
			$blog_twitter = $settings->get ( 'blog_twitter' );
			$blog_url = $settings->get ( 'blog_url' );
			$blog_title = $settings->get ( 'blog_title' );
			$meta_description = $settings->get ( 'meta_description' );
			$intro_title = $settings->get ( 'intro_title' );
			$intro_text = $settings->get ( 'intro_text' );
			$template = $settings->get ( 'template' );
			$password = $settings->get ( 'password' );
			$header_inject = $settings->get ( 'header_inject' );
			$footer_inject = $settings->get ( 'footer_inject' );
			
			// Get submitted setup values.
			if (isset ( $_POST ["blog_email"] )) {
				$blog_email = $_POST ["blog_email"];
			}
			if (isset ( $_POST ["blog_twitter"] )) {
				$blog_twitter = $_POST ["blog_twitter"];
			}
			if (isset ( $_POST ["blog_url"] )) {
				$blog_url = $_POST ["blog_url"];
			}
			if (isset ( $_POST ["blog_title"] )) {
				$blog_title = $_POST ["blog_title"];
			}
			if (isset ( $_POST ["meta_description"] )) {
				$meta_description = $_POST ["meta_description"];
			}
			if (isset ( $_POST ["intro_title"] )) {
				$intro_title = $_POST ["intro_title"];
			}
			if (isset ( $_POST ["intro_text"] )) {
				$intro_text = $_POST ["intro_text"];
			}
			if (isset ( $_POST ["template"] )) {
				$template = $_POST ["template"];
			}
            
			if (isset ( $_POST ["file_ext"] )) {
				$file_ext = $_POST ["file_ext"];
			}
			
			// There must always be a $password, but it can be changed optionally in the
			// settings, so you might not always get it in $_POST.
			if (! isset ( $password ) || ! empty ( $_POST ["password"] )) {
				$password = $hasher->HashPassword ( $_POST ["password"] );
			}
			
			if (! isset ( $header_inject )) {
				$header_inject = "";
			}
			
			if (isset ( $_POST ["header_inject"] )) {
				$header_inject = addslashes ( $_POST ["header_inject"] );
			}
			
			if (! isset ( $footer_inject )) {
				$footer_inject = "";
			}
			
			if (isset ( $_POST ["footer_inject"] )) {
				$footer_inject = addslashes ( $_POST ["footer_inject"] );
			}
			
			// Output submitted setup values.
			$config [] = $this->settings_format ( "blog_email", $blog_email );
			$config [] = $this->settings_format ( "blog_twitter", $blog_twitter );
			$config [] = $this->settings_format ( "blog_url", $blog_url );
			$config [] = $this->settings_format ( "blog_title", $blog_title );
			$config [] = $this->settings_format ( "meta_description", $meta_description );
			$config [] = $this->settings_format ( "intro_title", $intro_title );
			$config [] = $this->settings_format ( "intro_text", $intro_text );
			$config [] = "password = '" . $password . "'";
			$config [] = $this->settings_format ( "header_inject", $header_inject );
			$config [] = $this->settings_format ( "footer_inject", $footer_inject );
			$config [] = $this->settings_format ( "template", $template );
			$config [] = $this->settings_format ( "file_ext", $file_ext );
			
			// Create the settings file.
			file_put_contents ( $this->settings_file, implode ( "\n", $config ) );
			
			// Redirect
			header ( "Location: " . $blog_url );
		}
	}
}
?>
