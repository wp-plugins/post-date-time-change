<?php
/**
 * Post Date Time Change
 * 
 * @package    Post Date Time Change
 * @subpackage Post Date Time Change Main & Management screen
/*  Copyright (c) 2014- Katsushi Kawamori (email : dodesyoswift312@gmail.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

class PostDateTimeChangeAdmin {

	/* ==================================================
	 * Add a "Settings" link to the plugins page
	 * @since	1.0
	 */
	function settings_link( $links, $file ) {
		static $this_plugin;
		if ( empty($this_plugin) ) {
			$this_plugin = POSTDATETIMECHANGE_PLUGIN_BASE_FILE;
		}
		if ( $file == $this_plugin ) {
			$links[] = '<a href="'.admin_url('tools.php?page=postdatetimechange').'">'.__( 'Settings').'</a>';
		}
			return $links;
	}

	/* ==================================================
	 * Settings page
	 * @since	1.0
	 */
	function add_pages() {
		add_management_page('Post Date Time Change', 'Post Date Time Change', 'manage_options', 'postdatetimechange', array($this, 'manage_page'));
	}

	/* =================================================
	 * Add Css and Script
	 * @since	1.0
	 */
	function load_custom_wp_admin_style() {
		wp_enqueue_style( 'jquery-responsiveTabs', POSTDATETIMECHANGE_PLUGIN_URL.'/css/responsive-tabs.css' );
		wp_enqueue_style( 'jquery-responsiveTabs-style', POSTDATETIMECHANGE_PLUGIN_URL.'/css/style.css' );
		wp_enqueue_style( 'jquery-datetimepicker', POSTDATETIMECHANGE_PLUGIN_URL.'/css/jquery.datetimepicker.css' );
		wp_enqueue_style( 'post-date-time-change',  POSTDATETIMECHANGE_PLUGIN_URL.'/css/post-date-time-change.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-responsiveTabs', POSTDATETIMECHANGE_PLUGIN_URL.'/js/jquery.responsiveTabs.min.js' );
		wp_enqueue_script( 'jquery-datetimepicker', POSTDATETIMECHANGE_PLUGIN_URL.'/js/jquery.datetimepicker.js', null, '2.3.4' );
	}

	/* ==================================================
	 * Add Script on footer
	 * @since	1.0
	 */
	function load_custom_wp_admin_style2() {
		echo $this->add_js();
	}

	/* ==================================================
	 * Main
	 */
	function manage_page() {

		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$pagequery = 1;
		if( !empty($_GET['p']) ) {
			$pagequery = $_GET['p'];
		}
		$readposttype = 'any';
		if( !empty($_GET['posttype']) ) {
			$readposttype = $_GET['posttype'];
		}
		$catfilter = 0;
		$mimefilter = NULL;
		if( !empty($_GET['cat']) ) {
			$catfilter = $_GET['cat'];
		}
		if( !empty($_GET['mime']) ) {
			$mimefilter = $_GET['mime'];
		}
		if( !empty($_POST) ) {
			if ( !empty($_POST['ShowToPage']) ) {
				$this->options_updated();
				echo '<div class="updated"><ul><li>'.__('Settings saved.').'</li></ul></div>';
			}
			if ( !empty($_POST['UpdateDateTimeChange']) ) {
				$this->posts_updated();
				echo '<div class="updated"><ul><li>'.__('Changed the date and time.', 'postdatetimechange').'</li></ul></div>';
			}
			if( !empty($_POST['posttype']) ) {
				$readposttype = $_POST['posttype'];
			}
			if( !empty($_POST['cat']) ) {
				$catfilter = $_POST['cat'];
			}
			if( !empty($_POST['mime']) ) {
				$mimefilter = $_POST['mime'];
			}
		}

		$scriptname = admin_url('tools.php?page=postdatetimechange');

		$postdatetimechange_mgsettings = get_option('postdatetimechange_mgsettings');
		$pagemax = $postdatetimechange_mgsettings['pagemax'];

		?>
		<div class="wrap">
		<h2>Post Date Time Change</h2>
			<div id="postdatetimechange-tabs">
				<ul>
				<li><a href="#postdatetimechange-tabs-1"><?php _e('Edit date and time'); ?></a></li>
				<li><a href="#postdatetimechange-tabs-2"><?php _e('Donate to this plugin &#187;'); ?></a></li>
				</ul>
				<div id="postdatetimechange-tabs-1">

			<h3><?php _e('Edit date and time'); ?></h3>

			<?php
			echo '<div id="postdatetimechange-loading"><img src="'.POSTDATETIMECHANGE_PLUGIN_URL.'/css/loading.gif"></div>';
			echo '<div id="postdatetimechange-loading-container">';
			?>

			<form method="post" action="<?php echo $scriptname; ?>">

			<div class="submit">
			 	<input type="submit" name="UpdateDateTimeChange" class="button-primary button-large" value="<?php _e('Change the date and time', 'postdatetimechange') ?>" />
			</div>

			<p>
			<div style="float:left;"><?php _e('Number of titles to show to this page', 'postdatetimechange'); ?>:<input type="text" name="postdatetimechange_mgsettings_pagemax" value="<?php echo $pagemax; ?>" size="3" /></div>
			<input type="submit" name="ShowToPage" value="<?php _e('Save') ?>" />
			<div style="clear:both"></div>

			<div>
			<select name="posttype">
			<?php
			$selectedany = NULL;
			$selectedattach = NULL;
			if ( $readposttype === 'any' ) {
				$selectedany = ' selected';
			} else if ( $readposttype === 'attachment' ) {
				$selectedattach = ' selected';
			}
			?>
			<option value="any"<?php echo $selectedany; ?>><?php _e('Posts'); ?></option>
			<option value="attachment"<?php echo $selectedattach; ?>><?php _e('Media'); ?></option>
			</select>
			<input type="submit" value="<?php _e('Apply'); ?>">
			<span style="margin-right: 1em;"></span>
			<?php
			if ( $readposttype === 'attachment' ) {
				$args = array(
					'post_type' => 'attachment',
					'post_mime_type' => $mimefilter,
					'numberposts' => -1,
					'orderby' => 'date',
					'order' => 'DESC'
					); 
				?>
				<select name="mime" style="width: 180px;">
				<option value=""><?php echo esc_attr( __( 'All Mime types', 'postdatetimechange' ) ); ?></option> 
				<?php
				foreach ( wp_get_mime_types() as $exts => $mime ) {
					?>
					<option value="<?php echo esc_attr($mime); ?>"<?php if ($mimefilter === $mime) echo 'selected';?>><?php echo esc_attr($mime); ?></option>
					<?php
				}
				?>
				</select>
				<input type="submit" value="<?php _e('Filter'); ?>">
				<?php
			} else {
				$args = array(
					'category' => $catfilter,
					'post_type' => $readposttype,
					'numberposts' => -1,
					'orderby' => 'date',
					'order' => 'DESC'
					); 
				$catargs = array(
					'show_option_all' => __('All categories'),
					'hierarchical' => 1,
					'selected' => $catfilter
				);
				wp_dropdown_categories($catargs);
				?>
				<input type="submit" value="<?php _e('Filter'); ?>">
				<?php
			}
			?>
			</div>
			<?php
			$postpages = get_posts($args);

			$pageallcount = 0;
			// pagenation
			foreach ( $postpages as $postpage ) {
				++$pageallcount;
			}
			if (!empty($_GET['p'])){
				$page = $_GET['p'];
			} else {
				$page = 1;
			}
			$count = 0;
			$pagebegin = (($page - 1) * $pagemax) + 1;
			$pageend = $page * $pagemax;
			$pagelast = ceil($pageallcount / $pagemax);

			?>
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype, $catfilter, $mimefilter);
				?>
				<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
					<div>
					<?php
					if ( $readposttype === 'attachment' ) {
						_e('Thumbnail'); ?> -
					<?php
					}
					_e('Title'); ?> -
					<?php
					if ( $readposttype === 'any' ) {
						_e('Type'); ?> -
					<?php
					} else if ( $readposttype === 'attachment' ) {
						_e('Mime type', 'postdatetimechange'); ?> -
					<?php
					}
					_e('Date/Time'); ?> -
					<?php _e('Edit date and time'); ?>
					</div>
				</div>
			<?php

			if ($postpages) {
				foreach ( $postpages as $postpage ) {
					++$count;
					if ( $pagebegin <= $count && $count <= $pageend ) {
						$postid = $postpage->ID;
						$title = $postpage->post_title;
						$link = $postpage->guid;
						$thumb_html = NULL;
						if ( $readposttype === 'attachment' ) {
							$link = get_attachment_link($postpage->ID);
							$image_attr_thumbnail = wp_get_attachment_image_src($postpage->ID, 'thumbnail', true);
							$thumb_html = '<img width="40" height="40" src="'.$image_attr_thumbnail[0].'" align="middle">';
							$posttype = $postpage->post_mime_type;
						}
						if ( $readposttype === 'any' ) {
							$posttype = $postpage->post_type;
						}
						$date = $postpage->post_date;
						$newdate = substr( $date , 0 , strlen($date)-3 );
					?>
					<div style="border-bottom: 1px solid; padding-top: 5px; padding-bottom: 5px;">
						<div style="float: left;">
						<?php echo $thumb_html; ?>
						<a style="color: #4682b4;" title="<?php _e('View');?>" href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a>
						<span style="margin-right: 1em;"></span>
						<?php echo $posttype; ?>
						<span style="margin-right: 1em;"></span>
						<?php echo $date; ?>
						<input type="text" id="datetimepicker-postdatetimechange<?php echo $postid; ?>" name="postdatetimechange_datetime[<?php echo $postid ?>]" value="<?php echo $newdate; ?>" />
						</div>
						<div style="clear:both"></div>
					</div>
					<?php
					}
				}
			}

			?>
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype, $catfilter, $mimefilter);
				?>

			<div class="submit">
				<input type="submit" name="UpdateDateTimeChange" class="button-primary button-large" value="<?php _e('Change the date and time', 'postdatetimechange') ?>" />
			</div>

			</form>

			</div>

		</div>

		<div id="postdatetimechange-tabs-2">
		<div class="wrap">
		<div style="padding:10px;border:#CCC 2px solid; margin:0 0 20px 0">
			<h3><?php _e('Please make a donation if you like my work or would like to further the development of this plugin.', 'postdatetimechange'); ?></h3>
			<div align="right">Katsushi Kawamori</div>
			<h3 style="float: left;"><?php _e('Donate to this plugin &#187;'); ?></h3>
