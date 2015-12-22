<?php

namespace Dropplets;

class PostManager {
    
    private $settings = null;
    
    public function __construct() {
        $this->settings = Settings::instance();
        $this->tools = new Tools();
    }
    
    public function invoke() {
        
		$blog_url = $this->settings->get ( 'blog_url' );
		$template_dir_url = $blog_url . '/templates/' . $this->settings->get ( 'template' ) . '/';
		$template_dir = 'templates/' . $this->settings->get ( 'template' ) . '/';
		$index_file = $template_dir . 'index.php';
        
        
        $content = $this->listPosts();
        
        $page_title = "Post Manager";
        $is_home = false;
        $page_meta = "";
        
		// Get the index template file.
		include_once $index_file;
    }
    
    
    
    public function listPosts() {
        $files = scandir($this->settings->posts_dir);
		ob_start ();
        ?>
        <div class="row">
            <table>
        <?php    
            foreach ($files as $file) {
                
                echo "<tr><td>".$file."</td></tr>";
            }
        ?>
            </table>
        </div>
        <?php 
		return ob_get_clean ();
    }
    
    
    
    
    public function editPost($file) {
        $files = scandir($this->settings->posts_dir);
        
        var_dump($files);
    }
    
    
    public function deletePost($file) {
    }
    
}