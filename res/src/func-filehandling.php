<?php

// global blocklist so we can prep content in advance,
// and to prevent "magic" files from appearing in calendar
global $blocklist;
global $ext;

global $chapters_dir;
$chapters_dir = '/chapters'; // no trailing slash
$chapters_dir = getcwd() . $chapters_dir;

function getSortedFiles($dir) {
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
    $globString = $dir . "/*{";
        // echo $globString;
        // exit();

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

function getChapterDirs() {
    // Iterate $chapters_dir for subdirectories, pass those to getUnblockedFileList
    $retArray = [];

    $chapDir = getcwd() . '/chapters';
    $chapDirs = scanDir($chapDir);
    array_shift($chapDirs); // remove .
    array_shift($chapDirs); // remove ..

    foreach ($chapDirs as $baseDir) {
        $retArray[$baseDir] = getUnblockedFileList($chapDir . "/" . $baseDir);
    }

    return $retArray;
}

function getUnblockedFileList($dir='./pages') {
	// Returns a list of all files in ./pages 
	// which do not appear in the global blocklist

    global $blocklist;

	$files = getSortedFiles($dir);
	$keep_list = [];

	foreach ($files as $file) {
		if ( !in_array( basename($file), $blocklist) ) {
			$keep_list[] = $file;
		}
	}

	return $keep_list;
}


function isBlocked($file) {
    global $blocklist;
    // echo var_dump($blocklist);
    // echo $file; 
    // echo in_array($file, $blockList);
    return in_array($file, $blocklist);
}

function buildPageIfFileExists($file, $prefix) {

    $fullPath = "./" . $prefix . "/" . $file;

    if ( is_file($fullPath) == false ) {
        // file does not exist, redirect
        echo '<meta http-equiv="refresh" content="0;url=/">' . "\n";
    } else {
        // found real file. load contents.
        buildPageFromFile($fullPath);
    }

}

function requestPage() {

	// Check $_GET for page request information.
	// Do some routine handling, like checking if the named file exists,
	// if it is or isn't on the block list.
    // Lastly, if no filename is given, have a reasonable default behavior. 
    // This function hands off file contents to rendering stage. 

    global $blocklist;
    global $chapters_dir;

    // is page a menu item? 
    if ($_GET['m']) {
        $page = $_GET['m'];
        buildPageIfFileExists($page, "menu");
    }
    
    
    else if ($_GET['p']) {
        $page = $_GET['p'];

		if ( isBlocked($page) ) {
            echo '<meta http-equiv="refresh" content="0;url=/">' . "\n";	
		} else {
            buildPageIfFileExists($page, "chapters");
		}
	} else {
        // No filename given, grab first file depending on userpref
        include("globals.php");
        include("user-prefs.php");

        $filesList = [];
        $firstPage = "";

        // Sort pages and take first element as starting page
        $filesList = sortPageList($READ_FIRST_PAGE_FIRST);
        $firstPage = $filesList[0];

        // Edge case: sort pages and take first element of second chapter
        // as starting page
        if ($READ_DIRECTION == $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST) {
            $allChapters = sortChapters($READ_DIRECTION);

            // First dict key is most recent chapter and its last elm 
            // is the starting page
            $chap = array_keys($allChapters)[0];

            // php does not rewrite key indices when reorder them,
            // so we have to figure out which key accesses the 0th
            // element. yuck. is this right?
            $firstPageIndex = array_keys($allChapters[$chap])[0];
            $firstPage = $allChapters[$chap][$firstPageIndex];
            
        }

        $firstPage = "chapters/" . makeRelativeChapterPath($firstPage);
 		buildPageFromFile($firstPage);
	}
}

?>