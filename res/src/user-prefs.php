<?php 
include("globals.php");


/*

Read direction.

Depending on the style of publication, you might want the user to land
on the newest page first, like a webcomic would, or you might want to 
start from the very first page in the first chapter, like a traditional text. 

*/

// Start from first page of first chapter:
$READ_DIRECTION = $READ_FIRST_PAGE_FIRST;

// Display the last page first. 
// $READ_DIRECTION = $READ_NEWEST_FIRST;

// Display first page of newest chapter first
// $READ_DIRECTION = $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST;







/*

Chapter folding in menu.

Selects the behavior for hiding the titles of chapter pages behind a fold.

*/


// Do not hide any page names
// $FOLD_BEHAVIOR = $FOLD_NONE;

// Hide all page names
// $FOLD_BEHAVIOR = $FOLD_ALL;

// Show only the page names of the newest chapter
// $FOLD_BEHAVIOR = $FOLD_ALL_BUT_NEWEST_CHAPTER;

// Fold all but the chapter the user is currently in
$FOLD_BEHAVIOR = $FOLD_ALL_BUT_CURRENT_CHAPTER;






/*

Show or hide file extensions in menu

Certain projects may require that file extensions are either occluded or on display.

*/

// Hide file extensions
// $FILE_BEHAVIOR = $EXTENSIONS_HIDE;

// Show file extensions
$FILE_BEHAVIOR = $EXTENSIONS_SHOW;






/*

Menu coloring

*/

$MENU_COLOR_START = 100;
$MENU_COLOR_STEP = 5;
?>