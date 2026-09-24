<?php

/*****************************************
* Weaver's Web Functions & Definitions *
*****************************************/
$functions_path = get_template_directory().'/functions/';
// $post_type_path = get_template_directory().'/inc/post-types/';
/*--------------------------------------*/
/* Optional Panel Helper Functions
/*--------------------------------------*/
require_once($functions_path.'admin-functions.php');
require_once($functions_path.'admin-interface.php');
require_once($functions_path.'theme-options.php');


// --------------------------------------------SVG format supporter----------------------------------------------
add_filter('wp_check_filetype_and_ext', function ($data, $file, $filename, $mimes) {
	$filetype = wp_check_filetype($filename, $mimes);
	return [
		'ext' => $filetype['ext'],
		'type' => $filetype['type'],
		'proper_filename' => $data['proper_filename']
	];

}, 10, 4);

function cc_mime_types($mimes)
{
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');

function fix_svg()
{
	echo '<style type="text/css">
		  .attachment-266x266, .thumbnail img {
			   width: 100% !important;
			   height: auto !important;
		  }
		  </style>';
}
add_action('admin_head', 'fix_svg');

// ---------------------------------------------------------------------------------------------------------------------
function weaversweb_ftn_wp_enqueue_scripts(){
    if(!is_admin()){
        wp_enqueue_script('jquery');
        if(is_singular()and get_site_option('thread_comments')){
            wp_print_scripts('comment-reply');
			}
		}
	}
add_action('wp_enqueue_scripts','weaversweb_ftn_wp_enqueue_scripts');
function weaversweb_ftn_get_option($name){
    $options = get_option('weaversweb_ftn_options');
    if(isset($options[$name]))
        return $options[$name];
	}
function weaversweb_ftn_update_option($name, $value){
    $options = get_option('weaversweb_ftn_options');
    $options[$name] = $value;
    return update_option('weaversweb_ftn_options', $options);
	}
function weaversweb_ftn_delete_option($name){
    $options = get_option('weaversweb_ftn_options');
    unset($options[$name]);
    return update_option('weaversweb_ftn_options', $options);
	}
function get_theme_value($field){
	$field1 = weaversweb_ftn_get_option($field);
	if(!empty($field1)){
		$field_val = $field1;

		}
	return	$field_val;
	}
/*--------------------------------------*/
/* Post Type Helper Functions
/*--------------------------------------*/

// require_once($post_type_path.'testimonials.php');
// require_once($post_type_path.'teams.php');
// require_once($post_type_path.'projects.php');
// require_once($post_type_path.'perks.php');

/*--------------------------------------*/
/* Theme Helper Functions
/*--------------------------------------*/
if(!function_exists('weaversweb_theme_setup')):
	function weaversweb_theme_setup(){
		add_theme_support('title-tag');
		add_theme_support('post-thumbnails');
		register_nav_menus(array(
			'primary' => __('Primary Menu','weaversweb'),
			'secondary'  => __('Secondary Menu','weaversweb'),
			));

		add_theme_support('html5',array('search-form','comment-form','comment-list','gallery','caption'));
		}
	endif;
add_action('after_setup_theme','weaversweb_theme_setup');
function weaversweb_widgets_init(){
	register_sidebar(array(
		'name'          => __('Widget Area','weaversweb'),

		'id'            => 'sidebar-1',

		'description'   => __('Add widgets here to appear in your sidebar.','weaversweb'),

		'before_widget' => '<div id="%1$s" class="widget %2$s">',

		'after_widget'  => '</div>',

		'before_title'  => '<h2 class="widget-title">',

		'after_title'   => '</h2>',
		));

	}
add_action('widgets_init','weaversweb_widgets_init');




function weaversweb_scripts(){
	wp_enqueue_style('bootstrap.min.css',get_template_directory_uri().'/assets/css/bootstrap.min.css',array());

	wp_enqueue_style('font-awesome-all.min.css',get_template_directory_uri().'/assets/css/font-awesome-all.min.css',array());

	wp_enqueue_style('fonts.css',get_template_directory_uri().'/assets/css/fonts.css',array());
	
    wp_enqueue_style('style.css',get_template_directory_uri().'/style.css',array()); 

   	wp_enqueue_style('custom.css',get_template_directory_uri().'/assets/css/custom.css',array());
	// Load the Internet Explorer specific script.

	global $wp_scripts;

    wp_enqueue_script('bootstrap.bundle.min.js',get_template_directory_uri().'/assets/js/bootstrap.bundle.min.js',array('jquery'),time(),true);

    wp_enqueue_script('bootstrap.bundle.min.js.map',get_template_directory_uri().'/assets/js/bootstrap.bundle.min.js.map',array('jquery'),time(),true);

	wp_enqueue_script('font-awesome-all.min.js',get_template_directory_uri().'/assets/js/font-awesome-all.min.js',array('jquery'),time(),true);

	//wp_enqueue_script('jquery.js',get_template_directory_uri().'/js/jquery.min.js',array('jquery'),time(),true);

  	 wp_register_script('custom.js',get_template_directory_uri().'/assets/js/custom.js',array(),1,1,1);
	 wp_enqueue_script('custom.js');

    
	}
add_action('wp_enqueue_scripts','weaversweb_scripts');
add_action('wp_head','hook_javascript');
function hook_javascript() {
?>

<script type="text/javascript">
	var ajaxurl = "<?php echo admin_url('admin-ajax.php')  ?>";
    
</script>


<?php 
}


function all_post(){
    $ip=$_SERVER['REMOTE_ADDR'];
    $page = ($_POST['page']) ? $_POST['page'] : 1;
    $s = $_POST['search'];
  print_r($page);
    $args = array(
    'post_type'      => 'post', // You can change this to your custom post type if needed
    'posts_per_page' => 5,     // Number of posts to display
    'orderby'        => 'date', // Sort by date
   //'order'          => 'DESC', // Show the latest posts first
    'paged'          => $page,  // Current page number
    's' => $s,
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) :
    while ($query->have_posts()) :
         $query->the_post();
         $post_id=get_the_ID( );

 // Get the time difference
	$post_time = get_the_time('U', $post_id);
	$current_time = current_time('timestamp');
	$time_diff = human_time_diff($post_time, $current_time);

  $content = get_the_content();
  $trimmed_content = wp_trim_words($content, 50, '...');
     ?>

            <div class="card-wrap post-each light-gray-bg" >
            <div class="top-comment-each">
                <ul class="user-info d-flex justify-content-between">
                  <li><?php echo "#".get_the_ID();  ?></li>
                  <li><?php echo esc_html($time_diff);?> ago</li> 


                </ul>
                <div class="post-top-text">
                  <h3><?php the_title();?></h3>
                  <p><?php echo $trimmed_content;?><span><a href="<?php the_permalink();?>">Read More</a></span></p>
                </div>
                
                <div class="reply-block">
                    <ul class="post-view d-flex justify-content-end">
                      <li>73 all reactions</li>
                      <li>10 shares</li>
                      <li><?php echo report_count($post_id);?> report</li>
                    </ul>
                    <div class="reply-bottom-info">
                      <ul class="icon-group d-flex align-items-center list-unstyled">
                                                            
                        <li><a class="icon-like" style="<?php echo is_like($post_id,$ip);?>" onclick="button_action('like',<?php echo $post_id;?>,event);" href=""></a><span>  <?php echo like_count($post_id);?></span></li>
                        <li><a class="icon-Dislike" style="<?php echo is_dislike($post_id,$ip);?>" onclick="button_action('dislike',<?php echo $post_id;?>,event)" href=""></a><span><?php echo dislike_count($post_id);?></span></li>
                        <li><a class="icon-comment" href=""></a><span>3</span></li>
                        <li><a class="icon-share" href=""></a><span>8</span></li>
                        <li><a class="icon-report" style="<?php echo is_report($post_id,$ip);?>" onclick="button_action('report',<?php echo $post_id;?>,event)" href=""></a><span><?php echo report_count($post_id);?></span></li>
                      </ul>
<!-- ===============================parent comment0 form ==================================== -->               
                      <div class="comment-block d-flex align-items-center">
                        <textarea class="form-control" id="commentForm<?php echo $post_id;?>" placeholder="Write your comments"></textarea>
                        <div class="send-icon">
                          <a href="" onclick="add_comment(<?php echo $post_id;?>,0,'commentForm<?php echo $post_id;?>',event);" id="send_comment"><span class="icon-send" id="send_comment"></span></a>
                        </div>
                      </div>
                    </div>
                </div>
 </div>              
              
<!-- ================================================to fetch parent (comment 0) ========================================================= -->
<div class="sub-comment">
                <?php
                     $parent_comment = get_comments(array(
                      'post_id' => $post_id,
                      'parent'=>'0',
                      'number'  => 2, //Display only 2 comments initially
                      
                  ));


                  if ($parent_comment) {
                    
                    foreach ($parent_comment as $comment0) {
                      $comment0_post_id=$comment0->comment_post_ID;
                      $comment0_id = $comment0->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment0->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff0 = human_time_diff($comment_time, $current_time);
                  
                       
                    
               ?>
               
                <div class=" top-comment-each">
                  <ul class="user-info d-flex justify-content-between">
                    <li>parent</li>
                    <li><?php echo $time_diff0;?></li>
                  </ul>
                  <div class="post-top-text">
                    <p><?php echo esc_html($comment0->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment0_id,0);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment0_id,0);?></span></li>
                          <li><a class="icon-comment" href=""></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment0_id,0);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!-- =====================================1st child (comment1) form================================== -->
                          <textarea class="form-control" id="commentForm1<?php echo $comment0_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment0_post_id;?>,<?php echo $comment0_id;?>,'commentForm1<?php echo $comment0_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
<!-- ================================================to fetch 1st child (comment 1) ========================================================= -->
                        <?php
                     $child1 = get_comments(array(
                      'post_id' => $comment0_post_id,
                      'parent'=>$comment0_id,
                       
                  ));
                  if ($child1) {
                    foreach ($child1 as $comment1) {
                      $comment1_post_id=$comment1->comment_post_ID;
                      $comment1_id = $comment1->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment1->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff1 = human_time_diff($comment_time, $current_time);
                    
                     ?>
                  <div class="top-comment-each sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>Child 1</li>
                    <li><?php echo $time_diff1;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment1->comment_content);?></p>
                
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment1_id,$comment0_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment1_id,$comment0_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment1_id,$comment0_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 2nd child (comment2 ) form==================================== -->
                          <textarea class="form-control" id="commentForm2<?php echo $comment1_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment1_post_id;?>,<?php echo $comment1_id;?>,'commentForm2<?php echo $comment1_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div><br>
                        
                      </div>
                  </div>
               </div>  
 <!-- ================================================to fetch 2nd child (comment 2) ========================================================= -->

               <?php
                     $child2 = get_comments(array(
                      'post_id' => $comment1_post_id,
                      'parent'=>$comment1_id,
                       
                  ));
                  if ($child2) {
                    foreach ($child2 as $comment2) {
                      $comment2_post_id=$comment2->comment_post_ID;
                      $comment2_id = $comment2->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment2->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff2 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 2</li>
                    <li><?php echo $time_diff2;?>ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment2->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment2_id,$comment1_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment2_id,$comment1_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment2_id,$comment1_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 3rd child (comment3 ) form==================================== -->
                          <textarea class="form-control" id="commentForm3<?php echo $comment2_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment2_post_id;?>,<?php echo $comment2_id;?>,'commentForm3<?php echo $comment2_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
               
<!-- ================================================to fetch 3rd child (comment 3) ========================================================= -->

<?php
                     $child3 = get_comments(array(
                      'post_id' => $comment2_post_id,
                      'parent'=>$comment2_id,
                       
                  ));
                 
                  if ($child3) {
                    foreach ($child3 as $comment3) {
                      $comment3_post_id=$comment3->comment_post_ID;
                      $comment3_id = $comment3->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment3->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff3 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub3_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 3</li>
                    <li><?php echo $time_diff3;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment3->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment3_id,$comment2_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment3_id,$comment2_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment3_id,$comment2_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 4th child (comment 4) form==================================== -->
                          <textarea class="form-control" id="commentForm4<?php echo $comment3_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment3_post_id;?>,<?php echo $comment3_id;?>,'commentForm4<?php echo $comment3_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
               
<!-- ================================================to fetch 4th child (comment 4) ========================================================= -->

<?php
                     $child4 = get_comments(array(
                      'post_id' => $comment3_post_id,
                      'parent'=>$comment3_id,
                       
                  ));
                  if ($child4) {
                    foreach ($child4 as $comment4) {
                      $comment4_post_id=$comment4->comment_post_ID;
                      $comment4_id = $comment4->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment4->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff4 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub4_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 4</li>
                    <li><?php echo $time_diff4;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment4->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment4_id,$comment3_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment4_id,$comment3_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment4_id,$comment3_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 5th child (comment 4) form==================================== -->
                          <textarea class="form-control" id="commentForm5<?php echo $comment4_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment4_post_id;?>,<?php echo $comment4_id;?>,'commentForm5<?php echo $comment4_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
                        
               
<!-- ================================================to fetch 5th child (comment 5) ========================================================= -->

<?php
                     $child5 = get_comments(array(
                      'post_id' => $comment4_post_id,
                      'parent'=>$comment4_id,
                       
                  ));
                  if ($child5) {
                    foreach ($child5 as $comment5) {
                      $comment5_post_id=$comment5->comment_post_ID;
                      $comment5_id = $comment5->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment5->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff5 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub5_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 5</li>
                    <li><?php echo $time_diff5;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment5->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment5_id,$comment4_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment5_id,$comment4_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                      
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment5_id,$comment4_id);?></span></li>
                        </ul>
                       
                      </div>
                  </div>
               </div>   


               
               <?php
              } //for each 5th
            } //if 5th 
              } //for each 4th
              } //if 4th
              } //for each 3rd
              } //if 3rd
              } //for each 2nd
               }//if 2nd
            } //for each 1st?>
            <!-- ======================span=============== -->
           
          <?php 
              }//if 1st
            
               ?> 

