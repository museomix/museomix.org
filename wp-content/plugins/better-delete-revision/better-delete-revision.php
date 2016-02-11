<?php
/*
Plugin Name: Better Delete Revision
Plugin URI: http://www.1e2.it/tag/better-delete-revision/
Description: Better Delete Revision is based on the old "Delete Revision" plugin
but it is compatible with the latest version of Wordpress (3.x) with improved
features. It not only deletes redundant revisions of posts from your Wordpress
Database, it also deletes other database content related to each revision such
meta information, tags, relationships, and more. Your current published,
scheduled, and draft posts are never touched by this plugin! This plugin can
also perform optimizations on your Wordpress database. With optimization and old
revision removal this plugin will keep your database lighter and smaller
throughout use. Removing old revisions and database optimizations is one of the
best things you can do to your Wordpress blog to keep it running as fast as it
can.
Author: Galerio & Urda
Version: 1.6.1
Author URI: http://www.1e2.it/
License: GPLv3 or later
*/

/*
	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY
	KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
	WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
	PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS
	OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR
	OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
	OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
	SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

/*
* Function to display admin menu.
*/
if ( ! function_exists( 'bdr_admin_menu' ) ) {
	function bdr_admin_menu() {
		add_options_page( __( 'Better Delete Revision', 'bdr' ), __( 'Better Delete Revision', 'bdr' ), 'manage_options', basename( __FILE__ ), 'bdr_page' );
	}
}

/*
* Function to add localization to the plugin.
*/
if ( ! function_exists ( 'bdr_init' ) ) {
	function bdr_init() {
		/* Internationalization. */
		load_plugin_textdomain( 'bdr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		if ( isset( $_GET['page'] ) && $_GET['page'] == 'better-delete-revision.php' ) {
			bdr_default_options();
		}
	}
}

