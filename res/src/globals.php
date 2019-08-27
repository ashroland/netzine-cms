<?php 

// User Preferences
//
// Read Order
global $READ_DIRECTION;
global $READ_FIRST_PAGE_FIRST;
global $READ_NEWEST_FIRST;
global $READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST;

$READ_FIRST_PAGE_FIRST                      = 100;
$READ_NEWEST_FIRST                          = 125;
$READ_NEWEST_CHAPTER_FROM_FIRST_PAGE_FIRST  = 150;

// Menu folding
global $FOLD_BEHAVIOR;
global $FOLD_NONE;
global $FOLD_ALL;
global $FOLD_ALL_BUT_CURRENT_CHAPTER;
global $FOLD_ALL_BUT_NEWEST_CHAPTER;

$FOLD_NONE                      = 200;
$FOLD_ALL                       = 225;
$FOLD_ALL_BUT_CURRENT_CHAPTER   = 250;
$FOLD_ALL_BUT_NEWEST_CHAPTER    = 275;

// Hide file extensions in menu?
global $FILE_BEHAVIOR;
global $EXTENSIONS_HIDE;
global $EXTENSIONS_SHOW;

$EXTENSIONS_HIDE = 300;
$EXTENSIONS_SHOW = 325;


// Pretty menu colors
global $MENU_COLOR_START;
global $MENU_COLOR_STEP;

// Internal
//
// Supported file extensions
global $ext;
$ext = [];
$ext['text'] = array("txt");
$ext['code'] = array("sh","py","js","c","cpp","rb");
$ext['web']  = array("html", "php");
$ext['images'] = array("jpg","png","gif","bmp");
$ext['audio'] = array("mp3", "ogg", "wav");

?>