</div>
</div>
</div>

<?php  }//for each comment?>
<br><?php echo "<span id='comments-list_$post_id'></span>"; ?>
<a href="" style="color:green;font-size: 17px;" data-post-id="<?php echo $post_id;?>" id="load-more"  >Load More..</a>
<?php   }//if comment
   
else {
  echo "<p>No comments yet..</p>";
}
?>
                 
</div> 
  </div>                                              
     </div>
</div><?php 
  endwhile;?>
            <div class="category_pagination light-gray-bg">
        <?php
         // Add pagination links
         echo paginate_links(array(
            'base' => get_pagenum_link(1) . '%_%',
            'format' => 'page/%#%/',
            'total' => $query->max_num_pages,
            'current' => max(1, $page),
            'show_all' => false,
            'end_size' => 1,
            'mid_size' => 2,
            'prev_next' => true,
            'prev_text' => __('Previous', 'test'),
            'next_text' => __('Next', 'test'),
            'type' => 'plain', // Change to 'array' if you want to customize the HTML
         ));
         ?>
         </div>
         <?php
    else :
        echo '<h3 class="card-wrap new-post light-gray-bg" style="color:red">  No posts found!</h3>';
    
endif;
wp_die();
   }
add_action('wp_ajax_all_post', 'all_post');
add_action('wp_ajax_nopriv_all_post', 'all_post');