/*
* Function to add script and styles to the admin panel.
*/
if ( ! function_exists( 'bdr_admin_head' ) ) {
	function bdr_admin_head() {
		wp_enqueue_style( 'bdr_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
	}
}

/*
* Function to set up options.
*/
if ( ! function_exists( 'bdr_default_options' ) ) {
	function bdr_default_options() {
		global $wpmu, $bdr_rev_no;

		if ( $wpmu == 1 ) {
			if ( ! get_site_option( 'bdr_rev_no' ) ) {
				add_site_option( 'bdr_rev_no', 0, '', 'yes' );
			}
			$bdr_rev_no = get_site_option( 'bdr_rev_no' );
		} else {
			if ( ! get_option( 'bdr_rev_no' ) ) {
				add_option( 'bdr_rev_no', 0, '', 'yes' );
			}
			$bdr_rev_no = get_option( 'bdr_rev_no' );
		}
	}
}

/*
* Function to display plugin main settings page.
*/
if ( ! function_exists( 'bdr_page' ) ) {
	function bdr_page() {
		global $wpdb, $bdr_rev_no;

		$bdr_plugin_info = get_plugin_data( __FILE__ );
		$bdr_version = $bdr_plugin_info['Version'];
		$bdr_posts = count(
			$wpdb->get_results(
				"SELECT ID
				FROM ($wpdb->posts)
				WHERE `post_type` = 'post'"
			)
		); ?>
		<div class="wrap">
			<h2><?php _e( 'Better Delete Revision Manager', 'bdr' ); ?> <font size=1><?php echo $bdr_version; ?></font></h2>
			<div class="bdr_widget">
				<p><?php _e( 'You have', 'bdr' ); ?> <span><?php echo $bdr_posts; ?></span> <?php _e( 'posts', 'bdr' ); ?>.</p>
				<p><?php _e( 'Since you started using Better Delete Revision', 'bdr' ); ?>, <span id="bdr_revs_no"><?php echo $bdr_rev_no; ?></span> <?php _e( 'redundant post revisions have been removed!', 'bdr' ); ?></p>
			</div><!-- .widget -->
			<?php if ( isset( $_POST['bdr_get_rev'] ) && check_admin_referer( plugin_basename( __FILE__ ) ) ) {
				$bdr_results = $wpdb->get_results(
					"SELECT `ID`,`post_date`,`post_title`,`post_modified`
					FROM ($wpdb->posts)
					WHERE `post_type` = 'revision'
					ORDER BY `ID` DESC"
				);
				if ( $bdr_results ) {
					$bdr_res_no = count( $bdr_results ); ?>
					<table class="widefat bdr_table">
						<thead>
							<tr>
								<th width="30"><?php _e( 'Id', 'bdr' ); ?></th>
								<th width="450"><?php _e( 'Title', 'bdr' ); ?></th>
								<th width="180"><?php _e( 'Post date', 'bdr' ); ?></th>
								<th width="180"><?php _e( 'Last modified', 'bdr' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php for ( $i = 0 ; $i < $bdr_res_no ; $i++ ) { ?>
								<tr>
									<td><?php echo $bdr_results[ $i ]->ID; ?></td>
									<td><?php echo $bdr_results[ $i ]->post_title; ?></td>
									<td><?php echo $bdr_results[ $i ]->post_date; ?></td>
									<td><?php echo $bdr_results[ $i ]->post_modified; ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
					<p><?php _e( 'Would you like to remove the revision posts?', 'bdr' )?></p>
					<form class="bdr_form" method="post" action="options-general.php?page=better-delete-revision.php">
						<?php wp_nonce_field( plugin_basename( __FILE__ ) ); ?>
						<input type="hidden" name="bdr_rev_no" value="<?php echo $bdr_res_no; ?>" />
						<input class="button-primary" type="submit" name="bdr_del_act" value="<?php printf( __( 'Yes , I would like to delete them! (A Total Of %s)' , 'bdr' ), $bdr_res_no ); ?>" />
						<input class="button" type="submit" name="goback" value="<?php _e( 'No , I prefer to keep them!', 'bdr' ); ?>" />
					</form>
				<?php } else { ?>
					<div class="updated bdr_no_rev">
						<p>
							<?php _e( 'Great! You have no revisions now!', 'bdr' ); ?>
						</p>
					</div>
				<?php }
			} elseif ( isset( $_POST['bdr_del_act'] ) && check_admin_referer( plugin_basename( __FILE__ ) ) ) {
				$bdr_ngg_fix = bdr_get_ngg_fix();
				$bdr_revisions = $wpdb->get_results(
					"SELECT `ID` AS revision_id
					FROM ($wpdb->posts)
					WHERE `post_type` = 'revision'
					ORDER BY `ID` DESC"
				);
				if ( is_array( $bdr_ngg_fix ) ) {
					remove_action( $bdr_ngg_fix['tag'], array( $bdr_ngg_fix['class'], $bdr_ngg_fix['method'] ), $bdr_ngg_fix['priority'] );
				}
				foreach ( $bdr_revisions as $bdr_revision ) {
					wp_delete_post_revision( $bdr_revision->revision_id );
				}
				if ( is_array( $bdr_ngg_fix ) ) {
					add_action( $bdr_ngg_fix['tag'], array( $bdr_ngg_fix['class'], $bdr_ngg_fix['method'] ), $bdr_ngg_fix['priority'] );
				}
				$bdr_del_no = $_POST['bdr_rev_no'];
				$bdr_rev_new = $bdr_rev_no + $bdr_del_no;
				update_option( 'bdr_rev_no', $bdr_rev_new ); ?>
				<div class="updated bdr_updated">
					<p>
						<strong><?php printf( __( 'Deleted %s revisions!', 'bdr' ), sprintf( '<span>%s</span>', $bdr_del_no ) ); ?></strong>
					</p>
				</div>
				<script type="text/javascript">
					document.getElementById( 'bdr_revs_no' ).innerHTML = <?php echo $bdr_rev_new; ?>;
				</script>
			<?php } elseif ( isset( $_POST['bdr_maintain_mysql'] ) && check_admin_referer( plugin_basename( __FILE__ ) ) ) {
				if ( isset( $_POST['bdr_operation'] ) && $_POST['bdr_operation'] == 'OPTIMIZE' ) {
					$bdr_operation = 'OPTIMIZE';
				} else {
					$bdr_operation = 'CHECK';
				}

				$bdr_tables = $wpdb->get_results( 'SHOW TABLES IN ' . DB_NAME );
				$bdr_query = "$bdr_operation TABLE";
				$bdr_tables_in_db_name = 'Tables_in_' . DB_NAME;

				foreach ( $bdr_tables as $k => $v ) {
					$bdr_table = $v->$bdr_tables_in_db_name;
					$bdr_query .= " `$bdr_table`,";
				}
				$bdr_query = substr( $bdr_query, 0, strlen( $bdr_query ) - 1 );
				$bdr_result = $wpdb->get_results( $bdr_query );

				switch ( $bdr_operation ) {
					case 'OPTIMIZE': ?>
						<h3><?php _e( 'Optimization of database completed!', 'bdr' ); ?></h3>
						<?php break;
					case 'CHECK':
					default: ?>
						<table border="0" class="widefat bdr_table">
							<thead>
								<tr>
									<th><?php _e( 'Table', 'bdr' ); ?></th>
									<th><?php _e( 'OP', 'bdr' ); ?></th>
									<th><?php _e( 'Status', 'bdr' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ( $bdr_result as $j => $o ) { ?>
									<tr>
										<?php foreach ( $o as $k => $v ) {
											$bdr_tr_class = $j%2 == 1 ? 'active alt' : 'inactive';
											if ( $k == 'Msg_type' ) {
												continue;
											}
											if ( $k == 'Msg_text' ) {
												if ( $v == 'OK' ) { ?>
													<td class="<?php echo $bdr_tr_class; ?>">
														<font color="green">
															<b><?php echo $v; ?></b>
														</font>
													</td>
												<?php } else { ?>
													<td class="<?php echo $bdr_tr_class; ?>">
														<font color="red">
															<b><?php echo $v; ?></b>
														</font>
													</td>
												<?php }
											} else { ?>
												<td class="<?php echo $bdr_tr_class; ?>">
													<?php echo $v; ?>
												</td>
											<?php }
										} ?>
									</tr>
								<?php } ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="3">
										<?php printf(
											__( 'If all statuses are %s, then your database does not need any optimization! If any are %s, then click on the following button to optimize your Wordpress database.', 'bdr' ),
											sprintf( '<font color="green">%s</font>', __( 'OK', 'bdr' ) ),
											sprintf( '<font color="red">%s</font>', __( 'red', 'bdr' ) )
										); ?>
									</th>
								</tr>
							</tfoot>
						</table>
						<form class="bdr_form" method="post" action="options-general.php?page=better-delete-revision.php">
							<?php wp_nonce_field( plugin_basename( __FILE__ ) ); ?>
							<input name="bdr_operation" type="hidden" value="OPTIMIZE" />
							<input name="bdr_maintain_mysql" type="hidden" value="OPTIMIZE" />
							<input name="submit" type="submit" class="button-primary" value="<?php _e( 'Optimize Wordpress Database', 'bdr' ); ?>" />
						</form>
						<?php break;
				}
			} else { ?>
				<form class="bdr_form" method="post" action="options-general.php?page=better-delete-revision.php">
					<?php wp_nonce_field( plugin_basename( __FILE__ ) ); ?>
					<input class="button" type="submit" name="bdr_get_rev" value="<?php _e( 'Check Revision Posts', 'bdr' ); ?>" />
					<input class="button" type="submit" name="bdr_maintain_mysql" value="<?php _e( 'Optimize Your Database', 'bdr' ); ?>" />
				</form>
			<?php } ?>
			<div class="bdr_widget">
				<p>
					<?php _e( 'Post Revisions are a feature introduced in Wordpress 2.6. Whenever you or Wordpress saves a post or a page, a revision is automatically created and stored in your Wordpress database. Each additional revision will slowly increase the size of your database. If you save a post or page multiple times, your number of revisions will greatly increase overtime. For example, if you have 100 posts and each post has 10 revisions you could be storing up to 1,000 copies of older data!', 'bdr' ); ?>
				</p>
				<br />
				<p>
					<?php _e( 'The Better Delete Revision plugin is your #1 choice to quickly and easily removing revision from your Wordpress database. Try it out today to see what a lighter and smaller Wordpress database can do for you!', 'bdr' ); ?>
				</p>
				<br />
				<p>
					<?php _e( 'Thank you for using this plugin! I hope you enjoy it!', 'bdr' ); ?>
				</p>
				<br />
				<p><?php _e( 'Author:', 'bdr' ); ?> <a href="http://www.1e2.it" target="_blank">http://www.1e2.it</a></p>
			</div>
		</div><!-- .wrap -->
	<?php }
}

/*
* Fix for plugin NextGEN Gallery.
*/
if ( ! function_exists( 'bdr_get_ngg_fix' ) ) {
	function bdr_get_ngg_fix() {
		global $wp_filter;
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$bdr_is_ngg_active = is_plugin_active( 'nextgen-gallery/nggallery.php' );
		$bdr_class = 'M_Attach_To_Post';
		if ( $bdr_is_ngg_active && class_exists( $bdr_class ) ) {
			$bdr_tag = 'after_delete_post';
			$bdr_method = 'cleanup_displayed_galleries';
			$bdr_filters = $wp_filter[ $bdr_tag ];
			if ( ! empty( $bdr_filters ) ) {
				foreach ( $bdr_filters as $bdr_priority => $bdr_filter ) {
					foreach ( $bdr_filter as $bdr_identifier => $bdr_function ) {
						if ( is_array( $bdr_function) AND is_a( $bdr_function['function'][0], $bdr_class ) AND $bdr_method === $bdr_function['function'][1] ) {
							return array(
								'tag'      => $bdr_tag,
								'class'    => $bdr_function['function'][0],
								'method'   => $bdr_method,
								'priority' => $bdr_priority
							);
						}
					}
				}
			}
		}
		return false;
	}
}

/*
* Adds Settings link to the plugins page
*/
if ( ! function_exists( 'bdr_plugin_action_links' ) ) {
	function bdr_plugin_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $this_plugin;
		if ( ! $this_plugin )
			$this_plugin = plugin_basename( __FILE__ );
		if ( $file == $this_plugin ) {
			$settings_link = '<a href="options-general.php?page=better-delete-revision.php">' . __( 'Settings', 'bdr' ) . '</a>';
			array_unshift( $links, $settings_link );
		}
		return $links;
	}
}

/*
* Adds additional links to the plugins page
*/
if ( ! function_exists( 'bdr_links' ) ) {
	function bdr_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		static $base;
		if ( ! $base )
			$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			$links[] = '<a href="options-general.php?page=better-delete-revision.php">' . __( 'Settings','bdr' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/better-delete-revision/faq" target="_blank">' . __( 'FAQ','bdr' ) . '</a>';
		}
		return $links;
	}
}

/*
* Function to uninstall plugin.
*/
if ( ! function_exists( 'bdr_uninstall' ) ) {
	function bdr_uninstall() {
		delete_option( 'bdr_rev_no' );
		delete_site_option( 'bdr_rev_no' );
	}
}

/* Displaying admin menu */
add_action( 'admin_menu', 'bdr_admin_menu' );
/* Initialization */
add_action( 'init', 'bdr_init' );
/* Adding styles in the admin panel */
add_action( 'admin_enqueue_scripts', 'bdr_admin_head' );
/* Adds additional links to the plugins page */
add_filter( 'plugin_action_links', 'bdr_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'bdr_links', 10, 2 );
/* Uninstall plugin */
register_uninstall_hook( __FILE__, 'bdr_uninstall' );