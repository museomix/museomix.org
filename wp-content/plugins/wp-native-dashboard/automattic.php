<?php

if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

if (!function_exists('esc_js')) {
	function esc_js($text) {
		return js_escape($text);
	}
}

class wp_native_dashboard_automattic {
	function wp_native_dashboard_automattic($tagged, $root_tagged) {
		$this->tagged_version 		= $tagged;
		$this->root_tagged_version 	= $root_tagged;
		add_action('admin_head', array(&$this, 'on_admin_head'));
		add_action('wp_ajax_wp_native_dashboard_check_repository', array(&$this, 'on_ajax_wp_native_dashboard_check_repository'));
		add_action('wp_ajax_wp_native_dashboard_check_language', array(&$this, 'on_ajax_wp_native_dashboard_check_language'));
		add_action('wp_ajax_wp_native_dashboard_delete_language', array(&$this, 'on_ajax_wp_native_dashboard_delete_language'));
		add_action('wp_ajax_wp_native_dashboard_download_language', array(&$this, 'on_ajax_wp_native_dashboard_download_language'));
	}
	
	function _snitch_off() {
		$snitch = remove_filter(
			'pre_http_request',
			array(
				'Snitch_HTTP',
				'inspect_request'
			), 10
		);
		if ($snitch) {
			remove_action(
				'http_api_debug',
				array(
					'Snitch_HTTP',
					'log_response'
				),
				10
			);
		}
		return $snitch;
	}
	
	function _snitch_on() {
		add_filter(
			'pre_http_request',
			array(
				'Snitch_HTTP',
				'inspect_request'
			),
			10,
			3
		);
		remove_action(
			'http_api_debug',
			array(
				'Snitch_HTTP',
				'log_response'
			),
			10
		);
	}
	