// function save_form_data() {
  
//         // Extract form data
//         $title = sanitize_text_field($_POST['title']);
//         $content = sanitize_text_field($_POST['content']);

//         // Create a new post with the submitted data
//         $post_data = array(
//             'post_title'   => "$title",
//             // 'post_type'    => 'post', // Replace with your custom post type slug
//             'post_status'  => 'publish',
//             'post_content' => "$content",
//         );

//         // Insert the post and get the post ID
//          $post_id = wp_insert_post($post_data);
//          echo "done";
     
// }
// add_action('wp_ajax_save_form_data', 'save_form_data');
// add_action('wp_ajax_nopriv_save_form_data', 'save_form_data');



function submit_form_data() {
   
    parse_str($_POST['form_data'], $form_data_array);

    $title = sanitize_text_field($form_data_array['title']);
    $content = sanitize_text_field($form_data_array['content']);

   
    // Create a new post with the submitted data
    $post_data = array(
        'post_title' => $title,
        'post_content' => $content,
        'post_type' => 'post', // Adjust if using a custom post type
        'post_status' => 'publish',
    );

    // Insert the post
    $post_id = wp_insert_post($post_data);

    if ($post_id) {
        echo 'Post created successfully!';
    } else {
        echo 'Error creating post.';
    }

    wp_die(); // Always include this at the end of the AJAX callback
}
add_action('wp_ajax_submit_form_data', 'submit_form_data');
add_action('wp_ajax_nopriv_submit_form_data', 'submit_form_data');

