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
        
        $action = (isset($_REQUEST['action'])) ? $_REQUEST['action'] : "";
        
        switch($action) {
            case "edit":
                $content = $this->editPost();
                break;
            default:
                $content = $this->listPosts();
        }
        
        $page_title = "Post Manager";
        $is_home = false;
        $page_meta = "";
        
		// Get the index template file.
		include_once $index_file;
    }
    
    
    
    public function listPosts() {
        
        $path = null;
        $timestamp = null;
        $dirname = $this->settings->posts_dir;
        $dir = new \DirectoryIterator($dirname);

		ob_start ();
        ?>
        <div class="row">
            <table>
        <?php    
            foreach ($dir as $fileinfo) {
                if (!$fileinfo->isDot()) {
                    if ($fileinfo->getMTime() > $timestamp) {
                        // current file has been modified more recently
                        // than any other file we've checked until now
                        $path = $fileinfo->getFilename();
                        $modified = $fileinfo->getMTime();
                        $created = $fileinfo->getCTime();
                        $linkUrl = preg_replace('/\\.[^.\\s]{1,4}$/', '', $path);

                        echo "<tr><td><a href='?action=edit&post=".$linkUrl."'>".$path."</a> - ".date("F d Y H:i:s.", $created)."</td></tr>";
                    }
                }
            }
        ?>
            </table>
        </div>
        <?php 
		return ob_get_clean ();
    }
    
    
    
    
    public function editPost() {
        
        $post = (isset($_REQUEST['post'])) ? $_REQUEST['post'] : "";
        $dirname = $this->settings->posts_dir;
        $file_ext = $this->settings->get("file_ext");
        
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $content = $_REQUEST['content'];
            if ($post && $content) {
                $file = new \SplFileObject($dirname . "/" . $post . $file_ext, "w");
                $written = $file->fwrite($content);
                $file = null;
            }
        }
        
        $file = new \SplFileObject($dirname . "/" . $post . $file_ext, "r"); //the file handler is created
        
        $postContent = "";
        while (!$file->eof()) $postContent .= $file->fgets();
        $file = null;
        
		ob_start ();
        ?>

<style>
    
    
html, body, #editor {
  margin: 0;
  height: 100%;
  font-family: 'Helvetica Neue', Arial, sans-serif;
  color: #333;
}

textarea, #editor div {
  display: inline-block;
  width: 49%;
  height: 100%;
  vertical-align: top;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 0 20px;
}

textarea {
  border: none;
  border-right: 1px solid #ccc;
  resize: none;
  outline: none;
  background-color: #f6f6f6;
  font-size: 14px;
  font-family: 'Monaco', courier, monospace;
  padding: 20px;
}

code {
  color: #f66;
}</style>



        <div class="">
        
        <script src="https://cdn.jsdelivr.net/vue/latest/vue.js"/></script>
        <script src="http://cdnjs.cloudflare.com/ajax/libs/marked/0.3.2/marked.min.js"/></script>
            
            
            <form action="?" method="POST">
                <input type="hidden" name="action" value="edit"/>
                <input type="hidden" name="post" value="<?php echo $post;?>"/>
            <div class="row">
            <div id="editor">
            <textarea v-model="input" debounce="300" name="content"><?php echo $postContent;?></textarea>
            <div v-html="input | marked"></div>
            </div>
            </div>
                <input type="submit" value="Save"/>
                </form>
        <script>
            new Vue({
  el: '#editor',
  data: {
    input: '# hello'
  },
  filters: {
    marked: marked
  }
})
            </script>
        
        <?php 
		return ob_get_clean ();
    }
    
    
    public function deletePost($file) {
    }
    
}