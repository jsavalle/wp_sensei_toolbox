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
  //add_menu_page('Toolbox Page', 'Sensei Toolbox', 'manage_options', 'sensei_toolbox-slug', 'sensei_toolbox_admin_page');
  add_submenu_page('sensei', 'Sensei Toolbox Page', 'Sensei Toolbox', 'manage_options', 'sensei_toolbox-slug', 'sensei_toolbox_admin_page');
}




add_action('admin_enqueue_scripts', 'sense_toolbox_reg_css_and_js');

function sense_toolbox_reg_css_and_js($hook)
{
  //wp_die($hook);
  // Load only on ?page=mypluginname
  if($hook != 'sensei_page_sensei_toolbox-slug') {
    return;
  }
    //wp_enqueue_style( 'custom_wp_admin_css', plugins_url('admin-style.css', __FILE__) );

    wp_enqueue_style('boot_css', plugins_url('inc/bootstrap.css',__FILE__ ));
    wp_enqueue_style('boot_theme_css', plugins_url('inc/bootstrap_theme.css',__FILE__ ));
    wp_enqueue_style('toolbox_css', plugins_url('inc/toolbox.css',__FILE__ ));
    wp_enqueue_script('boot_js', plugins_url('inc/bootstrap.js',__FILE__ ), ['jquery'], false, true);
    wp_enqueue_script('toolbox_script', plugins_url('inc/toolbox.js', __FILE__), ['jquery'], false, true);
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

  echo '<div class="wrap sensei_toolbox">';

  echo '<h2>Sensei Toolbox</h2>';
  echo '<h3>Export emails from students</h3>';


  // Dropwdown for all courses
  $all_courses = WooThemes_Sensei_Course::get_all_courses();
  
  $course_list   = "<label for='course_id_selected'>Select a course : &nbsp;</label>";
  $course_list  .= "<select name='course_id_selected'>";
  $course_list  .= "<option value='0'>----------------------</option>";

  if (count($all_courses) > 0){
    foreach( $all_courses as $course ){
      $course_list .= "<option value='".$course->ID."'>".$course->post_title."</option>";
    }
    $course_list .= "</select>";
  }
  else {
    echo '<div id="message" class="error fade"><p>'
    .'No course was created yet .' . '</p></div></div>';
    exit();
  }
  // Check whether the button has been pressed AND also check the nonce
  if (isset($_POST['export_confirmed']) && check_admin_referer('export_confirmed_clicked')) {
    // the button has been pressed AND we've passed the security check
    $course_id_selected = $_POST['course_id_selected'];
    $status_selected = $_POST['status_selected'];
    export_emails_button_action($course_id_selected, $status_selected);
  }

  echo '<form action="admin.php?page=sensei_toolbox-slug" method="post">';

  // this is a WordPress security feature - see: https://codex.wordpress.org/WordPress_Nonces
  wp_nonce_field('export_confirmed_clicked');
  echo $course_list;
  echo '<div> 
          <label for="status_selected">For status : &nbsp;</label><br>
          <input type="radio" name="status_selected" value="complete"> Completed<br>
          <input type="radio" name="status_selected" value="in-progress" checked> In Progress<br>
        </div>';

  echo '<input type="hidden" value="true" name="export_confirmed" />';
  submit_button('Export', 'primary', 'export_button', true);
  echo '</form>';

  echo '</div>';

}

function export_emails_button_action($course_id_selected, $status_selected)
{
  echo '<div id="message" class="success fade"><p>'
  .'No course was created yet .' . '</p></div></div>';

  //print("<pre> ## ".$course_id_selected." ##</pre>");

    
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

  // print("<pre>");
  // print_r($user_statusses);
  // print("</pre>");


//Now get all the users
$users = array();
foreach( $user_statusses as $activity ){
	$users[] = get_user_by( 'id', $activity->user_id );
}

// print("<pre>\n****************\n");
// print_r($users);
// print("</pre>");

print("<pre>");
  foreach( $users as $student ){
    print($student->data->user_email).", \n";
  }  
print("</pre>");
}

?>