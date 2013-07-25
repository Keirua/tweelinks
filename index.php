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
		ol .tweelink { border-left: 1px solid #999; ; padding-left: 10px; padding-bottom: 10px}
		ol  p {  display: inline }
		/*ol  li {  padding-bottom: 10px }*/
		ol li a { text-decoration:none; color:#666; display: inline }
		ol li a:hover { text-decoration:underline; }
		/*ol li p em { display:block; }*/
	</style>
</head>
<body>
<?= '<h1>Tweelinks</h1>'; ?>
<div class="list2">
	<?
	$tweeLink = new Tweelink\Tweelink($config);
	$tweeLink ->displayCacheContent();
	?>
</div>
</body>
</html>>


