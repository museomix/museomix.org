<?php

if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

class wp_native_dashboard_langswitcher {
	function wp_native_dashboard_langswitcher($plugin_url, $as_head, $as_admin_bar) {
		global $text_direction;
		if ($text_direction == 'rtl') 
			wp_enqueue_style('wp-native-dashboard-css-rtl', $plugin_url.'/css/style-rtl.css');
		else
			wp_enqueue_style('wp-native-dashboard-css', $plugin_url.'/css/style.css');	
		add_action('admin_head', array(&$this, 'on_admin_head'));
		add_action('wp_ajax_wp_native_dashboard_change_language', array(&$this, 'on_ajax_wp_native_dashboard_change_language'));
		add_action('wp_ajax_wp_native_dashboard_refresh_switcher', array(&$this, 'on_ajax_wp_native_dashboard_refresh_switcher'));
		
		$this->as_head = $as_head;
		$this->as_admin_bar = $as_admin_bar;
		
		global $wp_admin_bar;
		if(function_exists('is_admin_bar_showing') && is_admin_bar_showing() && $this->as_admin_bar) {
			$langs = wp_native_dashboard_collect_installed_languages();
			$loc = get_locale();
			
			$wp_admin_bar->add_menu( array( 'id' => 'wpnd-lang-cur', 'title' => '<span class="csp-'.$loc.'">'.wp_native_dashboard_get_name_of($loc).'</span>', 'href' => '#', 'meta' => array ( 'class' => 'csp-langoption' ) ) );
			if (count($langs) > 1) {
				foreach($langs as $lang) {
					if ($lang != $loc) {
						$wp_admin_bar->add_menu( array( 'parent' => 'wpnd-lang-cur', 'id' => 'wpnd-lang-'.$lang, 'title' => '<span class="csp-'.$lang.'" hreflang="'.$lang.'">'.wp_native_dashboard_get_name_of($lang).'</span>', 'href' => '#', 'meta' => array ( 'class' => 'csp-langoption csp-langoption-adminbar' ) ) );
					}
				}
			}
		}
		if (function_exists("admin_url")) {
			$this->admin_url = rtrim(admin_url(), '/');
		}else{
			$this->admin_url = rtrim(get_option('siteurl').'/wp-admin/', '/');
		}

		add_action( 'bp_adminbar_menus', array(&$this, 'bp_adminbar_switcher_menu') , 1 );
		
		global $wp_version;
		$this->no_dashboard_headline = version_compare($wp_version, '3.0', '>=');
	}
		
	function bp_adminbar_switcher_menu() {
			$langs = wp_native_dashboard_collect_installed_languages();
			$loc = get_locale();
		?>
		<li id="wp-admin-bar-wpnd-lang-cur" class="csp-langoption"><a href="#"><span><span class="csp-<?php echo $loc; ?>"><?php echo wp_native_dashboard_get_name_of($loc); ?></span></span></a>
		<?php 			
			if (count($langs) > 1) {
				echo '<ul>';
				foreach($langs as $lang) {
					if ($lang == $loc) continue;
				?>
				<li id="wp-admin-bar-wpnd-lang-<?php echo $lang; ?>" class="csp-langoption csp-langoption-adminbar"><a href="#"><span class="csp-<?php echo $lang; ?>" hreflang="<?php echo $lang; ?>"><?php echo wp_native_dashboard_get_name_of($lang); ?></span></a></li>
			<?php 
				} 
				echo '</ul>';
			} ?>
		</li>
		<?php
	}
	
	function on_print_admin_bar_switcher() {
			$langs = wp_native_dashboard_collect_installed_languages();
			$loc = get_locale();
		?>
		<li class="menupop csp-langoption" id="wp-admin-bar-wpnd-lang-cur"><a href="#" aria-haspopup="true" tabindex="10" class="ab-item"><span class="csp-<?php echo $loc; ?>"><?php echo wp_native_dashboard_get_name_of($loc); ?></span></a><div class="ab-sub-wrapper"><ul class="ab-submenu" id="wp-admin-bar-wpnd-lang-cur-default">
		<?php foreach($langs as $lang) {
					if ($lang == $loc) continue;
		?>
			<li class=" csp-langoption csp-langoption-adminbar" id="wp-admin-bar-wpnd-lang-<?php echo $lang; ?>"><a href="#" tabindex="10" class="ab-item"><span hreflang="<?php echo $lang; ?>" class="csp-<?php echo $lang; ?>"><?php echo wp_native_dashboard_get_name_of($lang); ?></span></a></li>
		<?php }  ?>
		</ul></div></li>
		<?php
	}
	