function submit_comment(){
 
  $ip=$_SERVER['REMOTE_ADDR'];
  $comment_content = $_POST['comment_content'];
  $post_id=$_POST['post_id'];
  $parent=$_POST['parent'];
 $response=$post_id;
 
 
 
  $comment_data = array(
    'comment_post_ID' => $post_id,  
    'comment_parent'=>$parent,
    'comment_content' => $comment_content,
    'comment_author_IP'=>$ip,
    //'comment_author'  => get_user_by('ID', $user_id)->display_name,
  
  );
  $comment_id = wp_insert_comment($comment_data);

    if ($comment_id) {
        $respose='Comment submitted successfully!';
    } else {
        $response= 'Error submitting comment.';
    }
    echo $response;
    wp_die();


}
add_action('wp_ajax_submit_comment', 'submit_comment');
add_action('wp_ajax_nopriv_submit_comment', 'submit_comment');

function like_dislike_click() {
    $post_id = $_POST['post_id'];
    $button_action = $_POST['button_action']; //like or dislike
    $ip=$_SERVER['REMOTE_ADDR'];
    global $wpdb;
    

    $query = "SELECT * FROM `wp_like_dislike` WHERE `ip`='$ip' AND `post_id`='$post_id'";
    $result = $wpdb->get_results($query);
    if(count($result)==0){
        $data_to_insert = array(
            'ip' => $ip,
            $button_action.'s' => 1,
            'post_id'=>$post_id,
        );
        
        $wpdb->insert('wp_like_dislike', $data_to_insert);
    }
    else if(count($result)>0){
        //existing ip and post
        foreach ($result as $row) {

            switch($button_action){
                case 'like':
                    
                    if($row->likes==1){
                        //for cliking like in already liked post
                       
                        $data_to_update = array(
                            'likes' => 0,
                        ); //like removed
                        $where_clause = array(
                            'ip' => $ip,
                            'post_id'=>$post_id,
                            
                        ); 
                        $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                        $response=0;
                        
                    }
                    else {
                       
                         //dislike removed, like added
                         $data_to_update = array(
                            'likes' => 1,
                            'dislikes'=>0,
                        );
                        $where_clause = array(
                            'ip' => $ip,
                            'post_id'=>$post_id,
                        ); 
                        $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                        $response=1;
                    }
                    break;

                case 'dislike':
                    
                    if($row->dislikes==1){
                        //for cliking dislike in already disliked post
                       
                        $data_to_update = array(
                            'dislikes' => 0,
                        ); //dislike removed
                        $where_clause = array(
                            'ip' => $ip,
                            'post_id'=>$post_id,
                        ); 
                        $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                        $response=0;
                        
                    }
                    else {
                       
                         //like removed, dislike added
                         $data_to_update = array(
                            'dislikes' => 1,
                            'likes'=>0,
                        );
                        $where_clause = array(
                            'ip' => $ip,
                            'post_id'=>$post_id,
                        ); 
                        $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                        $response=2;
                    }
                    break;

                case 'report':
                        
                        if($row->reports==1){

                        //2nd time clicking on repor     
                        $data_to_update = array(
                                'reports' => 0,
                            ); //report removed
                            $where_clause = array(
                                'ip' => $ip,
                                'post_id'=>$post_id,
                            ); 
                            $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                            $response=0;
                            
                        }
                        else {
                           
                             //report added
                             $data_to_update = array(
                                'reports' => 1,
                            );
                            $where_clause = array(
                                'ip' => $ip,
                                'post_id'=>$post_id,
                            ); 
                            $wpdb->update('wp_like_dislike', $data_to_update, $where_clause);
                            $response=3;
                        }
                        break;
            }

        }
    }
    

    
  echo $response ;
   
    wp_die();

}

