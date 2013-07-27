<?php
header('Content-Type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/config.php';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Tweelinks</title>
	<style>
		body {
			margin-top: 15px;
			margin-bottom: 15px;
		}
		h1,h3, ol {
			font-family:Georgia, Times, serif; 
			color:#222; 
			width:60%;
			display:block;
			margin: auto;
			margin: auto;
		}
		h1 { text-align: center; display: block; font-size:40px}
		h3 { text-align: right; font-size:30px;
			margin-top: 15px;
			margin-bottom: 15px;
		 }
		ol { font-style:italic; font-size:18px; color:;  }
		ol .tweelink { border-left: 1px solid #666; ; padding-left: 10px; padding-bottom: 10px}
		ol  p {  display: inline }
		ol li a { text-decoration:none; color:#222; display: inline;  }
		ol li a:hover { text-decoration:underline; }
	</style>
</head>
<body>
<?= '<h1>Tweelinks</h1>'; ?>
<div class="list2">
	<?
	$tweeLink = new Tweelink\Tweelink($config);
	$content = $tweeLink->getCacheContent();

	echo '<ol>';
	
	foreach ($content as $currMonth => $links){
		echo '</ol><h3>'.$currMonth.'</h3><ol>';

		foreach ($links as $currLink) {
			$title = empty($currLink['title']) ? $currLink['url'] : $currLink['title'];
			$url = empty($currLink['url']) ? $currLink['url'] : '';

	    	echo '<li><div class="tweelink"><p><a href="'.$url.'">'.$title.'</a></p></div></li>';
		}

	    
	}
	echo '</ol>';
	?>
</div>
</body>
</html>>