	function on_admin_head() {
		?>
		<script  type="text/javascript">
		//<![CDATA[
		function wpnd_delete_language() {
			var elem = jQuery(this);
			elem.parent().find('.ajax-feedback').css({visibility : 'visible' });
			var cred = { 
				action: 'wp_native_dashboard_delete_language',
				file : elem.attr('href') 
			};
			jQuery('#csp-credentials > form').find('input').each(function(i, e) {
				if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
				var s = jQuery(e).attr('name');
				var v = jQuery(e).val();
				cred[s] = v;
			});
			jQuery.ajax({
				type: "POST",
				url: "admin-ajax.php",
				data: cred,
				success: function(msg){
					elem.parents('tr').fadeOut('slow', function() { 
						var p = jQuery(this).parents('table');
						jQuery(this).remove(); 
						p.find('tr').each(function(i,e) {
							jQuery(e).removeClass('alternate');
							if (i % 2 == 0) jQuery(e).addClass('alternate');
						});
						
					});
					if(typeof csl_refresh_language_switcher == 'function') {
						csl_refresh_language_switcher();
					}
				},
				error: function(XMLHttpRequest, textStatus, errorThrown) {
					//handled in next version that also support all file system types
					if (XMLHttpRequest.status == '401') {
						jQuery('#csp-credentials').html(XMLHttpRequest.responseText).dialog({
							width: '500px',
							closeOnEscape: false,
							modal: true,
							resizable: false,
							title: '<b><?php echo esc_js(__('User Credentials required', 'wp-native-dashboard')); ?></b>',
							buttons: { 
								"<?php echo esc_js(__('Ok', 'wp-native-dashboard')); ?>": function() { 
									jQuery('#csp-credentials').dialog("close");
									elem.trigger('click');
								},
								"<?php echo esc_js(__('Cancel', 'wp-native-dashboard')); ?>": function() { 
									elem.parent().find('.ajax-feedback').css({visibility : 'hidden' });
									jQuery('#csp-credentials').dialog("close"); 
								} 
							},
							open: function(event, ui) {
								jQuery('#csp-credentials').show().css('width', 'auto');
							},
							close: function() {
								jQuery('#csp-credentials').dialog("destroy");
							}
						});
					}else {
						jQuery('#csp-credentials').html(XMLHttpRequest.responseText).dialog({
							width: '500px',
							closeOnEscape: false,
							modal: true,
							resizable: false,
							title: '<b><?php echo esc_js(__('Error', 'wp-native-dashboard')); ?></b>',
							buttons: { 
								"<?php echo esc_js(__('Ok', 'wp-native-dashboard')); ?>": function() { 
									elem.parent().find('.ajax-feedback').css({visibility : 'hidden' });
									jQuery('#csp-credentials').dialog("close");
								},
							},
							open: function(event, ui) {
								jQuery('#csp-credentials').show().css('width', 'auto');
							},
							close: function() {
								jQuery('#csp-credentials').dialog("destroy");
							}
						});
					}
					jQuery('#upgrade').hide().attr('disabled', 'disabled');
				}
			});
			return false;			
		}
		var last_auto_row = 0;
		function analyse_automattic_repository(idx) {
			if (idx<wp_native_dashboard_repository.entries) {
				if (idx==0) {
					last_auto_row = 0;
					jQuery('#svn-downloads .progressbar>div>div').css({ 'width' : '0%' });
					jQuery('#svn-downloads .progressbar div div div').html('0&nbsp;%');
					jQuery('#svn-downloads .progressbar').show();
					jQuery('#csp-check-repository').parent().find('.ajax-feedback').css({visibility : 'visible' });
				}
				jQuery.post("admin-ajax.php", { 
						action: 'wp_native_dashboard_check_language', 
						language: wp_native_dashboard_repository.langs[idx], 
						row: last_auto_row,
						ver: jQuery('#svn_wp_version').val()
					},
					function(data) {
						if (data != '')	{ 
							jQuery('#table_svn_i18n>tbody').append(data);
							last_auto_row += 1;
						}
						var perc = Math.min((idx+1)*100.0 / wp_native_dashboard_repository.entries, 100.0);
						jQuery('#svn-downloads .progressbar>div>div').css({ 'width' : perc + '%' });
						jQuery('#svn-downloads .progressbar div div div').html(Math.round(perc)+'&nbsp;%');
						window.setTimeout('analyse_automattic_repository('+(idx+1)+')', 50);
					}
				)
			}
			else{
				jQuery('#table_svn_i18n').show();
				jQuery('#svn-downloads .progressbar').hide();
				jQuery('#csp-check-repository').show();
				jQuery('#csp-check-repository').parent().find('.ajax-feedback').css({visibility : 'hidden' });
				jQuery('.csp-download-svn-file').click(function() {
					var elem = jQuery(this);
					elem.parent().find('.ajax-feedback').css({visibility : 'visible' });
					var cred = { 
						action: 'wp_native_dashboard_download_language',
						file : elem.attr('href') 
					};
					jQuery('#csp-credentials > form').find('input').each(function(i, e) {
						if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
						var s = jQuery(e).attr('name');
						var v = jQuery(e).val();
						cred[s] = v;
					});
					jQuery.ajax({
						type: "POST",
						url: "admin-ajax.php",
						data: cred,
						success: function(msg){
							jQuery('#table_local_i18n').append(msg).find('tr').each(function(i,e) {
								jQuery(e).removeClass('alternate');
								if (i % 2 == 0) jQuery(e).addClass('alternate');
							});
							jQuery('#table_local_i18n tr:last .csp-delete-local-file').click(wpnd_delete_language);
							elem.parents('tr').fadeOut('slow', function() { 
								var p = jQuery(this).parents('table');
								jQuery(this).remove();
								p.find('tr').each(function(i,e) {
									jQuery(e).removeClass('alternate');
									if (i % 2 == 0) jQuery(e).addClass('alternate');
								});
							});
							elem.parent().find('.ajax-feedback').css({visibility : 'hidden' });
							if(typeof csl_refresh_language_switcher == 'function') {
								csl_refresh_language_switcher();
							}
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							//handled in next version that also support all file system types
							if (XMLHttpRequest.status == '401') {
								jQuery('#csp-credentials').html(XMLHttpRequest.responseText).dialog({
									width: '500px',
									closeOnEscape: false,
									modal: true,
									resizable: false,
									title: '<b><?php echo esc_js(__('User Credentials required', 'wp-native-dashboard')); ?></b>',
									buttons: { 
										"<?php echo esc_js(__('Ok', 'wp-native-dashboard')); ?>": function() { 
											jQuery('#csp-credentials').dialog("close");
											elem.trigger('click');
										},
										"<?php echo esc_js(__('Cancel', 'wp-native-dashboard')); ?>": function() { 
											elem.parent().find('.ajax-feedback').css({visibility : 'hidden' });
											jQuery('#csp-credentials').dialog("close"); 
										} 
									},
									open: function(event, ui) {
										jQuery('#csp-credentials').show().css('width', 'auto');
									},
									close: function() {
										jQuery('#csp-credentials').dialog("destroy");
									}
								});
							}else {
								jQuery('#csp-credentials').html(XMLHttpRequest.responseText).dialog({
									width: '500px',
									closeOnEscape: false,
									modal: true,
									resizable: false,
									title: '<b><?php echo esc_js(__('Error', 'wp-native-dashboard')); ?></b>',
									buttons: { 
										"<?php echo esc_js(__('Ok', 'wp-native-dashboard')); ?>": function() { 
											jQuery('#csp-credentials').dialog("close");
										},
									},
									open: function(event, ui) {
										jQuery('#csp-credentials').show().css('width', 'auto');
									},
									close: function() {
										jQuery('#csp-credentials').dialog("destroy");
									}
								});
							}
							jQuery('#upgrade').hide().attr('disabled', 'disabled');							
							elem.parent().find('.ajax-feedback').css({visibility : 'hidden' });
						}
					});
					return false;
				});
			}
		}
		jQuery(document).ready(function($) { 
			$('#csp-check-repository').click(function() {
				jQuery('#table_svn_i18n').hide();
				var self = $(this);
				self.parent().find('.ajax-feedback').css({visibility : 'visible' });
				$.post("admin-ajax.php", { action: 'wp_native_dashboard_check_repository' },
					function(data) {
						self.parent().find('.ajax-feedback').css({visibility : 'hidden' });
						$(document.body).append(data);
					}
				)
				return false;
			});
			$('.csp-delete-local-file').click(wpnd_delete_language);
		});
		//]]>
		</script>
		<?php
	}
	