add_action('wp_ajax_like_dislike_click', 'like_dislike_click');
add_action('wp_ajax_nopriv_like_dislike_click', 'like_dislike_click');


function comments_like_dislike_click() {
  $post_id = $_POST['post_id'];
  $button_action = $_POST['button_action']; //like or dislike or report
  $comment_id=$_POST['comment_id'];
  $parent=$_POST['parent'];
  $ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  

  $query = "SELECT * FROM `wp_like_dislike_comments` WHERE `ip`='$ip' 
  AND `post_id`='$post_id' AND `comment_id`='$comment_id' AND `parent`='$parent'";
  //echo $query;die;
  $result = $wpdb->get_results($query);
  
  if(count($result)==0){
      $data_to_insert = array(
          'ip' => $ip,
          $button_action.'s' => 1,
          'post_id'=>$post_id,
          'comment_id'=>$comment_id,
          'parent'=>$parent
      );
      
      $wpdb->insert('wp_like_dislike_comments', $data_to_insert);
  }
  else if(count($result)>0){
      //existing ip and post
      foreach ($result as $row) {

          switch($button_action){
              case 'like':
                  
                  if($row->likes==1){
                      //for cliking like in already liked post
                     
                      $data_to_update = array(
                          'likes' => 0,
                      ); //like removed
                      $where_clause = array(
                          'ip' => $ip,
                          'post_id'=>$post_id,
                          'comment_id'=>$comment_id,
                          'parent'=>$parent
                          
                      ); 
                      $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                      $response=0;
                      
                  }
                  else {
                     
                       //dislike removed, like added
                       $data_to_update = array(
                          'likes' => 1,
                          'dislikes'=>0,
                      );
                      $where_clause = array(
                          'ip' => $ip,
                          'post_id'=>$post_id,
                          'comment_id'=>$comment_id,
                          'parent'=>$parent
                      ); 
                      $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                      $response=1;
                  }
                  break;

              case 'dislike':
                  
                  if($row->dislikes==1){
                      //for cliking dislike in already disliked post
                     
                      $data_to_update = array(
                          'dislikes' => 0,
                      ); //dislike removed
                      $where_clause = array(
                          'ip' => $ip,
                          'post_id'=>$post_id,
                          'comment_id'=>$comment_id,
                          'parent'=>$parent
                      ); 
                      $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                      $response=0;
                      
                  }
                  else {
                     
                       //like removed, dislike added
                       $data_to_update = array(
                          'dislikes' => 1,
                          'likes'=>0,
                      );
                      $where_clause = array(
                          'ip' => $ip,
                          'post_id'=>$post_id,
                          'comment_id'=>$comment_id,
                          'parent'=>$parent
                      ); 
                      $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                      $response=2;
                  }
                  break;

              case 'report':
                      
                      if($row->reports==1){

                      //2nd time clicking on repor     
                      $data_to_update = array(
                              'reports' => 0,
                          ); //report removed
                          $where_clause = array(
                              'ip' => $ip,
                              'post_id'=>$post_id,
                              'comment_id'=>$comment_id,
                              'parent'=>$parent
                          ); 
                          $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                          $response=0;
                          
                      }
                      else {
                         
                           //report added
                           $data_to_update = array(
                              'reports' => 1,
                          );
                          $where_clause = array(
                              'ip' => $ip,
                              'post_id'=>$post_id,
                              'comment_id'=>$comment_id,
                              'parent'=>$parent
                          ); 
                          $wpdb->update('wp_like_dislike_comments', $data_to_update, $where_clause);
                          $response=3;
                      }
                      break;
          }

      }
  }
  
echo $response ;
 wp_die();
}

