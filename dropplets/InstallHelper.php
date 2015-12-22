<?php

namespace Dropplets;

class InstallHelper {
    
    public function start() {
        	// Get the components of the current url.
	$protocol = @($_SERVER ["HTTPS"] != 'on') ? 'http://' : 'https://';
	$domain = $_SERVER ["SERVER_NAME"];
	$port = $_SERVER ["SERVER_PORT"];
	$path = $_SERVER ["REQUEST_URI"];
	
	// Check if running on alternate port.
	if ($protocol === "https://") {
		if ($port == 443)
			$url = $protocol . $domain;
		else
			$url = $protocol . $domain . ":" . $port;
	} elseif ($protocol === "http://") {
		if ($port == 80)
			$url = $protocol . $domain;
		else
			$url = $protocol . $domain . ":" . $port;
	}
	
	$url .= $path;
    
    
$htaccess_file = "./.htaccess";
    
    // Generate the .htaccess file on initial setup only.
    if (! file_exists ( $htaccess_file )) {
			
        // Get subdirectory
        $dir = str_replace ( 'dropplets/save.php', '', $_SERVER ["REQUEST_URI"] );

        // Parameters for the htaccess file.
        $htaccess [] = "# Pretty Permalinks";
        $htaccess [] = "RewriteRule ^(images)($|/) - [L]";
        $htaccess [] = "RewriteCond %{REQUEST_URI} !^action=logout [NC]";
        $htaccess [] = "RewriteCond %{REQUEST_URI} !^action=login [NC]";
        $htaccess [] = "Options +FollowSymLinks -MultiViews";
        $htaccess [] = "RewriteEngine on";
        $htaccess [] = "RewriteBase " . $dir;
        $htaccess [] = "RewriteCond %{REQUEST_URI} !index\.php";
        $htaccess [] = "RewriteCond %{REQUEST_FILENAME} !-f";
        $htaccess [] = "RewriteRule ^(.*)$ index.php?filename=$1 [NC,QSA,L]";

        // Generate the .htaccess file.
        file_put_contents ( $htaccess_file, implode ( "\n", $htaccess ) );
    }
	
	// Check if the install directory is writable.
	$is_writable = (TRUE == is_writable ( dirname ( __FILE__ ) . '/' ));
	?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Let's Get Started</title>
<link rel="stylesheet" href="./dropplets/style/style.css" />
<link href='http://fonts.googleapis.com/css?family=Lato:100,300'
	rel='stylesheet' type='text/css'>
<link
	href='http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300,400'
	rel='stylesheet' type='text/css'>
<link rel="shortcut icon" href="./dropplets/style/images/favicon.png">
</head>

<body class="dp-install">
	<form method="POST" action="./dropplets/save">
		<a class="dp-icon-dropplets" href="http://dropplets.com"
			target="_blank"></a>

		<h2>Install Dropplets</h2>
		<p>Welcome to an easier way to blog.</p>

		<input type="password" name="password" id="password" required
			placeholder="Choose Your Password"> <input type="password"
			name="password-confirmation" id="password-confirmation" required
			placeholder="Confirm Your Password" onblur="confirmPass()"> <input
			hidden type="text" name="blog_email" id="blog_email"
			value="hi@dropplets.com"> <input hidden type="text"
			name="blog_twitter" id="blog_twitter" value="dropplets"> <input
			hidden type="text" name="blog_url" id="blog_url"
			value="<?php echo($url) ?><?php if ($url == $domain) { ?>/<?php } ?>">
		<input hidden type="text" name="template" id="template" value="simple">
		<input hidden type="text" name="blog_title" id="blog_title"
			value="Welcome to Dropplets">
		<textarea hidden name="meta_description" id="meta_description"></textarea>
		<input hidden type="text" name="intro_title" id="intro_title"
			value="Welcome to Dropplets">
		<input hidden type="text" name="file_ext" 
			value=".md">
		<textarea hidden name="intro_text" id="intro_text">In a flooded selection of overly complex solutions, Dropplets has been created in order to deliver a much needed alternative. There is something to be said about true simplicity in the design, development and management of a blog. By eliminating all of the unnecessary elements found in typical solutions, Dropplets can focus on pure design, typography and usability. Welcome to an easier way to blog.</textarea>

		<button type="submit" name="submit" value="submit">k</button>
	</form>
                
            <?php if (!$is_writable) { ?>
                <p style="color: red;">It seems that your config folder
		is not writable, please add the necessary permissions.</p>
            <?php } ?>

            <script>
            	function confirmPass() {
            		var pass = document.getElementById("password").value
            		var confPass = document.getElementById("password-confirmation").value
            		if(pass != confPass) {
            			alert('Your passwords do not match!');
            		}
            	}
            </script>
</body>
</html>
<?php
        
        
    }
    
}
    