	function on_ajax_wp_native_dashboard_check_repository() {
		$installed = wp_native_dashboard_collect_installed_languages();
		$revision 	= 0;
		$langs 		= $installed;
		$url 		= 'http://svn.automattic.com/wordpress-i18n/';
		$snitch = $this->_snitch_off();
		$response = @wp_remote_get($url);
		if ($snitch) $this->_snitch_on();
		$error = is_wp_error($response);
		if(!$error) {
			$lines = split("\n",$response['body']);
			foreach($lines as $line) {
				if (preg_match("/href\s*=\s*\"(\S+)\/\"/", $line, $hits)) {
					if (in_array($hits[1], array('tools', 'theme', 'pot', 'http://subversion.tigris.org'))) continue;
					if (preg_match("/@/", $hits[1])) continue;
					if (!in_array($hits[1], $langs)) $langs[] = $hits[1];
				}
			}
			sort($langs);
		}
		?>
		<script type="text/javascript">
		//<![CDATA[
		var wp_native_dashboard_repository = {
			error: "<?php if($error) { echo __('The network connection to <strong>svn.automattic.com</strong> is currently not available. Please try again later.', 'wp-native-dashboard'); } ?>",
			entries: <?php echo count($langs); ?>,
			langs : ["<?php echo implode('","', $langs); ?>"]
		}
		if(wp_native_dashboard_repository.error.length==0) {
			jQuery('#csp-check-repository').hide();
			jQuery('#table_svn_i18n tbody').html('');
			analyse_automattic_repository(0);
		}
		else {
			jQuery('#csp-check-repository').hide();
			jQuery('#table_svn_i18n tbody').html('<tr><td align="center">'+wp_native_dashboard_repository.error+'</td></tr><tr><td align="center"><small><em><?php if($error) { echo esc_js(implode('<br/>',$response->get_error_messages())); } ?></em></small></td></tr>').parent().show();
		}
		//]]>
		</script>
		<?php
		exit();
	}
	
