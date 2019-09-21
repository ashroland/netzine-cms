<?php 
define( 'INCLUDE_DIR', dirname( __FILE__ ) . '/res/src' );

$rules = array( 
    'chapter_page' => '/(.*)\/(.*)/',
    'menu_page' => '/(.*)/'
);

$uri = rtrim( dirname($_SERVER["SCRIPT_NAME"]), '/' );
$uri = trim( str_replace( $uri, '', $_SERVER['REQUEST_URI'] ), '/' );
$uri = urldecode( $uri );


foreach ( $rules as $action => $rule ) {
    // echo var_dump($action) . "<br />";
    // echo var_dump($rule) . "<br />";
    // echo var_dump($uri);

    if (preg_match( $rule, $uri, $output_array )) {

        switch ($action) {
            case 'chapter_page':
                include('/index.php?p=' . $uri);
                break;
            case 'menu_page':
                include('/index.php?m=' . $uri);
                break;
            default:
                include('/index.php');
        }

        // exit to avoid the 404 message 
        exit();
    }
}

// nothing is found so handle the 404 error
include( INCLUDE_DIR . '/404.php' );

?>