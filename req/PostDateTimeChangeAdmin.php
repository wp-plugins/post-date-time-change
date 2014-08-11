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

	/* ==================================================
	 * Add Css and Script
	 * @since	1.0
	 */
	function load_custom_wp_admin_style() {
		wp_enqueue_style( 'jquery-ui-tabs', POSTDATETIMECHANGE_PLUGIN_URL.'/css/jquery-ui.css' );
		wp_enqueue_style( 'jquery-datetimepicker', POSTDATETIMECHANGE_PLUGIN_URL.'/css/jquery.datetimepicker.css' );
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-tabs-in', POSTDATETIMECHANGE_PLUGIN_URL.'/js/jquery-ui-tabs-in.js' );
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

		$pluginurl = plugins_url($path='',$scheme=null);

		$pagequery = 1;
		if( !empty($_GET['p']) ) {
			$pagequery = $_GET['p'];
		}
		$readposttype = 'any';
		if( !empty($_GET['posttype']) ) {
			$readposttype = $_GET['posttype'];
		}
		if( !empty($_POST) ) { 
			$this->options_updated();
			$this->posts_updated();
			$readposttype = $_POST['posttype'];
		}

		$scriptname = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH).'?page=postdatetimechange';

		$postdatetimechange_mgsettings = get_option('postdatetimechange_mgsettings');
		$pagemax = $postdatetimechange_mgsettings['pagemax'];

		?>
		<div class="wrap">
		<h2>Post Date Time Change</h2>
	<div id="tabs">
	  <ul>
	    <li><a href="#tabs-1"><?php _e('Settings'); ?></a></li>
	<!--
		<li><a href="#tabs-2">FAQ</a></li>
	 -->
	  </ul>

	  <div id="tabs-1">
		<div class="wrap">
		<h2><?php _e('Settings'); ?></h2>
			<form method="post" action="<?php echo $scriptname; ?>">

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			<p>
			<div><?php _e('Number of titles to show to this page', 'postdatetimechange'); ?>:<input type="text" name="postdatetimechange_mgsettings_pagemax" value="<?php echo $pagemax; ?>" size="3" /></div>

			<?php
			$args = array(
				'post_type' =>  $readposttype,
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
				); 

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
			<table class="wp-list-table widefat">
			<tbody>
				<tr><td colspan="3">
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype);
				?>
				</td>
				</tr>
				<tr>
				<td align="left" valign="middle"><?php _e('Title'); ?></td>
				<td align="left" valign="middle"><div><?php _e('Type'); ?>
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
				<option value="any"<?php echo $selectedany; ?>><?php _e('Posts'); ?></option>';
				<option value="attachment"<?php echo $selectedattach; ?>><?php _e('Media'); ?></option>';
				<input type="submit" value="<?php _e('Change'); ?>">
				</select>
				</div>
				</td>
				<td align="left" valign="middle"><?php _e('Date/Time'); ?></td>
				<td align="left" valign="middle"><?php _e('Edit date and time'); ?></td>
				</tr>
			<?php

			if ($postpages) {
				foreach ( $postpages as $postpage ) {
					++$count;
					if ( $pagebegin <= $count && $count <= $pageend ) {
						$postid = $postpage->ID;
						$title = $postpage->post_title;
						$link = $postpage->guid;
						if ( $readposttype === 'attachment' ) {
							$link = get_attachment_link($postpage->ID);
						}
						$posttype = $postpage->post_type;
						$date = $postpage->post_date;
						$newdate = substr( $date , 0 , strlen($date)-3 );
					?>
						<tr>
							<td align="left" valign="middle"><a style="color: #4682b4;" title="<?php _e('View');?>" href="<?php echo $link; ?>" target="_blank"><?php echo $title; ?></a></td>
							<td align="left" valign="middle"><?php echo $posttype; ?></td>
							<td align="left" valign="middle"><?php echo $date; ?></td>
							<td align="left" valign="middle">
							<input type="text" id="datetimepicker<?php echo $postid; ?>" name="postdatetimechange_datetime[<?php echo $postid ?>]" value="<?php echo $newdate; ?>" />

							</td>
					<?php
					}
				}
			}

			?>
				<tr><td colspan="3">
				<td align="right">
				<?php $this->pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype);
				?>
				</td>
				</tr>
			</tbody>
			</table>

			<p class="submit">
			  <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
			</p>

			</form>
		</div>
	  </div>

	<!--
	  <div id="tabs-2">
		<div class="wrap">
		<h2>FAQ</h2>

		</div>
	  </div>
	-->

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
	 * return	$html
	 */
	function pagenation($page, $pagebegin, $pageend, $pagelast, $scriptname, $readposttype){

			$pageprev = $page - 1;
			$pagenext = $page + 1;
			$scriptnamefirst = add_query_arg( array('p' => '1', 'posttype' => $readposttype ),  $scriptname);
			$scriptnameprev = add_query_arg( array('p' => $pageprev, 'posttype' => $readposttype ),  $scriptname);
			$scriptnamenext = add_query_arg( array('p' => $pagenext, 'posttype' => $readposttype ),  $scriptname);
			$scriptnamelast = add_query_arg( array('p' => $pagelast, 'posttype' => $readposttype ),  $scriptname);
			?>
			<div class='tablenav-pages'>
			<span class='pagination-links'>
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
			foreach ( $postdatetimechange_datetimes as $key => $value ) {
				$postdate = $value.':00';
				$postdategmt = get_gmt_from_date($postdate);
				$up_post = array(
								'ID' => $key,
								'post_date' => $postdate,
								'post_date_gmt' => $postdategmt,
								'post_modified' => $postdate,
								'post_modified_gmt' => $postdategmt
							);
				wp_update_post( $up_post );
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

// JS
$postdatetimechange_add_js = <<<POSTDATETIMECHANGE1

<!-- BEGIN: Post Date Time Change -->
<script type="text/javascript">
jQuery(function(){
POSTDATETIMECHANGE1;

			$args = array(
				'post_type' => $readposttype,
				'numberposts' => -1,
				'orderby' => 'date',
				'order' => 'DESC'
				); 

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

jQuery('#datetimepicker
POSTDATETIMECHANGE2;
			$postdatetimechange_add_js .= $postid;
$postdatetimechange_add_js .= <<<POSTDATETIMECHANGE3
').datetimepicker({format:'Y-m-d H:i'});
POSTDATETIMECHANGE3;
					}
				}
			}

$postdatetimechange_add_js .= <<<POSTDATETIMECHANGE4

});
</script>
<!-- END: Post Date Time Change -->

POSTDATETIMECHANGE4;

		return $postdatetimechange_add_js;

	}

}

?>