add_action('wp_ajax_comments_like_dislike_click', 'comments_like_dislike_click');
add_action('wp_ajax_nopriv_comments_like_dislike_click', 'comments_like_dislike_click');

function like_count($post_id){
    global $wpdb;
   
    $query1 = "SELECT SUM(`likes`) as like_count FROM `wp_like_dislike` WHERE `post_id`='$post_id'";
    $like = $wpdb->get_results($query1);
    $like_count = $like[0]->like_count;
    return $like_count;
   // wp_die();
}


function dislike_count($post_id){
    global $wpdb;
    $query1 = "SELECT SUM(`dislikes`) as dislike_count FROM `wp_like_dislike` WHERE `post_id`='$post_id'";
    $dislike = $wpdb->get_results($query1);
    $dislike_count = $dislike[0]->dislike_count;
    return $dislike_count;  
}

function report_count($post_id){
  global $wpdb;
  $query = "SELECT SUM(`reports`) as report_count FROM `wp_like_dislike` WHERE `post_id`='$post_id'";
  $report = $wpdb->get_results($query);
  $report_count = $report[0]->report_count;
  return $report_count;  
}


function is_like($post_id,$ip){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `likes` FROM `wp_like_dislike` WHERE `post_id`='$post_id' AND `ip`='$ip' AND `likes`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:green";
  }
  return $style;
}
function is_dislike($post_id,$ip){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `dislikes` FROM `wp_like_dislike` WHERE `post_id`='$post_id' AND `ip`='$ip' AND `dislikes`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:green";
  }
  return $style;
}
function is_report($post_id,$ip){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `reports` FROM `wp_like_dislike` WHERE `post_id`='$post_id' AND `ip`='$ip' AND `reports`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:red";
  }
  return $style;
}

function comment_like_count($post_id,$comment_id,$parent){
  global $wpdb;
 
  $query = "SELECT SUM(`likes`) as like_count FROM `wp_like_dislike_comments` 
  WHERE `post_id`='$post_id' AND `comment_id`='$comment_id' AND `parent`='$parent'";

  $like = $wpdb->get_results($query);
  $like_count = $like[0]->like_count;
  return $like_count;
 // wp_die();
}
function comment_dislike_count($post_id,$comment_id,$parent){
  global $wpdb;
 
  $query = "SELECT SUM(`dislikes`) as dislike_count FROM `wp_like_dislike_comments` 
  WHERE `post_id`='$post_id' AND `comment_id`='$comment_id' AND `parent`='$parent'";

  $dislike = $wpdb->get_results($query);
  $dislike_count = $dislike[0]->dislike_count;
  return $dislike_count;
 // wp_die();
}
function comment_report_count($post_id,$comment_id,$parent){
  global $wpdb;
 
  $query = "SELECT SUM(`reports`) as report_count FROM `wp_like_dislike_comments` 
  WHERE `post_id`='$post_id' AND `comment_id`='$comment_id' AND `parent`='$parent'";

  $report = $wpdb->get_results($query);
  $report_count = $report[0]->report_count;
  return $report_count;
 // wp_die();
}

function is_like_comment($post_id,$ip,$comment_id,$parent){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `likes` FROM `wp_like_dislike_comments` WHERE 
  `post_id`='$post_id' AND `ip`='$ip' AND `comment_id`='$comment_id' AND `parent`='$parent' AND `likes`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:green";
  }
  return $style;
}
function is_dislike_comment($post_id,$ip,$comment_id,$parent){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `dislikes` FROM `wp_like_dislike_comments` WHERE 
  `post_id`='$post_id' AND `ip`='$ip' AND `comment_id`='$comment_id' AND `parent`='$parent' AND `dislikes`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:green";
  }
  return $style;
}
function is_report_comment($post_id,$ip,$comment_id,$parent){
 
  //$ip=$_SERVER['REMOTE_ADDR'];
  global $wpdb;
  $query = "SELECT `reports` FROM `wp_like_dislike_comments` WHERE 
  `post_id`='$post_id' AND `ip`='$ip' AND `comment_id`='$comment_id' AND `parent`='$parent' AND `reports`='1'";
  $active = $wpdb->get_results($query);
  if($active[0]>0){
    $style= "color:red";
  }
  return $style;
}


