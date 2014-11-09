<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet"
              href="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link rel="stylesheet"
              href="http://rozpocet2.webdesign-vanzura.cz/assets/css/default.css">
        <script src="http://rozpocet2.webdesign-vanzura.cz/assets/js/jquery-1.11.1.min.js"
        type="text/javascript"></script>
        <script type="text/javascript"
        src="https://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
        <title>Vítejte</title>
    </head>
    <body>
    	<div class="container">
    		<h2>Vítejte na stránkách rozpočtů</h2>
    		<ul>
    			<?php
    				foreach($infos as $info) {
    					echo '<li><a href="/mesto/'.$info['subjekt'].'">'.$info['subjekt_nazev'].'</li>';
    				}
    			?>
    		</ul>
    	</div>
	</body>
</html>