	function on_ajax_wp_native_dashboard_check_language() {
		//disable snitch
		$snitch = remove_filter(
			'pre_http_request',
			array(
				'Snitch_HTTP',
				'inspect_request'
			), 10
		);
		
		$lang 			= $_POST['language'];
		$row 			= $_POST['row'];
		$ver			= isset($_POST['ver']) ? $_POST['ver'] : $this->root_tagged_version;
		$installed 		= wp_native_dashboard_collect_installed_languages();
		$url 			= "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$this->tagged_version."/messages/";
		$url_root		= "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$ver."/messages/";
		$snitch = $this->_snitch_off();
		$response_mo 	= @wp_remote_get($url);
		if ($snitch) $this->_snitch_on();
		$found 			= false;
		$tagged			= $this->tagged_version;
		
		if (!is_wp_error($response_mo)&&($response_mo['response']['code'] != 404)){
			if (preg_match("/href\s*=\s*\"".$lang."\.mo\"/", $response_mo['body'])) 
				$found = true;
		}
		if ($found === false) {
			$url = $url_root;
			$tagged	= $ver;
			$snitch = $this->_snitch_off();
			$response_mo = @wp_remote_get($url);
			if($snitch) $this->_snitch_on();
			if (!is_wp_error($response_mo)&&($response_mo['response']['code'] != 404)){
				if (preg_match("/href\s*=\s*\"".$lang."\.mo\"/", $response_mo['body'])) 
					$found = true;
			}
		}
		//add snitch again if present
		if ($snitch) {
			add_filter(
				'pre_http_request',
				array(
					'Snitch_HTTP',
					'inspect_request'
				),
				10,
				3
			);
		}		
		if ($found === false) exit();
		$url .= $lang.'.mo';
		?>
		<tr id="tr-i18n-download-<?php echo $lang; ?>" class="<?php if (($row + 1) % 2) echo 'alternate'; ?>">
		<td><span class="i18n-file csp-<?php echo $lang; ?>"><?php echo wp_native_dashboard_get_name_of($lang); ?></span></td>
		<td><?php echo (wp_native_dashboard_is_rtl_language($lang) ? __('right to left', 'wp-native-dashboard') : '&nbsp;'); ?></td>
		<td>-n.a.-</td>
		<td><?php if(!in_array($lang, $installed)) : ?>
				<a class="csp-download-svn-file" href="<?php echo $url; ?>"><?php _e('Download','wp-native-dashboard'); echo '&nbsp;('.$tagged.')'; ?></a>&nbsp;<span><img src="images/loading.gif" class="ajax-feedback" title="" alt="" /></span>
			<?php else: echo '&nbsp;'; endif; ?>
		</td>
		</tr>
		<?php 
		exit();
	}
	
