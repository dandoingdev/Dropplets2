<?php

namespace Dropplets;

class Layout {
    
    private $settings = null;
    private $tools = null;
    
    public function __construct() {
        $this->settings = Settings::instance();
        $this->tools = new Tools();
    }
    
    
function get_pagination($page,$total) {

    $string = '';
    $string .= "<ul style=\"list-style:none; width:400px; margin:15px auto;\">";

    for ($i = 1; $i<=$total;$i++) {
        if ($i == $page) {
            $string .= "<li style='display: inline-block; margin:5px;' class=\"active\"><a class=\"button\" href='#'>".$i."</a></li>";
        } else {
            $string .=  "<li style='display: inline-block; margin:5px;'><a class=\"button\" href=\"?page=".$i."\">".$i."</a></li>";
        }
    }
    
    $string .= "</ul>";
    return $string;
}
    
    function get_header() {
        
            $blog_url = $this->settings->get('blog_url');
?>
    <!-- RSS Feed Links -->
    <link rel="alternate" type="application/rss+xml" title="Subscribe using RSS" href="<?php echo $blog_url; ?>rss" />
    <link rel="alternate" type="application/atom+xml" title="Subscribe using Atom" href="<?php echo $blog_url; ?>atom" />
    
    <!-- Dropplets Styles -->
    <link rel="stylesheet" href="<?php echo $blog_url?>/dropplets/style/style.css">
    <link rel="shortcut icon" href="<?php echo $blog_url?>/dropplets/style/images/favicon.png">

    <!-- User Header Injection -->
    <?php echo $this->settings->get('header_inject'); ?>
    
    <!-- Plugin Header Injection -->
    <?php \Dropplets\Actions\Action::run('dp_header'); ?>
<?php 

} 
    
    function get_footer() { 
$PAGINATION_ON_OFF = $this->settings->get('PAGINATION_ON_OFF');       

?>
    <!-- jQuery & Required Scripts -->
    <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
    
    <!-- Dropplets Tools -->
    <?php 
    
    echo $this->tools->showMenu(); ?>
    
    <!-- User Footer Injection -->
    <?php echo $this->settings->get('FOOTER_INJECT'); ?>
    
    <!-- Plugin Footer Injection -->
    <?php \Dropplets\Actions\Action::run('dp_footer'); ?>
<?php 

}
    
}
    