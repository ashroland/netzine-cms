<?php

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
		echo '<div id="txt">' . file_get_contents($file) . '</div>' . "\n";
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
		echo tabs(2) . '</code></pre>' . "\n\n";

		// add packages for highlighting and invoke.
		echo tabs(2) . '<link rel="stylesheet" href="./res/highlight/styles/rainbow.css" />' . "\n";
		echo tabs(2) . '<script src="./res/highlight/highlight.pack.js"></script>' . "\n";
		echo tabs(2) . '<script>hljs.initHighlightingOnLoad();</script>' . "\n";
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
		echo '<iframe src="' . $file . '" id="frame"></iframe>' . "\n";
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

		echo '<center>' . "\n";
		echo tabs(3) . '<img id="image" src="' . $file . '" style="' . $marginTop . '" />' . "\n";
		echo tabs(2) . '</center>' . "\n";
	}
	else if ( in_array($fileExtension, $ext['audio'])) {

		// MP3 	audio/mpeg
		// OGG 	audio/ogg
		// WAV 	audio/wav

		$typeString = ($fileExtension == "mp3" ? "mpeg" : $fileExtension);

		echo '<audio controls>' . "\n";
		echo tabs(3) . '<source src="' . $file . '" type="audio/' . $typeString . '">' . "\n";
		echo tabs(2) . '</audio>' . "\n";
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

function sortChapters($dir) {
	global $READ_FIRST_PAGE_FIRST;
	global $READ_NEWEST_FIRST;
	global $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST;

	$chapters = getChapterDirs();

	if ($dir == $READ_FIRST_PAGE_FIRST) {
		// Sort $chapters oldest to newest
		ksort($chapters);
		foreach ($chapters as $name=>$chapter) {
            // Sort pages alphabetically
			sort($chapters[$name]);
		}
	}
	else if ($dir == $READ_NEWEST_FIRST) {
		// Sort $chapters reverse alphabetically
		krsort($chapters);
		foreach ($chapters as $name=>$chapter){
            // Sort pages reverse alphabetically
			rsort($chapters[$name]);
		}
	}
	else if ($dir == $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST) {
        // Sort $chapters reverse alphabetically
        krsort($chapters);
    
		foreach ($chapters as $name=>$chapter) {
            // Sort pages alphabetically
            asort($chapters[$name]);
        }
        
	}

	return $chapters;

}

function buildPageLinks() {
	
	include("globals.php");

	// Sort data per user prefs
	$chapters = sortChapters($READ_DIRECTION);


	// Create nested lists for menu items
	echo '<ul>' . "\n";

	foreach ( $chapters as $chapterName=>$chapter ) {
		// Each chapter is assigned a UID for expanding / collapsing 
		// the fold. UIDs are used instead of, say, url_encode()-ing 
		// the chapter names to prevent namespace collision 
		// and weird user edge cases. 
		$chapterUID = "netzine-mf-" . rand_string(5);

		$showHide = "";

		if ($FOLD_BEHAVIOR == $FOLD_NONE) {
			$showHide = "display";
		} else if ($FOLD_BEHAVIOR == $FOLD_ALL){
			$showHide = "hidden";
		} else if ($FOLD_BEHAVIOR == $FOLD_ALL_BUT_CURRENT_CHAPTER) {
			// Determine which key is the "newest" and keep it unfolded
			// regardless of where it shows up in the menu order.

			// Get a copy of chapters array and reverse sort
			$chapCopy = $chapters;
			krsort($chapCopy);

			// Is current chapter key == first key of sorted copy?
			// If so, it's the newest chapter

			if ( $chapterName == array_keys($chapCopy)[0] ) {
				$showHide = "display";
			} else {
				$showHide = "hidden";
			}
		}

		echo tabs(5) . '<a href="#" onclick="toggleElement(\'' . $chapterUID . '\')"><li>' . $chapterName . '</li></a>' . "\n"; 
		echo tabs(5) . '<ul id="' . $chapterUID . '" class="menuFold ' . $showHide . '">' . "\n";
		
		foreach ($chapter as $page ) {
			$disp = basename($page);

			if ($FILE_BEHAVIOR == $EXTENSIONS_HIDE) {
				$disp = substr($disp, 0, strrpos($disp, "."));
			} 

			$link = $chapterName . "/" . basename($page);
	 		echo tabs(6) . '<li><a href="?p=' . $link . '">' . $disp . "</a></li>\n";
		}
		
		echo tabs(5) . '</ul>' . "\n";
	}
	
	echo tabs(4) . '</ul>' . "\n";
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
		echo '<a href="?p=' . $prevLink . '"><img src="./res/prev.png" alt="previous" /></a>' . "\n";
	} else {
		echo '<img src="./res/blank.png" />' . "\n";
	}

	echo tabs(3) . '<a href="#" onclick="toggleElement(\'calendar\')"><img src="./res/calendar.png" alt="menu" /></a>' . "\n";

	if ($renderForward == true) {
		$nextLink = basename( $files[$index + 1] );
		echo tabs(3) . '<a href="?p=' . $nextLink . '"><img src="./res/next.png" alt="next" /></a>' . "\n";
	} else {
		echo tabs(3) . '<img src="./res/blank.png" />' . "\n";
	}
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

?>