<a href='https://pledgie.com/campaigns/28307' target="_blank"><img alt='Click here to lend your support to: Various Plugins for WordPress and make a donation at pledgie.com !' src='https://pledgie.com/campaigns/28307.png?skin_name=chrome' border='0' ></a>
		</div>
		</div>

		</div>
		</div>
		<?php
	}

	/* ==================================================
	 * Pagenation
	 * @since	1.0
	 * string	$page
	 * string	$pagebegin
	 * string	$pageend
	 * string	$pagelast
	 * string	$scriptname
	 * string	$readposttype
	 * string	$catfilter
	 * string	$mimefilter
	 * return	$html
	 */
	function pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype, $catfilter, $mimefilter){

			$pageprev = $page - 1;
			$pagenext = $page + 1;
			$scriptnamefirst = add_query_arg( array('p' => '1', 'posttype' => $readposttype, 'cat' => $catfilter, 'mime' => $mimefilter ),  $scriptname);
			$scriptnameprev = add_query_arg( array('p' => $pageprev, 'posttype' => $readposttype, 'cat' => $catfilter, 'mime' => $mimefilter ),  $scriptname);
			$scriptnamenext = add_query_arg( array('p' => $pagenext, 'posttype' => $readposttype, 'cat' => $catfilter, 'mime' => $mimefilter ),  $scriptname);
			$scriptnamelast = add_query_arg( array('p' => $pagelast, 'posttype' => $readposttype, 'cat' => $catfilter, 'mime' => $mimefilter ),  $scriptname);
			?>
			<div class="post-date-time-change-pages">
			<span class="post-date-time-change-links">
			<?php
			if ( $page <> 1 ){
				?><a title='<?php _e('Go to the first page'); ?>' href='<?php echo $scriptnamefirst; ?>'>&laquo;</a>
				<a title='<?php _e('Go to the previous page'); ?>' href='<?php echo $scriptnameprev; ?>'>&lsaquo;</a>
			<?php
			}
			echo $page; ?> / <?php echo $pagelast;
			?>
			<?php
			if ( $page <> $pagelast ){
				?><a title='<?php _e('Go to the next page'); ?>' href='<?php echo $scriptnamenext; ?>'>&rsaquo;</a>
				<a title='<?php _e('Go to the last page'); ?>' href='<?php echo $scriptnamelast; ?>'>&raquo;</a>
			<?php
			}
			?>
			</span>
			</div>
			<?php

	}

	/* ==================================================
	 * Update wp_options table.
	 * @since	1.0
	 */
	function options_updated(){

		$mgsettings_tbl = array(
						'pagemax' => intval($_POST['postdatetimechange_mgsettings_pagemax'])
						);
		update_option( 'postdatetimechange_mgsettings', $mgsettings_tbl );

	}

	/* ==================================================
	 * Update wp_post table.
	 * @since	1.0
	 */
	function posts_updated(){

		if(isset($_POST['postdatetimechange_datetime'])){ $postdatetimechange_datetimes = $_POST['postdatetimechange_datetime']; }

		if ( !empty($postdatetimechange_datetimes) ) {

			$upload_dir = wp_upload_dir();

			foreach ( $postdatetimechange_datetimes as $key => $value ) {
				$postdate = $value.':00';
				$postdategmt = get_gmt_from_date($postdate);

				$post_datas = get_post ( $key );
				$new_subdir = FALSE;
				if ( $post_datas->post_type === 'attachment' && get_option( 'uploads_use_yearmonth_folders' ) ) {
					$y = substr( $postdategmt, 0, 4 );
					$m = substr( $postdategmt, 5, 2 );
					$subdir = "/$y/$m";

					$post_data_date_gmt = $post_datas->post_date_gmt;
					$y_post = substr( $post_data_date_gmt, 0, 4 );
					$m_post = substr( $post_data_date_gmt, 5, 2 );
					$subdir_post = "/$y_post/$m_post";

					if ( $subdir <> $subdir_post ) { $new_subdir = TRUE; }
				}

				if ( $new_subdir ) {
					$new_file = str_replace($upload_dir['baseurl'].$subdir_post, '', $post_datas->guid);
					$new_file = $subdir.$new_file;
					$newurl = $upload_dir['baseurl'].$new_file;
					$new_file = substr( $new_file, 1 );
					update_attached_file( $key, $new_file );

					$filename = wp_basename($new_file);
					$suffix_new_files = explode('.', $new_file);
					$ext = end($suffix_new_files);

					$upload_new_path = $upload_dir['basedir'].$subdir;
					$upload_old_path = $upload_dir['basedir'].$subdir_post;
					if ( !file_exists($upload_new_path) ) {
						mkdir($upload_new_path, 0757, true);
					}
					copy( $upload_old_path.'/'.$filename, $upload_new_path.'/'.$filename );

					if ( wp_ext2type($ext) === 'image' ){
						$metaolddata = wp_get_attachment_metadata( $key );
						foreach ( $metaolddata as $key1 => $key2 ){
							if ( $key1 === 'sizes' ) {
								foreach ( $metaolddata[$key1] as $key2 => $key3 ){
									$oldthumbs[] = $upload_old_path.'/'.$metaolddata['sizes'][$key2]['file'];
								}
							}
						}
						foreach ( $oldthumbs as $oldthumb ) {
							unlink( $oldthumb );
						}
					    unlink( $upload_old_path.'/'.$filename );
						$metadata = wp_generate_attachment_metadata( $key, $upload_new_path.'/'.$filename );
						wp_update_attachment_metadata( $key, $metadata );
					}else if ( wp_ext2type($ext) === 'video' ){
						$metadata = wp_read_video_metadata( $upload_new_path.'/'.$filename );
						wp_update_attachment_metadata( $key, $metadata );
					    unlink( $upload_old_path.'/'.$filename );
					}else if ( wp_ext2type($ext) === 'audio' ){
						$metadata = wp_read_audio_metadata( $upload_new_path.'/'.$filename );
						wp_update_attachment_metadata( $key, $metadata );
					    unlink( $upload_old_path.'/'.$filename );
					} else {
						$metadata = NULL;
					    unlink( $upload_old_path.'/'.$filename );
					}

					$up_post = array(
									'ID' => $key,
									'post_date' => $postdate,
									'post_date_gmt' => $postdategmt,
									'post_modified' => $postdate,
									'post_modified_gmt' => $postdategmt,
									'guid' => $newurl
								);
					wp_update_post( $up_post );
				} else {
					if ( $post_datas->post_date <> $postdate ) {
						$up_post = array(
										'ID' => $key,
										'post_date' => $postdate,
										'post_date_gmt' => $postdategmt,
										'post_modified' => $postdate,
										'post_modified_gmt' => $postdategmt
									);
						wp_update_post( $up_post );
						if ( $post_datas->post_type <> 'attachment' ) {
							$rev_post = array(
											'post_author' => $post_datas->post_author,
											'post_date' => $postdate,
											'post_date_gmt' => $postdategmt,
											'post_content' => $post_datas->post_content,
											'post_title' => $post_datas->post_title,
											'post_excerpt' => $post_datas->post_excerpt,
											'post_status' => 'inherit',
											'post_name' => $key.'-revision-from-'.$post_datas->post_date.'-to-'.$postdate,
											'post_modified' => $postdate,
											'post_modified_gmt' => $postdategmt,
											'post_parent' => $key,
											'post_type' => 'revision'
										);
							wp_insert_post( $rev_post );
						}
					}
				}
			}
		}

	}

	/* ==================================================
	 * Add js
	 * @since	1.0
	 */
	function add_js(){

		$readposttype = 'any';
		if( !empty($_GET['posttype']) ) {
			$readposttype = $_GET['posttype'];
		}
		if( !empty($_POST['posttype']) ) {
			$readposttype = $_POST['posttype'];
		}
		$catfilter = 0;
		$mimefilter = NULL;
		if( !empty($_GET['cat']) ) {
			$catfilter = $_GET['cat'];
		}
		if( !empty($_POST['cat']) ) {
			$catfilter = $_POST['cat'];
		}
		if( !empty($_GET['mime']) ) {
			$mimefilter = $_GET['mime'];
		}
		if( !empty($_POST['mime']) ) {
			$mimefilter = $_POST['mime'];
		}

// JS
$postdatetimechange_add_js = <<<POSTDATETIMECHANGE1

<!-- BEGIN: Post Date Time Change -->
<script type="text/javascript">
jQuery('#postdatetimechange-tabs').responsiveTabs({
  startCollapsed: 'accordion'
});
</script>
<script type="text/javascript">
POSTDATETIMECHANGE1;

		if ( $readposttype === 'attachment' ) {
			$args = array(
				'post_type' => 'attachment',
				'post_mime_type' => $mimefilter,
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
				); 
		} else {
			$args = array(
				'category' => $catfilter,
				'post_type' => $readposttype,
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
				); 
		}

		$postpages = get_posts($args);

		$postdatetimechange_mgsettings = get_option('postdatetimechange_mgsettings');
		$pagemax = $postdatetimechange_mgsettings['pagemax'];

		if (!empty($_GET['p'])){
			$page = $_GET['p'];
		} else {
			$page = 1;
		}
		$count = 0;
		$pagebegin = (($page - 1) * $pagemax) + 1;
		$pageend = $page * $pagemax;

		if ($postpages) {
			foreach ( $postpages as $postpage ) {
				++$count;
				if ( $pagebegin <= $count && $count <= $pageend ) {
					$postid = $postpage->ID;
$postdatetimechange_add_js .= <<<POSTDATETIMECHANGE2

jQuery('#datetimepicker-postdatetimechange
POSTDATETIMECHANGE2;
					$postdatetimechange_add_js .= $postid;
$postdatetimechange_add_js .= <<<POSTDATETIMECHANGE3
').datetimepicker({format:'Y-m-d H:i'});
POSTDATETIMECHANGE3;
				}
			}
		}

$postdatetimechange_add_js .= <<<POSTDATETIMECHANGE4

</script>
<script type="text/javascript">
window.addEventListener( "load", function(){
  jQuery("#postdatetimechange-loading").delay(2000).fadeOut();
  jQuery("#postdatetimechange-loading-container").delay(2000).fadeIn();
}, false );
</script>
<!-- END: Post Date Time Change -->

POSTDATETIMECHANGE4;

		return $postdatetimechange_add_js;

	}

}

?>