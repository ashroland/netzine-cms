<?php
// global blocklist so we can prep content in advance
global $blocklist;
include './res/blocklist.php';

function userInfo($file) {
	// grab user info for hit log because i am curious!
	
	$ip = $_SERVER['REMOTE_ADDR']; // naive implementation for naive surveillance
	
	$logFile = "./users/.users";
	$logContent = file_get_contents($logFile);
	$timeStamp = date("Ymd-H:i:s");
	$browser = $_SERVER['HTTP_USER_AGENT'];
	$outString = $ip . "\t" . $file . "\t" . $timeStamp . "\t" . $browser . "\n" . $logContent;

	file_put_contents($logFile, $outString);

}

function buildPageFromFile($file) {
	// we will encounter many different kinds of files when
	// we scan the page folder, and will want to handle them
	// in explicit ways depending on what they are.

	$handledExtensions = [];
	$handledExtensions['text'] = array("txt");
	$handledExtensions['code'] = array("sh","py");
	$handledExtensions['web']  = array("html", "php");

	// surmise what kind of file we're dealing with
	$fileExtension = pathinfo($file, PATHINFO_EXTENSION);

	
	if ( in_array($fileExtension, $handledExtensions['text'])) {
		// white-space sensitive textfiles just get echoed to a div
		echo "<div id=\"txt\">" . file_get_contents($file) . "</div>\n";
	}
	else if ( in_array($fileExtension, $handledExtensions['code'])) {
		// code snippets get parsed by a syntax highlighter.

		// echo to a nested <pre><code class="type of file to highlight">
		echo "<pre><code class=\"" . $fileExtension . "\">";
		echo file_get_contents($file);
		echo "\t\t</code></pre>\n\n";

		// add packages for highlighting and invoke.
		echo "\t\t<link rel=\"stylesheet\" href=\"./res/highlight/styles/rainbow.css\" />\n";
		echo "\t\t<script src=\"./res/highlight/highlight.pack.js\"></script>\n";
		echo "\t\t<script>hljs.initHighlightingOnLoad();</script>\n";
	}
	else if ( in_array($fileExtension, $handledExtensions['web'])) {
		// web languages rendered by browser; appear in a full-sized iframe 
		echo "<iframe src=\"" . $file . "\" id=\"frame\"></iframe>\n";
	}

	// finally, log user info to file.
	userInfo($file);

}

function getSortedFiles() {
	// we want to be selective about which files we look at
	// while building the navigation or building the page body.
	// to save time later, reverse-sort the files we find
	
 	$files = glob("./pages/*{.html,.php,.txt,.sh}", GLOB_BRACE);
	rsort($files);
	return $files;
}

function buildPageBody() {

	global $blocklist;


	if ($_GET['p']) {

		if ( in_array($_GET['p'], $blocklist) ) {
			// catch request for about page -- handled a little differently
			if ( $_GET['p'] == 'about.html') {
				$page = "./pages/about.html";
				buildPageFromFile($page);
			} else {
				// file is on blocklist, redirect
				echo "<meta http-equiv=\"refresh\" content=\"0;url=https://xxyle.computer/\">\n";	
			}
		} else {
			// grab requested page
			$page = "./pages/" . $_GET['p'];

			if ( is_file($page) == false ) {
				// file does not exist, redirect
				echo "<meta http-equiv=\"refresh\" content=\"0;url=https://xxyle.computer/\">\n";
			} else {
				// found real file. load contents.
				buildPageFromFile($page);
			}
		}
	} else {
		// grab newest
		$files = getSortedFiles();
		$keep_list = [];

		foreach ($files as $file) {
			if ( !in_array( basename($file), $blocklist) ) {
				$keep_list[] = $file;
			}
		}

 		$page = $keep_list[0];
 		buildPageFromFile($page);
	}
}

function buildNavigation() {
	global $blocklist;

	$files = getSortedFiles();
 	
 	echo "<ul>\n";
 	foreach ($files as $file) {
 		// pretty up the filenames, format html nicely
 		$output = basename($file);
 		if ( !in_array($output, $blocklist) ) {
 			echo "\t\t\t\t\t<li><a href=\"?p=" . $output . "\">" . $output . "</a></li>\n";
 		}
 	}
 	echo "\t\t\t\t</ul>\n";
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>XXYLE.COMPUTER</title>
		<script language="javascript">

			function toggleCalendar() {
				cal = document.getElementById("calendar");
				if (cal.getAttribute("style") == 'visibility:visible') {
					cal.setAttribute("style", 'visibility:hidden');
				} else {
					cal.setAttribute("style", 'visibility:visible');
				}
			}

		</script>
		<script type="text/javascript" src="https://xxyle.computer/pages/packages/jquery/jquery-3.3.1.min.js"></script>
		<script type="text/javascript" src="./pages/packages/cookie/jquery.cookie.js"></script>
		<script type="text/javascript">
			$(function() {
					var COOKIE_NAME = 'xxyle.computer.contentwarning';
					$go = $.cookie(COOKIE_NAME);
					if ($go == null) {
						$.cookie(COOKIE_NAME, 'wegood', { path: '/', expires: 365 });
						window.location = "cw.php";
					}
					else {
					}
			});
		</script>


		<link rel="stylesheet" type="text/css" href="./res/style.css">
	</head>
	<body>
		<?php buildPageBody() ?>

		<div id="calendar" style="visibility:hidden">
			<div class="top">
				<?php buildNavigation() ?>
			</div>
			<a href="?p=about.html"><div class="bottom">about</div></a>
		</div>
		<div id="calbutton"><a href="#" onclick="toggleCalendar()"><img src="./res/calendar.png" border="0" /></a></div>
	</body>
</html>