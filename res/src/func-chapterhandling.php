<?php

include("globals.php");
include("user-prefs.php");

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

function sortPageList() {

    // Sorts chapters in given direction,
    // adds them to a list in that order,
    // and returns that list.

    include("globals.php");
    include("user-prefs.php");

    $outPages = [];

	// Sort chapters
	$sortedChapters = sortChapters($READ_DIRECTION);

	// Some use cases will start from most recent chapter and move forward,
	// we want to make sure that the list is sorted in the proper
	// direction, which will be different from how the list shows up
	// in the menu

	if ($READ_DIRECTION == $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST) {
		$sortedChapters = sortChapters($READ_FIRST_PAGE_FIRST);
	}

	// Build a list of all pages in the order we want
	foreach ($sortedChapters as $chapter) {
		foreach ($chapter as $page) {
			$outPages[] = $page;
		}
	}

    return $outPages;

}

?>