<?php

include("globals.php");
include("user-prefs.php");
include("func-chapterhandling.php");

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
		println('<div id="txt">' . file_get_contents($file) . '</div>');
	}
	else if ( in_array($fileExtension, $ext['code'])) {
		// code snippets get parsed by a syntax highlighter.
		//
		// to consider in the future:
		// is this a lightweight syntax highlighter? would this work
		// be better offloaded to server-side rendering?
		// it couldn't be too hard to whip up a php -> node linter...
		// plus expecting the visitors to have reliable enough internet 
		// to download a large package and execute it might be too 
		// much for ppl with limited / slow internet 

		// echo to a nested <pre><code>
		echo '<pre><code class="' . $fileExtension . '">';
		echo file_get_contents($file);
		println('</code></pre>', 2);
		println("");

		// add packages for highlighting and invoke.
		println('<link rel="stylesheet" href="./res/highlight/styles/rainbow.css" />', 2);
		println('<script src="./res/highlight/highlight.pack.js"></script>', 2);
		println('<script>hljs.initHighlightingOnLoad();</script>', 2);
	}
	else if ( in_array($fileExtension, $ext['web'])) {
		// web languages rendered by browser; appear in a full-sized iframe.
		// 
		// for a possible major refactor, when i get bored lol,
		// instead of an iframe we can just read and place whole web pages,
		// and then add the menu on top. iframe may not be durable and
		// while it is certified to be accessible, it is two page loads
		// instead of just one. this might be considered a no-go overhead
		// in areas with very low / slow internet access.
		println('<iframe src="' . $file . '" id="frame"></iframe>');
	}
	else if ( in_array($fileExtension, $ext['images'])) {
		// images positioned at top-third "fold" and centered
		// the css gets it positioned, but we have to set negative margins based
		// on the images' actual dimensions

		// get image information
		$imgInfo = getimagesize($file);
		$height = $imgInfo[1];

		// render inline css
		$marginTop  = "margin-top: " . (floor($height / 3)) . "px;";

		println('<center>');
		println('<img id="image" src="' . $file . '" style="' . $marginTop . '" />', 3);
		println('</center>', 2);
	}
	else if ( in_array($fileExtension, $ext['audio'])) {

		// MP3 	audio/mpeg
		// OGG 	audio/ogg
		// WAV 	audio/wav

		$typeString = ($fileExtension == "mp3" ? "mpeg" : $fileExtension);

		println('<audio controls>');
		println('<source src="' . $file . '" type="audio/' . $typeString . '">', 3);
		println('</audio>', 2);
	}

}