	function on_print_dashboard_switcher() {
		$langs = wp_native_dashboard_collect_installed_languages();
		$loc = get_locale();
		echo '<div id="csp-langswitcher-actions" class="alignleft">';
		echo '<div id="csp-langswitcher-current"><span class="csp-'.$loc.'">'.wp_native_dashboard_get_name_of($loc).'</span></div>';
		echo '<div id="csp-langswitcher-toggle"><br/></div>';
		if (count($langs) > 1) {
			echo '<div id="csp-langoptions" style="display: none;">';
			foreach($langs as $lang) {
				if ($lang != $loc) {
					echo '<a href="javascript:void(0);" class="csp-langoption" hreflang="'.$lang.'"><span class="csp-'.$lang.'">'.wp_native_dashboard_get_name_of($lang).'</span></a>';
				}
			}
			echo '</div>';
		}
		echo '</div>';		
	}
	
	function on_admin_head() {
		?>
		<script  type="text/javascript">
		//<![CDATA[
		function csl_extend_dashboard_header(html) {
			<?php if($this->as_head) : ?>
			if (html) {				
				<?php if ($this->no_dashboard_headline): ?>
				jQuery('#wp-admin-bar-wpnd-lang-cur').replaceWith(html);
				jQuery('#wp-admin-bar-wpnd-lang-cur').hoverIntent({
					over: function(e){
						jQuery(this).addClass('hover');
					},
					out: function(e){
						jQuery(this).removeClass('hover');
					},
					timeout: 180,
					sensitivity: 7,
					interval: 100
				});
				<?php else : ?>
				jQuery("#csp-langswitcher-actions").remove();
				jQuery("h1:first").before(html);
				<?php endif; ?>
			}
			<?php if (!$this->no_dashboard_headline): ?>
			else{ 
				jQuery("h1:first").before('<?php $this->on_print_dashboard_switcher(); ?>');
			}
			<?php endif; ?>
			jQuery("#csp-langswitcher").click(function() {
				jQuery(this).blur();
				jQuery("#csp-langoptions").toggle();
			});
			<?php endif; ?>
			jQuery("a.csp-langoption, li.csp-langoption-adminbar a span").click(function(event) {
				event.preventDefault();
				jQuery(this).blur();
				jQuery("#csp-langoptions").hide();
				jQuery.post("<?php echo $this->admin_url; ?>/admin-ajax.php", { action: 'wp_native_dashboard_change_language', locale: jQuery(this).attr('hreflang') },
					function(data) {
						window.location.reload();
					}
				)
			});
			jQuery('#csp-langswitcher-toggle, #csp-langoptions').bind( 'mouseenter', function(){jQuery('#csp-langoptions').removeClass('slideUp').addClass('slideDown'); setTimeout(function(){if ( jQuery('#csp-langoptions').hasClass('slideDown') ) { jQuery('#csp-langoptions').slideDown(100); jQuery('#csp-langswitcher-current').addClass('slide-down'); }}, 200) } );
			jQuery('#csp-langswitcher-toggle, #csp-langoptions').bind( 'mouseleave', function(){jQuery('#csp-langoptions').removeClass('slideDown').addClass('slideUp'); setTimeout(function(){if ( jQuery('#csp-langoptions').hasClass('slideUp') ) { jQuery('#csp-langoptions').slideUp(100, function(){ jQuery('#csp-langswitcher-current').removeClass('slide-down'); } ); }}, 300) } );
		}
		function csl_refresh_language_switcher() {
				jQuery.post("<?php echo $this->admin_url; ?>/admin-ajax.php", { action: 'wp_native_dashboard_refresh_switcher' },
					function(data) {
						csl_extend_dashboard_header(data);
					}
				)			
		}
		jQuery(document).ready(function() { 
			csl_extend_dashboard_header(false); 
		});
		//]]>
		</script>
		<?php
	}
	
	function on_ajax_wp_native_dashboard_change_language() {
		//TODO: standardize the USER-META behavoir
		$u = wp_get_current_user();
		
		if (!isset($u->wp_native_dashboard_language)) exit();
		update_user_meta($u->ID, 'wp_native_dashboard_language', $_POST['locale']);
		exit();		
	}
	
	function on_ajax_wp_native_dashboard_refresh_switcher() {
		if($this->no_dashboard_headline) {
			$this->on_print_admin_bar_switcher();
		}else{
			$this->on_print_dashboard_switcher();
		}
		exit();
	}
}