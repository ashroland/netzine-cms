<?php

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
        <title>netzine-cms</title>
        <link rel="stylesheet" type="text/css" href="res/style.css">
        <script language="javascript">

			function toggleElement(uid) {
				elm = document.getElementById(uid);
                classes = elm.classList;

				if ( elmHidden(uid) ){
					classes.remove("hidden");
					classes.add("visible");
				} else {
					classes.remove("visible");
					classes.add("hidden");
				}
			}

            function elmHidden(uid) {
                elm = document.getElementById(uid);
                classes = elm.classList;
                return classes.contains("hidden");
            }

            function closeMenu(evt) {
                // Clicking outside of menu closes it
                elm = document.getElementById("calendar");
                style = window.getComputedStyle(elm);
                menuWidth = style.width.split("px")[0];
                width = window.innerWidth - menuWidth;

                if ( !elmHidden("calendar") && evt.clientX < width ) {                  
                    toggleElement("calendar");
                    evt.preventDefault();
                }
            }            

            function init() {

                // Bind menu close to document click event
                document.onclick = function(evt) {
                    closeMenu(evt);
                } 

                // There is a bug where clicking on an iframe
                // does not trigger a document.onclick event
                // or even a window.onclick event.
                // Bind document click events to all child iframes
                // to circumvent this. 

                iframes = document.getElementsByTagName('iframe');
                iframesArray = Array.prototype.slice.apply(iframes);

                iframesArray.forEach(
                    function(frame) {
                        frame.contentWindow.document.addEventListener('click', function(evt) {
                            closeMenu(evt);
                        }, 
                    true);
                });
            }

        </script>
	</head>
	<body onload="init()">
		<div id="container">

            <div id="content"><?php requestPage() ?></div>
            <div id="calendar" class="visible">
                <?php buildPageLinks() ?>
            </div>
            <div id="calbutton">
                <?php buildNavigation() ?>
            </div>

        </div>
	</body>
</html>