<?php
// global blocklist so we can prep content in advance,
// and to prevent "magic" files from appearing in calendar
global $blocklist;
include './res/blocklist.php';

// enable and disable supported extensions
global $ext;
$ext = [];
$ext['text'] = array("txt");
$ext['code'] = array("sh","py","js","c","cpp","rb");
$ext['web']  = array("html", "php");
$ext['images'] = array("jpg","png","gif","bmp");


function buildPageFromFile($file) {
	// we will encounter many different kinds of files when
	// we scan the page folder, and will want to handle them
	// in explicit ways depending on what they are.

	global $ext;
	
	// surmise what kind of file we're dealing with
	$fileExtension = pathinfo($file, PATHINFO_EXTENSION);
	

	// start making some design decisions based 
	// on what kind of file we've found

	if ( in_array($fileExtension, $ext['text'])) {
		// white-space sensitive textfiles just get echoed to a div,
		// css takes care of the rest
		echo "<div id=\"txt\">" . file_get_contents($file) . "</div>\n";
	}
	else if ( in_array($fileExtension, $ext['code'])) {
		// code snippets get parsed by a syntax highlighter.

		// echo to a nested <pre><code>
		echo "<pre><code class=\"" . $fileExtension . "\">";
		echo file_get_contents($file);
		echo "\t\t</code></pre>\n\n";

		// add packages for highlighting and invoke.
		echo "\t\t<link rel=\"stylesheet\" href=\"./res/highlight/styles/rainbow.css\" />\n";
		echo "\t\t<script src=\"./res/highlight/highlight.pack.js\"></script>\n";
		echo "\t\t<script>hljs.initHighlightingOnLoad();</script>\n";
	}
	else if ( in_array($fileExtension, $ext['web'])) {
		// web languages rendered by browser; appear in a full-sized iframe 
		echo "<iframe src=\"" . $file . "\" id=\"frame\"></iframe>\n";
	}
	else if ( in_array($fileExtension, $ext['images'])) {
		// images positioned at top-third "fold" and centered
		// the css gets it positioned, but we have to set negative margins based
		// on the images' actual dimensions

		// get image information
		$imgInfo = getimagesize($file);
		$height = $imgInfo[1];

		// render inline css
		$marginTop = "margin-top: " . -(floor($height / 2)) . "px;";

		echo '<div id="image">';
		echo '<img src="' . $file . '" style="' . $marginTop . '" />';
		echo '</div>';
	}

}

function getSortedFiles() {
	// We want to be selective about which files we look at
	// while building the navigation or building the page body.
	// This excludes system files that can get uploaded 
	// without users' knowledge. (.trashes, etc) and files which 
	// are found on the global blocklist.
	// PHP's built-in glob() is really good at this
	
	global $ext;

	// iterate nested arrays in $ext for all supported filetypes
	// into a glob search string, e.g.:
	// ./pages/*{.html,.php,.txt,.sh,.jpg,.png}
	$globString = "./pages/*{";

	foreach ( $ext as $extList ) {
		foreach ($extList as $extension) {
			$globString = $globString . $extension . ","; 
		}
	}

	$globString = substr($globString, 0, -1); // no trailing comma
	$globString = $globString . "}";

	// find, sort and return file list
 	$files = glob($globString, GLOB_BRACE);
	rsort($files);
	return $files;
}

function getUnblockedFileList() {
	// Returns a list of all files in ./pages 
	// which do not appear in the global blocklist

	global $blocklist;

	$files = getSortedFiles();
	$keep_list = [];

	foreach ($files as $file) {
		if ( !in_array( basename($file), $blocklist) ) {
			$keep_list[] = $file;
		}
	}

	return $keep_list;
}

function requestPage() {

	// Check $_GET for page request information.
	// Do some routine handling, like checking if the named file exists,
	// if it is or isn't on the block list.
	// Lastly, if no filename is given, have a reasonable default behavior. 

	global $blocklist;

	if ($_GET['p']) {
		if ( in_array($_GET['p'], $blocklist) ) {
			// catch request for about page -- handled a little differently
			if ( $_GET['p'] == 'about.html') {
				$page = "./pages/about.html";
				buildPageFromFile($page);
			} else {
				// file is on blocklist, redirect
				echo "<meta http-equiv=\"refresh\" content=\"0;url=/\">\n";	
			}
		} else {
			// grab requested page
			$page = "./pages/" . $_GET['p'];

			if ( is_file($page) == false ) {
				// file does not exist, redirect
				echo "<meta http-equiv=\"refresh\" content=\"0;url=/\">\n";
			} else {
				// found real file. load contents.
				buildPageFromFile($page);
			}
		}
	} else {
		// No filename given, grab newest
 		$page = getUnblockedFileList()[0];
 		buildPageFromFile($page);
	}
}

function buildPageLinks() {

	$files = getUnblockedFileList();
 	
 	echo "<ul>\n";
 	foreach ($files as $file) {
 		// pretty up the filenames, format html nicely
 		$output = basename($file);
 		echo "\t\t\t\t\t<li><a href=\"?p=" . $output . "\">" . $output . "</a></li>\n";
 	}
 	echo "\t\t\t\t</ul>\n";
}

function buildNavigation() {
// make arrows depending on state

	$files = getUnblockedFileList();
	$index = 0;
	$renderForward = true;
	$renderBackward = true;

	if (isset($_GET['p'])) {
		// get index of current page
		$curPage = $_GET['p'];
		$index = array_search( "./pages/" . $curPage, $files);
	}

	// catch edge cases
	if ($index == 0) {
		$renderBackward = false;
	} else if ($index == sizeof($files) - 1 ) {
		$renderForward = false;
	}
	

	if ($renderBackward == true) {
		$prevLink = basename( $files[$index - 1] );
		echo '<a href="?p=' . $prevLink . '"><img src="./res/prev.png" alt="previous" /></a>';
	} else {
		echo '<img src="./res/blank.png" />';
	}

	echo '<a href="#" onclick="toggleCalendar()"><img src="./res/calendar.png" alt="menu" /></a>';

	if ($renderForward == true) {
		$nextLink = basename( $files[$index + 1] );
		echo '<a href="?p=' . $nextLink . '"><img src="./res/next.png" alt="next" /></a>';
	} else {
		echo '<img src="./res/blank.png" />';
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>netzine-cms</title>
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
		<link rel="stylesheet" type="text/css" href="./res/style.css">
	</head>
	<body>
		<?php requestPage() ?>

		<div id="calendar" style="visibility:hidden">
			<div class="top">
				<?php buildPageLinks() ?>
			</div>
			<a href="?p=about.html"><div class="bottom">about</div></a>
		</div>
		<div id="calbutton">
			<?php buildNavigation() ?>
		</div>
	</body>
</html>