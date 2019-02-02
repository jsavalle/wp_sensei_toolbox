<?php

/*
Plugin Name: Sensei Toolbox 
Plugin URI: 
Description: Mini Toolbox for Sensei
Author:JS
Version: 0.0.1
Author URI:
*/


add_action('admin_menu', 'sensei_toolbox_menu');

function sensei_toolbox_menu(){
  add_menu_page('Toolbox Page', 'Extra Toolbox', 'manage_options', 'sensei_toolbox-slug', 'sensei_toolbox_admin_page');

}

function sensei_toolbox_admin_page() {

  // This function creates the output for the admin page.
  // It also checks the value of the $_POST variable to see whether
  // there has been a form submission. 

  // The check_admin_referer is a WordPress function that does some security
  // checking and is recommended good practice.

  // General check for user permissions.
  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient gold to access this page.')    );
  }

  // Start building the page

  echo '<div class="wrap">';

  echo '<h2>Toolbox</h2>';

  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['test_button']) && check_admin_referer('test_button_clicked')) {
    // the button has been pressed AND we've passed the security check
    test_button_action();
  }

  echo '<form action="options-general.php?page=sensei_toolbox-slug" method="post">';

  // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
  wp_nonce_field('test_button_clicked');
  echo '<input type="hidden" value="true" name="test_button" />';
  submit_button('Call Function');
  echo '</form>';

  echo '</div>';

}

function test_button_action()
{
  echo '<div id="message" class="updated fade"><p>'
    .'The "Call Function" button was clicked.' . '</p></div>';

    
  // Course ID can be found in the URL when you edit the course
  $course_id = '790';

  $activity_args = array(
    'post_id' => $course_id,
    'type' => 'sensei_course_status',
    //'status' => 'complete',
    'status' => 'in-progress',
  );


  // run WP_Comment_Query to get the activity on the course
  $user_statusses = WooThemes_Sensei_Utils::sensei_check_for_activity( $activity_args, true );

  print("<pre>");
  print_r($user_statusses);
  print("</pre>");


//Now get all the users
$users = array();
foreach( $user_statusses as $activity ){
	$users[] = get_user_by( 'id', $activity->user_id );
}

print("<pre>\n****************\n");
print_r($users);
print("</pre>");

foreach( $users as $student ){
  print("<pre>");
  print_r($student->data);
  print("</pre>");
}  

?>
