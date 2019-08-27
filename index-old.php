<?php

/* 

EPOCH 2:

Chapter-driven zines. 

- Content directory moves from ./pages to ./chapters
- Contents of ./chapters/ are treated like disparate units
- Chapter-jump buttons
- display most-recent page first or first page of first chapter first
	- buttons move thru pages relative to this choice


User-settable preferences. 
- Most-recent page first or first page of first chapter first

Logical housekeeping
- Rendering and filehandling functions separated

*/

include './res/src/globals.php';
include './res/src/user-prefs.php';
include './res/src/blocklist.php';
include './res/src/func-filehandling.php';
include './res/src/func-rendering.php';

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" type="text/css" href="res/style.css">
		<link rel="stylesheet" media="(max-width: 640px)" href="res/max-640px.css">

		<title>netzine-cms</title>
		<script language="javascript">

			function toggleElement(uid) {
				elm = document.getElementById(uid);
				classes = elm.classList;
				if ( classes.contains("visible") ){
					classes.remove("visible");
					classes.add("hidden");
				} else {
					classes.remove("hidden");
					classes.add("visible");
				}
			}

		</script>
	</head>
	<body>
		<?php requestPage() ?>

		<div id="calendar" class="hidden">
			<?php buildPageLinks() ?>
		</div>
		<div id="calbutton">
			<?php buildNavigation() ?>
		</div>
	</body>
</html>