function load_more_comments() {
  $ip=$_SERVER['REMOTE_ADDR'];
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
    
   // $comment_id = $_POST['comment_id'];
if($post_id>0){


   
                     $parent_comment = get_comments(array(
                      'post_id' => $post_id,
                      'parent'=>'0',
                      'number'  => 2, //Display only 2 comments initially
                      'offset'  => $offset, // Use the offset provided by the AJAX request
                      
                  ));
//print_r($parent_comment[0]->comment_ID);die;

                  if ($parent_comment) { 
                    foreach ($parent_comment as $comment0) {
                      $comment0_post_id=$comment0->comment_post_ID;
                      $comment0_id = $comment0->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment0->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff0 = human_time_diff($comment_time, $current_time);
                  
                       
                    
               ?>
                <div class="top-comment-each">
                  <ul class="user-info d-flex justify-content-between">
                    <li>parent</li>
                    <li><?php echo $time_diff0;?></li>
                  </ul>
                  <div class="post-top-text">
                    <p><?php echo esc_html($comment0->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment0_id,0);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment0_id,0);?></span></li>
                          <li><a class="icon-comment" href=""></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment0_id,0);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment0_id;?>,0,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment0_id,0);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!-- =====================================1st child (comment1) form================================== -->
                          <textarea class="form-control" id="commentForm1<?php echo $comment0_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment0_post_id;?>,<?php echo $comment0_id;?>,'commentForm1<?php echo $comment0_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
          <!-- ================================================to fetch 1st child (comment 1) ========================================================= -->
                        <?php
                     $child1 = get_comments(array(
                      'post_id' => $comment0_post_id,
                      'parent'=>$comment0_id,
                       
                  ));
                  if ($child1) {
                    foreach ($child1 as $comment1) {
                      $comment1_post_id=$comment1->comment_post_ID;
                      $comment1_id = $comment1->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment1->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff1 = human_time_diff($comment_time, $current_time);
                    
                     ?>
                  <div class="top-comment-each sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>Child 1</li>
                    <li><?php echo $time_diff1;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment1->comment_content);?></p>
                
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment1_id,$comment0_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment1_id,$comment0_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment1_id,$comment0_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment1_id;?>,<?php echo $comment0_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment1_id,$comment0_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 2nd child (comment2 ) form==================================== -->
                          <textarea class="form-control" id="commentForm2<?php echo $comment1_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment1_post_id;?>,<?php echo $comment1_id;?>,'commentForm2<?php echo $comment1_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div><br>
                        
                      </div>
                  </div>
               </div>  
 <!-- ================================================to fetch 2nd child (comment 2) ========================================================= -->

               <?php
                     $child2 = get_comments(array(
                      'post_id' => $comment1_post_id,
                      'parent'=>$comment1_id,
                       
                  ));
                  if ($child2) {
                    foreach ($child2 as $comment2) {
                      $comment2_post_id=$comment2->comment_post_ID;
                      $comment2_id = $comment2->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment2->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff2 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 2</li>
                    <li><?php echo $time_diff2;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment2->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment2_id,$comment1_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment2_id,$comment1_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment2_id,$comment1_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment2_id;?>,<?php echo $comment1_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment2_id,$comment1_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 3rd child (comment3 ) form==================================== -->
                          <textarea class="form-control" id="commentForm3<?php echo $comment2_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment2_post_id;?>,<?php echo $comment2_id;?>,'commentForm3<?php echo $comment2_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
               
<!-- ================================================to fetch 3rd child (comment 3) ========================================================= -->

<?php
                     $child3 = get_comments(array(
                      'post_id' => $comment2_post_id,
                      'parent'=>$comment2_id,
                       
                  ));
                 
                  if ($child3) {
                    foreach ($child3 as $comment3) {
                      $comment3_post_id=$comment3->comment_post_ID;
                      $comment3_id = $comment3->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment3->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff3 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub3_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 3</li>
                    <li><?php echo $time_diff3;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment3->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment3_id,$comment2_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment3_id,$comment2_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment3_id,$comment2_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment3_id;?>,<?php echo $comment2_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment3_id,$comment2_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 4th child (comment 4) form==================================== -->
                          <textarea class="form-control" id="commentForm4<?php echo $comment3_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment3_post_id;?>,<?php echo $comment3_id;?>,'commentForm4<?php echo $comment3_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
               
<!-- ================================================to fetch 4th child (comment 4) ========================================================= -->

<?php
                     $child4 = get_comments(array(
                      'post_id' => $comment3_post_id,
                      'parent'=>$comment3_id,
                       
                  ));
                  if ($child4) {
                    foreach ($child4 as $comment4) {
                      $comment4_post_id=$comment4->comment_post_ID;
                      $comment4_id = $comment4->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment4->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff4 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub4_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 4</li>
                    <li><?php echo $time_diff4;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment4->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment4_id,$comment3_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment4_id,$comment3_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                          <!-- <li><a class="icon-share" href="#"></a><span>8</span></li> -->
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment4_id,$comment3_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment4_id;?>,<?php echo $comment3_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment4_id,$comment3_id);?></span></li>
                        </ul>
                        <div class="comment-block d-flex align-items-center">
                          <!--=============================== 5th child (comment 4) form==================================== -->
                          <textarea class="form-control" id="commentForm5<?php echo $comment4_id;?>" placeholder="Reply to this comment"></textarea>
                          <div class="send-icon">
                            <a href="" onclick="add_comment(<?php echo $comment4_post_id;?>,<?php echo $comment4_id;?>,'commentForm5<?php echo $comment4_id;?>',event);" id="send_comment"><span class="icon-send"></span></a>
                          </div>
                        </div>
                      </div>
                  </div>
               </div>   
                        
               
<!-- ================================================to fetch 5th child (comment 5) ========================================================= -->

<?php
                     $child5 = get_comments(array(
                      'post_id' => $comment4_post_id,
                      'parent'=>$comment4_id,
                       
                  ));
                  if ($child5) {
                    foreach ($child5 as $comment5) {
                      $comment5_post_id=$comment5->comment_post_ID;
                      $comment5_id = $comment5->comment_ID; 

                      // Get the time difference
                      $comment_time = strtotime($comment5->comment_date); // Convert MySQL datetime to Unix timestamp
                      $current_time = current_time('timestamp'); // Get the current time as a Unix timestamp
                      $time_diff5 = human_time_diff($comment_time, $current_time);
                    
                    
                     ?>
                  <div class="top-comment-each sub5_sub-sub-comment">
                  <ul class="user-info d-flex justify-content-between">
                    <li>child 5</li>
                    <li><?php echo $time_diff5;?> ago</li>
                  </ul>
                  <div class="post-top-text">
                  <p><?php echo esc_html($comment5->comment_content);?></p>
                  </div>
                  <div class="reply-block">
                      <div class="reply-bottom-info">
                        <ul class="icon-group d-flex align-items-center list-unstyled">
                          <li><a class="icon-like" style="<?php echo is_like_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('like',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_like_count($post_id,$comment5_id,$comment4_id);?></span></li>
                          <li><a class="icon-Dislike" style="<?php echo is_dislike_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('dislike',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_dislike_count($post_id,$comment5_id,$comment4_id);?></span></li>
                          <li><a class="icon-comment" href="#"></a><span>3</span></li>
                      
                          <li><a class="icon-report" style="<?php echo is_report_comment($post_id,$ip,$comment5_id,$comment4_id);?>" onclick="comment_button_action('report',<?php echo $post_id;?>,<?php echo $comment5_id;?>,<?php echo $comment4_id;?>,event);" href=""></a><span><?php echo comment_report_count($post_id,$comment5_id,$comment4_id);?></span></li>
                        </ul>
                       
                      </div>
                  </div>
               </div>   


               
               <?php
              } //for each 5th
            } //if 5th 
              } //for each 4th
              } //if 4th
              } //for each 3rd
              } //if 3rd
              } //for each 2nd
               }//if 2nd
            } //for each 1st
              }//if 1st
               ?>