function rand_string($len=10) {
    $characters = '0123456789ABCDEF';
    $random_string = '';
    for ($i = 0; $i < $len; $i++) {
        $random_string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $random_string;
}

function buildPageLinks() {
	
	// Build menu. 
	//
	include("user-prefs.php");

	// Sort data per user prefs
	$chapters = sortChapters($READ_DIRECTION);

	println('<div id="upperMenu">');
	println('<ul>', 4);

	// do fun and pretty colors
	$colCounter = $MENU_COLOR_START; 
	$colStep = $MENU_COLOR_STEP;

	foreach ( $chapters as $chapterName=>$chapter ) {
		// Each chapter is assigned a UID for expanding / collapsing 
		// the fold. UIDs are used instead of, say, url_encode()-ing 
		// the chapter names to prevent namespace collision 
		// and weird user edge cases. 
		$chapterUID = "netzine-mf-" . rand_string(5);

		$showHide = "";

		if ($FOLD_BEHAVIOR == $FOLD_NONE) {
			$showHide = "visible";
		} else if ($FOLD_BEHAVIOR == $FOLD_ALL){
			$showHide = "hidden";
		} else if ($FOLD_BEHAVIOR == $FOLD_ALL_BUT_NEWEST_CHAPTER) {
			// Determine which key is the "newest" and keep it unfolded
			// regardless of where it shows up in the menu order.

			// Get a copy of chapters array and reverse sort
			$chapCopy = $chapters;
			krsort($chapCopy);

			// Is current chapter key == first key of sorted copy?
			// If so, it's the newest chapter

			if ( $chapterName == array_keys($chapCopy)[0] ) {
				$showHide = "visible";
			} else {
				$showHide = "hidden";
			}
		} else if ($FOLD_BEHAVIOR == $FOLD_ALL_BUT_CURRENT_CHAPTER) {
			// Determine which chapter we're in and keep it unfolded
			// regardless of where it shows up in order

			$chap = "";

			if (isset($_GET['p'])) {
				// Easy mode
				$pageQuery = $_GET['p'];
				$chap = explode("/", $pageQuery)[0];
			} else {
				// Figure out how we're sorting pages and
				// make some decisions from there
				if ($READ_DIRECTION == $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST) {
					// trickiest case
					
					// get list of pages
					$allChapters = sortChapters($READ_DIRECTION);
					// get index of current page
					// First dict key is most recent chapter and its last elm 
					// is the starting page
					$chap = array_keys($allChapters)[0];
				
				} else {
					$pages = sortPageList();
					$chap = array_splice(explode("/", $pages[0]), -2, 1);
					$chap = $chap[0];
				}
			}

			if ($chap == $chapterName) {
				$showHide = "visible";
			} else {
				$showHide = "hidden";
			}
		}


		// build uid style for cute hover effects. 
		$col = prettyColor($colCounter);

		$colUID = "menu_" . rand_string(8);
		echo tabs(5) . '<style type="text/css">';
		echo "." . $colUID . ":hover {";
		echo "color: white; ";
		echo "background-color:" . $col . "} ";
		println('</style>');

		println('<a href="#" style="color:' . $col . '" onclick="toggleElement(\'' . $chapterUID . '\')"><li class="' . $colUID . '">' . $chapterName . '</li></a>', 5); 
		$colCounter+=$colStep;

		println('<ul id="' . $chapterUID . '" class="menuFold ' . $showHide . '">', 5);
		
		foreach ($chapter as $page ) {
			$disp = basename($page);

			if ($FILE_BEHAVIOR == $EXTENSIONS_HIDE) {
				$disp = substr($disp, 0, strrpos($disp, "."));
			} 

			$link = $chapterName . "/" . basename($page);
			$col = prettyColor($colCounter);
			println('<a href="?p=' . $link . '"><li style="border-color:' . $col . '">' . $disp . '</li>', 6);
			$colCounter+=$colStep;
		}
		
		println('</ul>', 5);
	}
	
	println('</ul>', 4);
	println('</div>', 3);
	println('<div id="lowerMenu">', 3);
	println('<ul>', 4);

	// iterate contents of ./menu and build <li>s 

	$menuItems = getSortedFiles("./menu");

	foreach ($menuItems as $menuItem) {

		$link = "?m=" . basename($menuItem);
		$disp = basename($menuItem);

		if ($EXTENSIONS_BEHAVIOR == $EXTENSIONS_HIDE) {
			$disp = array_split(explode(".", $disp), -1, 1)[0];
		}
		
		println('<a href="' . $link . '"><li>' . $disp . '</li></a>', 5);
		
	}


	println('</ul>', 4);
	println('</div>', 3);

}

function buildNavigation() {
	// make arrows depending on state.
	// Create a page list, sort it depending on user pref,
	// determine where u are on that list, and create next/prev
	// buttons which move through that list 

	// include("globals.php");
	include("user-prefs.php");

	// Buttons will behave differently depending on how content is arranged
	// global $READ_DIRECTION;
	// global $READ_FIRST_PAGE_FIRST;
	// global $READ_NEWEST_FIRST;
	// global $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST;

	$curPage = ""; // Assume we're at the starting page 
	$outPages = sortPageList();
	$index = 0;
	$renderForward = true;
	$renderBackward = true;

	if (isset($_GET['p'])) {
		// get index of current page
		$curPage = $_GET['p'];
	} else if ($READ_DIRECTION == $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST) {
		$allChapters = sortChapters($READ_DIRECTION);

		// First dict key is most recent chapter and its last elm 
		// is the starting page
		$chap = array_keys($allChapters)[0];

		// php does not rewrite key indices when reorder them,
		// so we have to figure out which key accesses the 0th
		// element. yuck. is this right?
		$firstPageIndex = array_keys($allChapters[$chap])[0];
		$firstPage = $allChapters[$chap][$firstPageIndex];

		$curPage = makeRelativeChapterPath($firstPage);

	} else {
		$curPage = makeRelativeChapterPath($outPages[0]);
	}

	$search = getcwd() . "/chapters/" . $curPage;
	$index = array_search( $search, $outPages);


	// catch edge cases
	if ($index == 0) {
		$renderBackward = false;
	} else if ($index == sizeof($outPages) - 1 ) {
		$renderForward = false;
	}
	
	// Build links
	// 
	if ($renderBackward == true) {
		// Find previous link in array
		$prevLink = $outPages[$index - 1];

		// Build a relative url
		$prevLink = makeRelativeChapterPath($prevLink);
		println('<a href="?p=' . $prevLink . '"><img src="./res/prev.png" alt="previous" /></a>');
	} else {
		println('<img src="./res/blank.png" />');
	}

	println('<a href="#" onclick="toggleElement(\'calendar\')"><img src="./res/calendar.png" alt="menu" /></a>', 3);

	if ($renderForward == true) {
		// Find previous link in array
		$nextLink = $outPages[$index + 1];
		
		// Build a relative url
		$nextLink = makeRelativeChapterPath($nextLink);
		println('<a href="?p=' . $nextLink . '"><img src="./res/next.png" alt="next" /></a>', 3);
	} else {
		println('<img src="./res/blank.png" />', 3);
	}
}

function makeRelativeChapterPath($fqp) {
	return implode("/", array_slice(explode("/" , $fqp), -2, 2));
}

function tabs($num=1) {
    // i feel compelled to make output html as pretty as possible.
    // this helps us build tabs so we don't have to keep track 
    // of writing them in by hand

	$output = "";

	for ($x = 0; $x < $num; $x++ ) {
		$output .= "\t";
	}

	return $output;
}

function println($inStr, $tabs=0) {
	// Tidy output, tidy codebase. 
	echo tabs($tabs) . $inStr . "\n";
}

function mapNum($value, $istart, $istop, $ostart, $ostop) {
	return $ostart + ($ostop - $ostart) * (($value - $istart) / ($istop - $istart));
}

function hsl2rgb($h, $s, $l) {
	// big ups to enhzflep on stackoverflow
	// i did not want to implement this
	// thx bud

	$r = $l;
	$g = $l;
	$b = $l;
	$v = ($l <= 0.5) ? ($l * (1.0 + $s)) : ($l + $s - $l * $s);
	if ($v > 0){
		$m;
		$sv;
		$sextant;
		$fract;
		$vsf;
		$mid1;
		$mid2;

		$m = $l + $l - $v;
		$sv = ($v - $m ) / $v;
		$h *= 6.0;
		$sextant = floor($h);
		$fract = $h - $sextant;
		$vsf = $v * $sv * $fract;
		$mid1 = $m + $vsf;
		$mid2 = $v - $vsf;

		switch ($sextant)
		{
			case 0:
				$r = $v;
				$g = $mid1;
				$b = $m;
				break;
			case 1:
				$r = $mid2;
				$g = $v;
				$b = $m;
				break;
			case 2:
				$r = $m;
				$g = $v;
				$b = $mid1;
				break;
			case 3:
				$r = $m;
				$g = $mid2;
				$b = $v;
				break;
			case 4:
				$r = $mid1;
				$g = $m;
				$b = $v;
				break;
			case 5:
				$r = $v;
				$g = $m;
				$b = $mid2;
				break;
		}
	}
	return array('r' => $r * 255.0, 'g' => $g * 255.0, 'b' => $b * 255.0);
}

function padHex($hex, $len) {

	while (strlen($hex) < $len) {
		$hex = "0" . $hex;
	}

	return $hex;

}

function hexColor($rgbArray) {

	$hex_r = padHex(dechex($rgbArray['r']), 2);
	$hex_g = padHex(dechex($rgbArray['g']), 2);
	$hex_b = padHex(dechex($rgbArray['b']), 2);

	return "#" . $hex_r . $hex_g . $hex_b . ";";
}

function prettyColor($hue, $saturation=100, $lightness=40) {
	// return hexColor( hsl2rgb($hue, $saturation, $lightness) );
	return "hsl(" . $hue . ","  . $saturation . "%," . $lightness . "%);";
}

?>