	function on_ajax_wp_native_dashboard_delete_language() {
	
		if (is_user_logged_in() && current_user_can('manage_options')) {
			global $wp_filesystem, $parent_file;
			
			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists('set_current_screen')) set_current_screen('tools'); //WP 3.0 fix
			
			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ($credentials = request_filesystem_credentials($url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if( ! empty($data) ){
					header('Status: 401 Unauthorized');
					header('HTTP/1.1 401 Unauthorized');
					echo $data;
					exit;
				}
				return;
			}

			if ( ! WP_Filesystem($credentials) ) {
				request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if( ! empty($data) ){
					header('Status: 401 Unauthorized');
					header('HTTP/1.1 401 Unauthorized');
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;

			$file = basename($_POST['file']);
			$dir = $wp_filesystem->find_folder(WP_LANG_DIR.'/');
			$filename = $dir.$file;
			if (($dir === false) || !$wp_filesystem->is_file($filename)) {
				header('Status: 404 Not Found');
				header('HTTP/1.1 404 Not Found');
				echo sprintf(__("The language file %s you tried to delete does not exist.", 'wp-native-dashboard'), $file);
				exit();				
			}
			
			ob_start();
			if ( WP_Filesystem($credentials) && is_object($wp_filesystem) ) {
				if($wp_filesystem->delete($filename)) {
					$wp_filesystem->delete($dir.'continents-cities-'.$file);
					$wp_filesystem->delete($dir.'ms-'.$file);
					$wp_filesystem->delete($dir.'admin-'.$file);
					$wp_filesystem->delete($dir.'admin-network-'.$file);
					$wp_filesystem->delete(substr($filename, 0, -2).'php');
					$wp_filesystem->delete(substr($filename, 0, -2).'css');
					$wp_filesystem->delete(substr($filename, 0, -3).'-ie.css');
					$wp_filesystem->delete($dir.'ms-'.substr($file, 0, -2).'css');
					ob_end_clean();
					exit();
				}
			}
			ob_end_clean();
		}
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("You do not have the permission to delete language files.", 'wp-native-dashboard');
		exit();
	}
	
	function on_ajax_wp_native_dashboard_download_language() {
		if (is_user_logged_in() && current_user_can('manage_options')) {
			global $wp_filesystem, $parent_file;
			$current_parent  = $parent_file;
			$parent_file 	 = 'tools.php'; //needed for screen icon :-)
			if (function_exists('set_current_screen')) set_current_screen('tools'); //WP 3.0 fix
						
			//check the file system
			ob_start();
			$url = 'admin-ajax.php';
			if ( false === ($credentials = request_filesystem_credentials($url)) ) {
				$data = ob_get_contents();
				ob_end_clean();
				if( ! empty($data) ){
					header('Status: 401 Unauthorized');
					header('HTTP/1.1 401 Unauthorized');
					echo $data;
					exit;
				}
				return;
			}

			if ( ! WP_Filesystem($credentials) ) {
				request_filesystem_credentials($url, '', true); //Failed to connect, Error and request again
				$data = ob_get_contents();
				ob_end_clean();
				if( ! empty($data) ){
					header('Status: 401 Unauthorized');
					header('HTTP/1.1 401 Unauthorized');
					echo $data;
					exit;
				}
				return;
			}
			ob_end_clean();
			$parent_file = $current_parent;

			
			$file = basename($_POST['file']);
			$lang = substr($file,0,-3);
			$tagged = $this->tagged_version;
			if (preg_match('/\/tags\/(\d+\.\d+|\d+\.\d+\.\d+)\/messages/', $_POST['file'], $h)) {
				$tagged = $h[1];
			}
			//disable snitch
			$snitch = $this->_snitch_off();
			$response_mo = @wp_remote_get("http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/messages/".$file);
			if ($snitch) $this->_snitch_on();
			if(!is_wp_error($response_mo) && ($response_mo['response']['code'] != 404)) {
				ob_start();
				if ( WP_Filesystem($credentials) && is_object($wp_filesystem) ) {
					$dir = $wp_filesystem->find_folder(WP_LANG_DIR.'/');
					if (!$wp_filesystem->is_dir($dir)) { 
						//US original versions doesn't contain any languages folder !
						if($wp_filesystem->method == 'direct')
							$dir = WP_LANG_DIR.'/';
						else
							$dir = '/'.str_replace(ABSPATH, '', WP_LANG_DIR.'/');
						if ($wp_filesystem->mkdir($dir) === false) {
							ob_end_clean();
							header('Status: 404 Not Found');
							header('HTTP/1.1 404 Not Found');
							echo sprintf(__("The missing languages directory could not be created at '%s'.", 'wp-native-dashboard'), $dir);
							exit();
						}
					}
					$done = $wp_filesystem->put_contents($dir.$file, $response_mo['body']);
					if ($done) {
						global $wp_version;
						$additional_download_files = array(
							//FORMAT:	file system name => ( min-version => 'x.x', 'location' => url, 'alternative' => url)
							//continent cities translation
							$dir.'continents-cities-'.$file => array(
								'min-version' => '2.8',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/continents-cities-".$file,
								'alternative' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/messages/continents-cities-".$file
							),
							//multisite translations
							$dir.'ms-'.$file => array(
								'min-version' => '3.0',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/ms-".$file,
								'alternative' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/messages/ms-".$file
							),
							//admin file (3.4)
							$dir.'admin-'.$file => array(
								'min-version' => '3.4',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/admin-".$file,
								'alternative' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/messages/admin-".$file
							),
							//admin network file (3.4)
							$dir.'admin-network-'.$file => array(
								'min-version' => '3.4',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/admin-network-".$file,
								'alternative' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/messages/admin-network-".$file
							),
							//RTL or language adjustment support
							$dir.$lang.'.php' => array(
								'min-version' => '2.0',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/".$lang.'.php',
								'alternative' => false
							),
							//language related stylesheet extensions
							$dir.$lang.'.css' => array(
								'min-version' => '3.0',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/".$lang.'.css',
								'alternative' => false
							),
							$dir.$lang.'-ie.css' => array(
								'min-version' => '3.0',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/".$lang.'-ie.css',
								'alternative' => false
							),
							$dir.'ms-'.$lang.'.css' => array(
								'min-version' => '3.0',
								'location' => "http://svn.automattic.com/wordpress-i18n/".$lang."/tags/".$tagged."/dist/wp-content/languages/ms-".$lang.'.css',
								'alternative' => false
							)
						);
						
						foreach($additional_download_files as $fsf => $desc) {
							if (version_compare($wp_version, $desc['min-version'], '>=')) {
								$snitch = $this->_snitch_off();
								$response_additional = @wp_remote_get($desc['location']);
								if($snitch) $this->_snitch_on();
								if(is_wp_error($response_additional)||($response_additional['response']['code'] == 404)) {
									if ($desc['alternative'] !== false) {
										$snitch = $this->_snitch_off();
										$response_additional = @wp_remote_get($desc['alternative']);
										if($snitch) $this->_snitch_on();
									}
								}
								if(!is_wp_error($response_additional)&&($response_additional['response']['code'] != 404)) {
									$wp_filesystem->put_contents($fsf, $response_additional['body']);
								}else {
									//special turn for required but not yet provided RTL extension files
									//enables RTL support for affected languages anyway
									if (($fsf == $dir.$lang.'.php') && wp_native_dashboard_is_rtl_language($lang) && version_compare($wp_version, '3.4-alpha', '<')) {
										$wp_filesystem->put_contents($fsf, wp_native_dashboard_rtl_extension_file_content());
									}
								}								
							}
						}
						ob_end_clean();
						$mo = str_replace('\\', '/', WP_LANG_DIR.'/'.$file);
						?>
						<tr id="tr-i18n-installed-<?php echo $lang; ?>">
							<td><span class="i18n-file csp-<?php echo $lang; ?>"><?php echo wp_native_dashboard_get_name_of($lang); ?></span></td>
							<td><?php echo (wp_native_dashboard_is_rtl_language($lang) ? __('right to left', 'wp-native-dashboard') : '&nbsp;'); ?></td>
							<td><?php echo filesize($mo).'&nbsp;Bytes'; ?></td>
							<td><?php if($lang != 'en_US') : ?><a class="csp-delete-local-file" href="<?php echo $mo; ?>"><?php _e('Delete','wp-native-dashboard'); ?></a>&nbsp;<span><img src="images/loading.gif" class="ajax-feedback" title="" alt="" /></span><?php endif; ?></td>
						</tr>
						<?php
						exit();
					}
				}
				ob_end_clean();
			}
		}
		header('Status: 404 Not Found');
		header('HTTP/1.1 404 Not Found');
		_e("The download is currently not available.", 'wp-native-dashboard');
		exit();
	}
	
}

?>