</div>
</div>
</div>
<?php }//for each comment?>
<?php  }//if comment 
   
 }else{
 // $data=array('value'=>'null');
  echo "null";
 }  
 wp_die(); // Always include this at the end of the AJAX callback
}
add_action('wp_ajax_load_more_comments', 'load_more_comments');
add_action('wp_ajax_nopriv_load_more_comments', 'load_more_comments');



// function custom_pagination($custom_query) {
//     $total_pages = $custom_query->max_num_pages;

//     if ($total_pages > 1) {
//         $current_page = max(1, get_query_var('paged'));

       

//         // Numbered pages
//         echo paginate_links(array(
//             'base'      => get_pagenum_link(1) . '%_%',
//             'format'    => 'page/%#%',
//             'current'   => $current_page,
//             'total'     => $total_pages,
          
//             'before_page_number' => '<li class="active">',
//             'after_page_number'  => '</li>',
//         ));

       

        
//     }
// }

// if (!function_exists('ic_custom_posts_pagination')) :
//     function ic_custom_posts_pagination($the_query=NULL, $paged=1){

//         global $wp_query;
//         $the_query = !empty($the_query) ? $the_query : $wp_query;

//         if ($the_query->max_num_pages > 1) {
//             $big = 999999999; // need an unlikely integer
//             $items = paginate_links(apply_filters('adimans_posts_pagination_paginate_links', array(
//                 'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
//                 'format' => '?paged=%#%',
//                 'prev_next' => TRUE,
//                 'current' => max(1, $paged),
//                 'total' => $the_query->max_num_pages,
//                 'type' => 'array',
//                 'prev_text' => ' <i class="fas fa-angle-double-left"></i> ',
//                 'next_text' => ' <i class="fas fa-angle-double-right"></i> ',
//                 'end_size' => 1,
//                 'mid_size' => 1
//             )));

//             $pagination = "<div class=\"col-sm-12 text-center\"><div class=\"ic-pagination\"><ul><li>";
//             $pagination .= join("</li><li>", (array)$items);
//             $pagination .= "</li></ul></div></div>";

//             echo apply_filters('ic_posts_pagination', $pagination, $items, $the_query);
//         }
//     }
// endif;
?>




