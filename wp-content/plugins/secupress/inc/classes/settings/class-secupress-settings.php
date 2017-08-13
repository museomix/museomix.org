<?php
defined( 'ABSPATH' ) or die( 'Cheatin\' uh?' );


/**
 * Base class for settings.
 *
 * @package SecuPress
 * @since 1.0
 */
abstract class SecuPress_Settings extends SecuPress_Singleton {

	const VERSION = '1.0.1';

	/**
	 * Current module: corresponds to the page tab, like `users_login`.
	 *
	 * @var (string)
	 */
	protected $modulenow;

	/**
	 * Current section: corresponds to a block, like `login_auth`.
	 *
	 * @var (string)
	 */
	protected $sectionnow;

	/**
	 * Current plugin (or sub-module): corresponds to a field, like `captcha`.
	 *
	 * @var (string)
	 */
	protected $pluginnow;

	/**
	 * Section descriptions.
	 *
	 * @var (array)
	 */
	protected $section_descriptions = array();

	/**
	 * Tell if a section is disabled.
	 *
	 * @var (bool|null)
	 */
	protected $section_is_disabled = null;

	/**
	 * Section Save buttons.
	 *
	 * @var (array)
	 */
	protected $section_save_buttons = array();

	/**
	 * Form action attribute (URL).
	 *
	 * @var (string)
	 */
	protected $form_action;

	/**
	 * Tells if the current module should be wrapped in a form.
	 *
	 * @var (bool)
	 */
	protected $with_form = true;


	/** Setters ================================================================================= */

	/**
	 * Set the current module.
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	protected function set_current_module() {
		die( 'Method SecuPress_Settings::set_current_module() must be over-ridden in a sub-class.' );
		return $this;
	}


	/**
	 * Set the current section.
	 *
	 * @since 1.0
	 *
	 * @param (string) $section The section to set.
	 *
	 * @return (object) The class instance.
	 */
	final protected function set_current_section( $section ) {
		$this->sectionnow = $section;
		return $this;
	}


	/**
	 * Set the current plugin.
	 *
	 * @since 1.0
	 *
	 * @param (string) $plugin The plugin to set.
	 *
	 * @return (object) The class instance.
	 */
	final protected function set_current_plugin( $plugin ) {
		$this->pluginnow = $plugin;
		return $this;
	}


	/**
	 * Set the current section description.
	 *
	 * @since 1.0
	 *
	 * @param (string) $description The description to set.
	 *
	 * @return (object) The class instance.
	 */
	final protected function set_section_description( $description ) {
		$section_id = $this->modulenow . '|' . $this->sectionnow;

		$this->section_descriptions[ $section_id ] = $description;

		return $this;
	}


	/**
	 * Tell if the current section should display a Save button.
	 *
	 * @since 1.0
	 *
	 * @param (bool) $value True to display the button. False to hide it.
	 *
	 * @return (object) The class instance.
	 */
	final protected function set_section_save_button( $value ) {
		$section_id = $this->get_section_id();

		if ( $value ) {
			$this->section_save_buttons[ $section_id ] = 1;
		} else {
			unset( $this->section_save_buttons[ $section_id ] );
		}

		return $this;
	}


	/** Getters ================================================================================= */

	/**
	 * Get the current module.
	 *
	 * @since 1.0
	 *
	 * @return (string) The current module.
	 */
	final public function get_current_module() {
		return $this->modulenow;
	}


	/**
	 * Get the current section.
	 *
	 * @since 1.0
	 *
	 * @return (string) The current section.
	 */
	final public function get_current_section() {
		return $this->sectionnow;
	}


	/**
	 * Get the current plugin.
	 *
	 * @since 1.0
	 *
	 * @return (string) The current plugin.
	 */
	final public function get_current_plugin() {
		return $this->pluginnow;
	}


	/**
	 * Get the current section ID.
	 *
	 * @since 1.0
	 *
	 * @return (string) The current section ID.
	 */
	public function get_section_id() {
		return 'module_' . $this->modulenow . '|' . $this->sectionnow;
	}


	/**
	 * Get the form action attribute (URL).
	 *
	 * @since 1.0
	 *
	 * @return (string) The attribute.
	 */
	final public function get_form_action() {
		return $this->form_action;
	}


	/**
	 * Tells if the current module should be wrapped in a form.
	 *
	 * @since 1.0
	 *
	 * @return (bool)
	 */
	final public function get_with_form() {
		return $this->with_form;
	}


	/** Init ==================================================================================== */

	/**
	 * Init: this method is required by the class `SecuPress_Singleton`.
	 *
	 * @since 1.0
	 */
	protected function _init() {
		$this->set_current_module();

		$this->form_action = is_network_admin() ? admin_url( 'admin-post.php' ) : admin_url( 'options.php' );
		$this->form_action = esc_url( $this->form_action );
	}


	/** Sections ================================================================================ */

	/**
	 * Add a section in the page (a block).
	 *
	 * @since 1.0
	 *
	 * @param (string) $title The section title.
	 * @param (array)  $args  An array allowing 2 parameters:
	 *                        - (bool) $with_roles       Whenever to display a "Affected roles" radios list.
	 *                        - (bool) $with_save_button Whenever to display a "Save Settings" button.
	 *
	 * @return (object) The class instance.
	 */
	protected function add_section( $title, $args = null ) {
		static $i = 0;

		$args       = wp_parse_args( $args, array( 'with_roles' => false, 'with_save_button' => true ) );
		$actions    = '';
		$section_id = $this->get_section_id();

		if ( ! empty( $args['with_roles'] ) ) {
			$actions .= '<button type="button" id="affected-role-' . $i . '" class="hide-if-no-js no-button button-actions-title">' . __( 'Roles', 'secupress' ) . ' <span class="dashicons dashicons-arrow-right" aria-hidden="true"></span></button>';
		}

		add_settings_section(
			$section_id,
			$title . $actions,
			array( $this, 'print_section_description' ),
			$section_id
		);

		if ( (bool) $args['with_save_button'] ) {
			$this->section_save_buttons[ $section_id ] = 1;
		}

		if ( ! $args['with_roles'] ) {
			return $this;
		}

		$this->add_field( array(
			'title'        => '<span class="dashicons dashicons-groups"></span> ' . __( 'Affected Roles', 'secupress' ),
			'description'  => __( 'Which roles will be affected by this module?', 'secupress' ),
			'depends'      => 'affected-role-' . $i,
			'row_class'    => 'affected-role-row',
			'name'         => $this->get_field_name( 'affected_role' ),
			'type'         => 'roles',
			'label_screen' => __( 'Affected Roles', 'secupress' ),
			'helpers'      => array(
				array(
					'type'        => 'description',
					'description' => __( 'Future roles will be automatically checked.', 'secupress' ),
				),
				array(
					'type'        => 'warning',
					'class'       => 'hide-if-js',
					'description' => __( 'Select 1 role minimum', 'secupress' ),
				),
			),
		) );

		++$i;

		return $this;
	}


	/**
	 * A wrapper for `$this->do_settings_sections()` that wraps the sections in a `<div>` tag and prints the "Save" button.
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	protected function do_sections() {
		$section_id       = $this->get_section_id();
		$html_id          = explode( '|', $section_id );
		$html_id          = sanitize_html_class( implode( '--', $html_id ) );
		$with_save_button = ! empty( $this->section_save_buttons[ $section_id ] );

		/**
		 * Fires before a section.
		 *
		 * @since 1.0
		 *
		 * @param (bool) $with_save_button True if a "Save All Changes" button will be printed.
		 */
		do_action( 'secupress.settings.before_section_' . $this->sectionnow, $with_save_button );

		$this->section_is_disabled = true;

		echo '<div class="secupress-settings-section" id="secupress-settings-' . $html_id . '">';

		echo '<div class="secublock">';
			$this->do_settings_sections();
		echo '</div><!-- .secublock -->';

		if ( $with_save_button ) {
			$args = array(
				'type' => 'primary',
				'name' => $this->sectionnow . '_submit',
			);

			if ( $this->section_is_disabled ) {
				$args['wrap']             = true;
				$args['other_attributes'] = array(
					'disabled'      => 'disabled',
					'aria-disabled' => 'true',
				);
			}
			/**
			 * Filter the arguments passed to the section submit button.
			 *
			 * @since 1.0.6
			 *
			 * @param (array) $args An array of arguments passed to the `submit_button()` method.
			 */
			$args = apply_filters( 'secupress.settings.section.submit_button_args', $args );

			call_user_func_array( array( __CLASS__, 'submit_button' ), $args );
		}

		echo '</div><!-- #secupress-settings-' . $html_id . ' -->';

		/**
		 * Fires after a section.
		 *
		 * @since 1.0
		 *
		 * @param (bool) $with_save_button True if a "Save All Changes" button will be printed.
		 */
		do_action( 'secupress.settings.after_section_' . $this->sectionnow, $with_save_button );

		$this->section_is_disabled = null;

		return $this;
	}


	/**
	 * Like the real `do_settings_sections()` but using a custom `do_settings_fields()`.
	 *
	 * @since 1.0
	 */
	final protected function do_settings_sections() {
		global $wp_settings_sections, $wp_settings_fields;

		$section_id = $this->get_section_id();

		if ( ! isset( $wp_settings_sections[ $section_id ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_sections[ $section_id ] as $section ) {

			$header_open_tag = false;

			if ( $section['title'] ) {
				echo '<div class="secupress-settings-section-header">';
				$header_open_tag = true;
				$id = explode( '|', $section['id'] );
				$id = end( $id );
				echo '<h3 class="secupress-settings-section-title" id="module-' . sanitize_html_class( $id ) . '">' . $section['title'] . '</h3>' . "\n";
			}

			if ( $section['callback'] ) {
				echo ( $header_open_tag ? '' : '<div class="secupress-settings-section-header">' );
				$header_open_tag = true;
				call_user_func( $section['callback'], $section );
			}

			echo ( $header_open_tag ? '</div><!-- .secupress-settings-section-header -->' : '' );

			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $section_id ] ) || ! isset( $wp_settings_fields[ $section_id ][ $section['id'] ] ) ) {
				continue;
			}

			echo '<div class="secupress-form-table">';
				$this->do_settings_fields( $section_id, $section['id'] );
			echo '</div>';
		}
	}


	/** Generic fields ========================================================================== */

	/**
	 * The main callback that prints basic fields.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array with the following parameters:
	 *                - (string) $type              The field type: 'number', 'email', 'tel', 'text', 'textarea', 'select', 'checkbox', 'checkboxes', 'radioboxes', 'radios', 'roles', 'countries', 'nonlogintimeslot'.
	 *                - (string) $name              The name attribute. Also used as id attribute if `$label_for` is not provided.
	 *                - (string) $label_for         The id attribute. Also used as name attribute if `$name` is not provided.
	 *                - (bool)   $plugin_activation Set to true if the field is not used for a setting but to (de)activate a plugin.
	 *                - (mixed)  $default           The default value.
	 *                - (mixed)  $value             The field value. If not provided the field will look for an option stored in db.
	 *                - (array)  $options           Used for 'select', 'checkboxes', 'radioboxes' and 'radios': all possible choices for the user (value => label).
	 *                - (string) $fieldset          Wrap the field in a `<fieldset>` tag. Possible values: 'start', 'end', 'no' and 'yes'. 'checkboxes', 'radioboxes' and 'radios' are automatically wrapped. 'start' and 'end' are not used yet.
	 *                - (string) $label_screen      Used for the `<legend>` tag when a fieldset is used.
	 *                - (string) $label             A label to display on top of the field. Also used as field label for the 'checkbox' type.
	 *                - (string) $label_before      A label to display before the field.
	 *                - (string) $label_after       A label to display after the field.
	 *                - (bool)   $disabled          True to disable the field. Pro fields are automatically disabled on the free version.
	 *                - (array)  $attributes        An array of html attributes to add to the field (like min and max for a 'number' type).
	 *                - (array)  $helpers           An array containing the helpers. See `self::helpers()`.
	 */
	protected function field( $args ) {
		$args = array_merge( array(
			'type'              => '',
			'name'              => '',
			'label_for'         => '',
			'plugin_activation' => false,
			'default'           => null,
			'value'             => null,
			'options'           => array(),
			'fieldset'          => null,
			'label_screen'      => '',
			'label'             => '',
			'label_before'      => '',
			'label_after'       => '',
			'disabled'          => false,
			'attributes'        => array(),
			'helpers'           => array(),
		), $args );

		if ( $args['plugin_activation'] ) {
			$option_name = 'secupress-plugin-activation';
		} else {
			$option_name = 'secupress' . ( 'global' !== $this->modulenow ? '_' . $this->modulenow : '' ) . '_settings';
		}
		$name_attribute = $option_name . '[' . $args['name'] . ']';
		$disabled       = (bool) $args['disabled'];

		// Type.
		$args['type'] = 'radio' === $args['type'] ? 'radios' : $args['type'];

		// Value.
		if ( isset( $args['value'] ) ) {
			if ( $args['plugin_activation'] ) {
				// For the checkboxes that activate un sub-module, make sure they are not checked if they are disabled.
				$value = $disabled ? null : $args['value'];
			} else {
				$value = $args['value'];
			}
		} elseif ( 'global' === $this->modulenow ) {
			$value = secupress_get_option( $args['name'] );
		} else {
			$value = secupress_get_module_option( $args['name'] );
		}

		if ( is_null( $args['default'] ) ) {
			$args['default'] = $args['plugin_activation'] ? 0 : '';
		}

		if ( is_null( $value ) ) {
			$value = $args['default'];
		}

		// HTML attributes.
		$args['label_for'] = $args['label_for'] ? $args['label_for'] : $args['name'];
		$args['label_for'] = esc_attr( $args['label_for'] );

		$attributes = '';
		$args['attributes']['class'] = ! empty( $args['attributes']['class'] ) ? (array) $args['attributes']['class'] : array();

		if ( 'radioboxes' === $args['type'] || 'checkboxes' === $args['type'] || 'checkbox' === $args['type'] || 'roles' === $args['type'] ) {
			$args['attributes']['class'][] = 'secupress-checkbox';
		}
		if ( 'countries' === $args['type'] ) {
			$args['attributes']['class'][] = 'secupress-checkbox';
			$args['attributes']['class'][] = 'secupress-checkbox-mini';
		}

		if ( 'radios' === $args['type'] ) {
			$args['attributes']['class'][] = 'secupress-radio';
		}

		if ( 'number' === $args['type'] ) {
			$args['attributes']['class'][] = 'small-text';
		} elseif ( 'radioboxes' === $args['type'] ) {
			$args['attributes']['class'][] = 'radiobox';
		}

		if ( $args['attributes']['class'] ) {
			$args['attributes']['class'] = implode( ' ', array_map( 'sanitize_html_class', $args['attributes']['class'] ) );
		} else {
			unset( $args['attributes']['class'] );
		}

		if ( ! empty( $args['attributes']['pattern'] ) ) {
			$args['attributes']['data-pattern'] = $args['attributes']['pattern'];
		}

		if ( ! empty( $args['attributes']['required'] ) ) {
			$args['attributes']['data-required']      = 'required';
			$args['attributes']['data-aria-required'] = 'true';
		}

		if ( $disabled ) {
			$args['attributes']['disabled'] = 'disabled';
		}

		unset( $args['attributes']['pattern'], $args['attributes']['required'] );

		if ( ! empty( $args['attributes'] ) ) {
			foreach ( $args['attributes'] as $attribute => $attribute_value ) {
				$attributes .= ' ' . $attribute . '="' . esc_attr( $attribute_value ) . '"';
			}
		}

		// Fieldset.
		$has_fieldset_begin = false;
		$has_fieldset_end   = false;

		switch ( $args['fieldset'] ) {
			case 'start' :
				$has_fieldset_begin = true;
				break;
			case 'end' :
				$has_fieldset_end = true;
				break;
			case 'no' :
				break;
			default :
				$fieldset_auto = array( 'checkboxes' => 1, 'radioboxes' => 1, 'radios' => 1, 'roles' => 1 );

				if ( 'yes' === $args['fieldset'] || isset( $fieldset_auto[ $args['type'] ] ) ) {
					$has_fieldset_begin = true;
					$has_fieldset_end   = true;
				}
		}

		if ( $has_fieldset_begin ) {
			echo '<fieldset class="fieldname-' . sanitize_html_class( $args['name'] ) . ' fieldtype-' . sanitize_html_class( $args['type'] ) . '">';

			if ( ! empty( $args['label_screen'] ) ) {
				echo '<legend class="screen-reader-text"><span>' . $args['label_screen'] . '</span></legend>';
			}
		}

		// Labels.
		$label_open  = '';
		$label_close = '';
		if ( '' !== $args['label_before'] || '' !== $args['label'] || '' !== $args['label_after'] ) {
			$label_open  = '<label class="secupress-' . esc_attr( $args['type'] ) . '-label' . ( $disabled ? ' disabled' : '' ) . '">';
			$label_close = '</label>';
		}

		// Types.
		switch ( $args['type'] ) {
			case 'number' :
			case 'email' :
			case 'tel' :
			case 'url' :
			case 'text' :

				echo $label_open; ?>
					<?php
					echo $args['label'] ? $args['label'] . '<br/>' : '';
					echo $args['label_before'];
					echo '<input type="' . $args['type'] . '" id="' . $args['label_for'] . '" name="' . $name_attribute . '" value="' . esc_attr( $value ) . '"' . $attributes . '/>';
					echo $args['label_after'];
					?>
				<?php
				echo $label_close;
				break;

			case 'textarea' :

				$value       = esc_textarea( html_entity_decode( implode( "\n" , (array) $value ), ENT_QUOTES ) );
				$attributes .= empty( $args['attributes']['cols'] ) ? ' cols="50"' : '';
				$attributes .= empty( $args['attributes']['rows'] ) ? ' rows="5"'  : '';

				// Don't add expandable feature for these exceptions.
				$exceptions   = array(
					'support_description',
					'notification-types_emails',
				);
				$is_exception = in_array( $args['name'], $exceptions, true );

				$args['label_before'] .= $is_exception ? '' : '<div class="secupress-textarea-container">';
				$args['label_after'] .= $is_exception ? '' : '</div>';

				echo $label_open; ?>
					<?php
					echo $args['label'] ? '<span class="secupress-bold">' . $args['label'] . '</span><br/>' : '';
					echo $args['label_before'];
					echo '<textarea id="' . $args['label_for'] . '" name="' . $name_attribute . '"' . $attributes . ' spellcheck="false">' . $value . '</textarea>';
					echo $args['label_after'];
					?>
				<?php
				echo $label_close;
				break;

			case 'select' :

				$value = array_flip( (array) $value );
				$has_disabled = false;

				echo $label_open; ?>
					<?php
					echo $args['label'] ? $args['label'] . '<br/>' : '';
					echo $args['label_before'];
					?>
					<select id="<?php echo $args['label_for']; ?>" name="<?php echo $name_attribute; ?>"<?php echo $attributes; ?>>
						<?php
						foreach ( $args['options'] as $val => $title ) {
							$disabled = '';
							if ( static::is_pro_feature( $args['name'] . '|' . $val ) ) {
								$disabled     = ' disabled="disabled"';
								$has_disabled = true;
							}
							?>
							<option value="<?php echo $val; ?>"<?php selected( isset( $value[ $val ] ) ); ?><?php echo $disabled; ?>><?php echo $title . ( $disabled ? ' (*)' : '' ); ?></option>
							<?php
						}
						?>
					</select>
					<?php
					echo $args['label_after'];
					?>
				<?php
				echo $label_close;

				echo $has_disabled ? static::get_pro_version_string( '<span class="description">(*) %s</span>' ) : '';

				break;

			case 'checkbox' :

				echo '<p class="secupress-checkbox-line">';
				echo $label_open; ?>
					<?php
					echo $args['label_before'];
					echo '<input type="checkbox" id="' . $args['label_for'] . '" name="' . $name_attribute . '" value="1"' . checked( $value, 1, false ) . $attributes . ' />';
					echo '<span class="label-text">' . $args['label'] . '</span>';
					?>
				<?php echo $label_close;
				echo '</p>';
				break;

			case 'checkboxes' :
			case 'radioboxes' :

				$value = array_flip( (array) $value );

				foreach ( $args['options'] as $val => $title ) {
					$args['label_for'] = $args['name'] . '_' . $val;
					$disabled          = static::is_pro_feature( $args['name'] . '|' . $val ) ? ' disabled="disabled"' : '';
					?>
					<p class="secupress-fieldset-item secupress-fieldset-item-<?php echo $args['type']; ?><?php echo static::is_pro_feature( $args['name'] . '|' . $val ) ? ' secupress-pro-option' : ''; ?>">
						<label<?php echo $disabled ? ' class="disabled"' : ''; ?> for="<?php echo esc_attr( $args['label_for'] ); ?>">
							<input type="checkbox" id="<?php echo $args['label_for']; ?>" name="<?php echo $name_attribute; ?>[]" value="<?php echo $val; ?>"<?php checked( isset( $value[ $val ] ) ); ?><?php echo $disabled; ?><?php echo $attributes; ?>>
							<?php echo '<span class="label-text">' . $title . '</span>'; ?>
						</label>
					<?php echo static::is_pro_feature( $args['name'] . '|' . $val ) ? static::get_pro_version_string( '<span class="description secupress-get-pro-version">%s</span>' ) : ''; ?>
					</p>
					<?php
				}
				break;

			case 'radios' : // Video killed the radio star.

				foreach ( $args['options'] as $val => $title ) {
					$args['label_for'] = $args['name'] . '_' . $val;
					$disabled          = static::is_pro_feature( $args['name'] . '|' . $val ) ? ' disabled="disabled"' : '';

					if ( ! $disabled && strpos( $title, 'secupress-coming-soon-feature' ) !== false ) {
						$disabled = ' disabled="disabled"';
					}
					?>
					<p class="secupress-radio-line<?php echo static::is_pro_feature( $args['name'] . '|' . $val ) ? ' secupress-pro-option' : ''; ?>">
						<label<?php echo $disabled ? ' class="disabled"' : ''; ?> for="<?php echo esc_attr( $args['label_for'] ); ?>">
							<input type="radio" id="<?php echo $args['label_for']; ?>" name="<?php echo $name_attribute; ?>" value="<?php echo $val; ?>"<?php checked( $value, $val ); ?><?php echo $disabled; ?><?php echo $attributes; ?>>
							<?php echo '<span class="label-text">' . $title . '</span>'; ?>
						</label>
						<?php echo static::is_pro_feature( $args['name'] . '|' . $val ) ? static::get_pro_version_string( '<span class="description secupress-get-pro-version">%s</span>' ) : ''; ?>
					</p>
					<?php
				}
				break;

			case 'roles' :

				$value = array_flip( (array) $value );
				$roles = new WP_Roles();
				$roles = $roles->get_names();
				$roles = array_map( 'translate_user_role', $roles );

				foreach ( $roles as $val => $title ) {
					?>
					<p class="secupress-checkbox-roles-line">
						<label<?php echo $disabled ? ' class="disabled"' : ''; ?>>
							<input type="checkbox" name="<?php echo $name_attribute; ?>[]" value="<?php echo $val; ?>"<?php checked( ! isset( $value[ $val ] ) ); ?><?php echo $attributes; ?>>
							<?php echo '<span class="label-text">' . $title . '</span>'; ?>
						</label>
					</p>
					<?php
				}
				?>
				<input type="hidden" name="<?php echo $name_attribute; ?>[witness]" value="1" />
				<?php
				break;

			case 'html' :

				echo $value;
				break;

			case 'submit' :

				echo '<button type="submit" class="secupress-button" id="' . esc_attr( $args['name'] ) . '">' . $args['label'] . '</button>';
				break;

			default :
				if ( secupress_is_pro() && function_exists( 'secupress_pro_' . $args['type'] . '_field' ) ) {
					call_user_func( 'secupress_pro_' . $args['type'] . '_field', $args, $this );
				} elseif ( method_exists( $this, $args['type'] ) ) {
					call_user_func( array( $this, $args['type'] ), $args );
				} else {
					echo 'Missing or incorrect type'; // Do not translate.
				}
		}

		// Helpers.
		static::helpers( $args );

		if ( $has_fieldset_end ) {
			echo '</fieldset>';
		}
	}


	/**
	 * Used to display buttons.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array of parameters. See `::field()`.
	 */
	protected function field_button( $args ) {

		if ( ! empty( $args['label'] ) ) {
			$class  = sanitize_html_class( $args['name'] );
			$class .= ! empty( $args['style'] ) ? ' button-' . sanitize_html_class( $args['style'] ) : ' button-secondary';
			$id     = ! empty( $args['id'] )    ? ' id="' . $args['id'] . '"' : '';

			if ( ! empty( $args['url'] ) ) {
				echo '<a' . $id . ' class="secupress-button secupress-button-primary secupressicon-' . $class . ( ! empty( $args['disabled'] ) ? ' disabled' : '' ) . '" href="' . esc_url( $args['url'] ) . '">' . $args['label'] . '</a>';
			}
			else {
				echo '<button' . $id . ' class="secupress-button secupress-button-primary secupressicon-' . $class . '"' . ( ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '' ) . ' type="button">' . $args['label'] . '</button>';
			}
		}

		// Helpers.
		static::helpers( $args );
	}


	/**
	 * Helpers printed after a field.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array containing a 'helpers' key.
	 *                      This 'helpers' key contains a list of arrays that contain:
	 *                      - (string) $description The text to print.
	 *                      - (string) $type        The helper type: 'description', 'help', 'warning'.
	 *                      - (string) $class       A html class to add to the text.
	 *                      - (string) $depends     Like in `$this->do_settings_fields()`, used to show/hide the helper depending on a field value.
	 */
	protected static function helpers( $args ) {
		if ( empty( $args['helpers'] ) || ! is_array( $args['helpers'] ) ) {
			return;
		}

		foreach ( $args['helpers'] as $helper ) {

			if ( empty( $helper['description'] ) ) {
				continue;
			}

			$depends = '';
			if ( ! empty( $helper['depends'] ) ) {
				$helper['depends'] = explode( ' ', $helper['depends'] );
				$depends           = ' depends-' . implode( ' depends-', $helper['depends'] );
			}

			$class = ! empty( $helper['class'] ) ? ' ' . trim( $helper['class'] ) : '';
			$name  = $args['name'];
			$type  = $helper['type'];
			$tag   = preg_match( '@</?p[ >]@', $helper['description'] ) ? 'div' : 'p';

			switch ( $type ) {
				case 'description' :
					$description = '<' . $tag . ' class="description desc' . $depends . $class . '">' . $helper['description'] . '</' . $tag . '>';
					break;
				case 'help' :
					$description = '<' . $tag . ' class="description help' . $depends . $class . '">' . $helper['description'] . '</' . $tag . '>';
					break;
				case 'warning' :
					$description = '<' . $tag . ' class="description warning' . $depends . $class . '">' . ( 'p' === $tag ? '' : '<p>' ) . '<strong>' . __( 'Warning: ', 'secupress' ) . '</strong> ' . $helper['description'] . '</' . $tag . '>'; // Don't forget to close the <p> tag.
					break;
				default :
					continue;
			}

			/**
			 * Filter the helper description.
			 *
			 * @since 1.0
			 *
			 * @param (string) $description The description.
			 * @param (string) $name        The field name argument.
			 * @param (string) $type        The helper type.
			 */
			echo apply_filters( 'secupress.settings.help', $description, $name, $type );
		}
	}


	/** Fields related ========================================================================== */

	/**
	 * Get a correct name for setting fields based on the current module.
	 *
	 * @since 1.0
	 *
	 * @param (string) $field A field name.
	 *
	 * @return (string)
	 */
	final protected function get_field_name( $field ) {
		return "{$this->pluginnow}_{$field}";
	}


	/**
	 * Add a field. It's a wrapper for `add_settings_field()`.
	 *
	 * @since 1.0
	 *
	 * @param (array) $args An array of parameters:
	 *                - (string) $title       The row title/label.
	 *                - (string) $description The row description.
	 *                - (string) $field_type  The field type.
	 *                See `self::field()` for the other paramaters.
	 *
	 * @return (object) The class instance.
	 */
	protected function add_field( $args ) {

		$args = wp_parse_args( $args, array(
			'title'       => '',
			'description' => '',
			'name'        => '',
			'field_type'  => 'field',
		) );

		if ( empty( $args['name'] ) && ! empty( $args['label_for'] ) ) {
			$args['name'] = $args['label_for'];
		}

		// Get the title.
		$title = $args['title'];
		unset( $args['title'] );

		// Get the callback.
		if ( is_array( $args['field_type'] ) ) {
			$callback = $args['field_type'];
		} elseif ( method_exists( $this, $args['field_type'] ) ) {
			$callback = array( $this, $args['field_type'] );
		} else {
			$callback = 'secupress_' . $args['field_type'];
		}

		add_settings_field(
			'module_' . $this->modulenow . '|' . $this->pluginnow . '|' . $args['name'],
			$title,
			$callback,
			$this->get_section_id(),
			$this->get_section_id(),
			$args
		);

		/**
		 * Triggered after a field is added.
		 *
		 * @since 1.0
		 */
		do_action( 'secupress.settings.after_field_' . $this->modulenow . '|' . $this->pluginnow );

		return $this;
	}


	/**
	 * Like the `do_settings_fields()` WordPress function but:
	 * - `id` and `class` attributes can be added to the `tr` tag (the `class` attribute appeared in WP 4.3) with `row_id` and `row_class`.
	 * - The `$depends` parameter can be used to show/hide the row depending on a field value.
	 * - Automatically add some text to the row description if the field is pro and w're not using the pro version.
	 *
	 * @since 1.0
	 *
	 * @param (string) $page    Slug title of the admin page who's settings fields you want to show.
	 * @param (string) $section Slug title of the settings section who's fields you want to show.
	 */
	final protected function do_settings_fields( $page, $section ) {
		global $wp_settings_fields;

		if ( ! isset( $wp_settings_fields[ $page ][ $section ] ) ) {
			return;
		}

		foreach ( (array) $wp_settings_fields[ $page ][ $section ] as $field ) {
			$id       = '';
			$field_id = isset( $field['id'] ) ? explode( '|', $field['id'] ) : array( '' );
			$field_id = end( $field_id );
			$is_pro   = static::is_pro_feature( $field['args']['name'] );
			$class    = 'secupress-setting-row ' . ( $is_pro ? 'secupress-pro-row ' : '' ) . 'secupress-setting-row_' . sanitize_html_class( $field_id ) . ' ';

			// Row ID.
			if ( ! empty( $field['args']['row_id'] ) ) {
				$id = ' id="' . esc_attr( $field['args']['row_id'] ) . '"';
			}

			// Row class.
			if ( ! empty( $field['args']['row_class'] ) ) {
				$class .= $field['args']['row_class'];
			}

			if ( ! empty( $field['args']['depends'] ) ) {
				$field['args']['depends'] = explode( ' ', $field['args']['depends'] );
				$class .= ' depends-' . implode( ' depends-', $field['args']['depends'] );
			}

			if ( $class ) {
				$class = ' class="' . esc_attr( trim( $class ) ) . '"';
			}

			unset( $field['args']['row_id'], $field['args']['row_class'] );
			?>
			<div<?php echo $id . $class; ?>>
				<div class="secupress-flex">
					<div class="secupress-setting-content-col">
					<?php
					// Row title.
					if ( $field['title'] ) {

						if ( ! empty( $field['args']['label_for'] ) ) {
							echo '<h4 id="row-' . sanitize_html_class( $field_id ) . '" class="screen-reader-text">' . $field['title'] . '</h4>';
							echo '<label for="' . esc_attr( $field['args']['label_for'] ) . '" class="secupress-setting-row-title">' . $field['title'] . '</label>';
						} else {
							echo '<h4 id="row-' . sanitize_html_class( $field_id ) . '" class="secupress-setting-row-title">' . $field['title'] . '</h4>';
						}
					}

					if ( $field['args']['description'] ) {
						echo '<p class="description">' . $field['args']['description'] . '</p>';
					}
					unset( $field['args']['description'] );

					$field_is_disabled = $this->field_is_disabled( $field['args'] );

					if ( empty( $field['args']['disabled'] ) && $field_is_disabled ) {
						$field['args']['disabled'] = true;
					}

					if ( $this->section_is_disabled && ! $field_is_disabled ) {
						$this->section_is_disabled = false;
					}

					call_user_func( $field['callback'], $field['args'] );
					?>
					</div>
					<div class="secupress-get-pro-col">
					<?php
					if ( $is_pro ) {
						echo '<p class="secupress-get-pro">' . static::get_pro_version_string() . '</p>';
					}
					?>
					</div><!-- .secupress-get-pro-col -->
				</div><!-- .secupress-flex -->
			</div>
			<?php
		}
	}


	/**
	 * Tell if a field is disabled.
	 *
	 * @since 1.2.1
	 * @author Grégory Viguier
	 *
	 * @param (array) $field_args Field arguments.
	 *
	 * @return (bool)
	 */
	protected function field_is_disabled( $field_args ) {
		static $fields = array();

		if ( isset( $fields[ $field_args['name'] ] ) ) {
			return $fields[ $field_args['name'] ];
		}

		$disabled = false;

		if ( ! empty( $field_args['disabled'] ) ) {
			// The field is disabled.
			$disabled = true;
		} elseif ( static::is_pro_feature( $field_args['name'] ) ) {
			// The field is Pro.
			$disabled = true;
		} elseif ( ! empty( $field_args['options'] ) && ! secupress_is_pro() ) {
			// All the options are Pro.
			$has_enabled = false;

			foreach ( (array) $field_args['options'] as $val => $title ) {
				$name = $field_args['name'] . '_' . $val;

				if ( ! static::is_pro_feature( $field_args['name'] . '|' . $val ) ) {
					$has_enabled = true;
					$fields[ $name ] = false;
				} else {
					$fields[ $name ] = true;
				}
			}

			$disabled = ! $has_enabled;
		}

		if ( ! $disabled && ! empty( $field_args['depends'] ) ) {
			// All dependencies are disabled.
			$has_enabled = false;

			foreach ( (array) $field_args['depends'] as $depend ) {
				if ( empty( $fields[ $depend ] ) ) {
					$has_enabled = true;
					break;
				}
			}

			$disabled = ! $has_enabled;
		}

		if ( $disabled && ! empty( $field_args['options'] ) ) {
			/*
			 * When a field is disabled and has options (checklist or select), mark all the options as disabled.
			 * It will be usefull for the previous dependencies test.
			 */
			foreach ( (array) $field_args['options'] as $val => $title ) {
				$name = $field_args['name'] . '_' . $val;

				if ( ! isset( $fields[ $name ] ) ) {
					$fields[ $name ] = true;
				}
			}
		}

		$fields[ $field_args['name'] ] = $disabled;
		return $disabled;
	}


	/** Main template tags ====================================================================== */

	/**
	 * Print the page content. Must be extended.
	 *
	 * @since 1.0
	 */
	public function print_page() {
		die( 'Method SecuPress_Settings::print_page() must be over-ridden in a sub-class.' );
	}


	/** Other template tags ===================================================================== */

	/**
	 * Print the current section description (because you wouldn't guess by the method's name, be thankful).
	 *
	 * @since 1.0
	 *
	 * @return (object) The class instance.
	 */
	protected function print_section_description() {
		$key = $this->modulenow . '|' . $this->sectionnow;

		if ( ! empty( $this->section_descriptions[ $key ] ) ) {
			echo '<div class="secupress-settings-section-description">';
				echo $this->section_descriptions[ $key ];
			echo '</div>';
		}

		return $this;
	}


	/**
	 * Get or print a submit button.
	 *
	 * @since 1.0
	 *
	 * @param (string)       $type             Optional. The type of button. Accepts 'primary', 'secondary', or 'delete'. Default 'primary large'.
	 * @param (string)       $name             Optional. The HTML name of the submit button. If no id attribute is given in $other_attributes below, `$name` will be used as the button's id. Default 'main_submit'.
	 * @param (bool|string)  $wrap             Optional. True if the output button should be wrapped in a paragraph tag, false otherwise. Can be used as a string to add a class to the wrapper. Default true.
	 * @param (array|string) $other_attributes Optional. Other attributes that should be output with the button, mapping attributes to their values, such as `array( 'tabindex' => '1' )`. These attributes will be output as `attribute="value"`, such as `tabindex="1"`. Other attributes can also be provided as a string such as `tabindex="1"`, though the array format is typically cleaner. Default empty.
	 * @param (bool)         $echo             Optional. True if the button should be "echo"ed, false otherwise.
	 *
	 * @return (string) Submit button HTML.
	 */
	protected static function submit_button( $type = 'primary large', $name = 'main_submit', $wrap = true, $other_attributes = null, $echo = true ) {
		if ( true === $wrap ) {
			$wrap = '<p class="submit">';
		} elseif ( $wrap ) {
			$wrap = '<p class="submit ' . sanitize_html_class( $wrap ) . '">';
		}

		if ( ! is_array( $type ) ) {
			$type = explode( ' ', $type );
		}

		$button_shorthand = array( 'primary' => 1, 'secondary' => 1, 'tertiary' => 1, 'small' => 1, 'large' => 1, 'delete' => 1 );
		$classes          = array( 'secupress-button' );

		foreach ( $type as $t ) {
			$classes[] = isset( $button_shorthand[ $t ] ) ? 'secupress-button-' . $t : $t;
		}
		$class = implode( ' ', array_unique( $classes ) );

		// Default the id attribute to $name unless an id was specifically provided in $other_attributes.
		$id = $name;
		if ( is_array( $other_attributes ) && isset( $other_attributes['id'] ) ) {
			$id = $other_attributes['id'];
			unset( $other_attributes['id'] );
		}

		$attributes = '';
		if ( is_array( $other_attributes ) ) {
			foreach ( $other_attributes as $attribute => $value ) {
				$attributes .= ' ' . $attribute . '="' . esc_attr( $value ) . '"';
			}
		} elseif ( ! empty( $other_attributes ) ) { // Attributes provided as a string.
			$attributes = $other_attributes;
		}

		// Don't output empty name and id attributes.
		$name_attr = $name ? ' name="' . esc_attr( $name ) . '"' : '';
		$id_attr   = $id   ? ' id="' . esc_attr( $id ) . '"'     : '';

		$button = '<button type="submit"' . $name_attr . $id_attr . ' class="' . esc_attr( $class ) . '"' . $attributes . '>' . __( 'Save All Changes', 'secupress' ) . '</button>';

		if ( $wrap ) {
			$button = $wrap . $button . '</p>';
		}

		if ( $echo ) {
			echo $button;
		}

		return $button;
	}


	/**
	 * Print the sidebar with Ads and cross-selling.
	 *
	 * @author Geoffrey Crofte
	 * @since 1.1.4
	 * @since 1.2 A method of this class. Was previously `secupress_print_sideads()`.
	 */
	protected function print_sideads() {
		global $current_screen;

		if ( secupress_is_pro() ) {
			return;
		}

		$rk_offer = '20%';
		$rk_code  = 'SECUPRESS20';
		$rk_url   = 'https://wp-rocket.me/?utm_source=secupress&utm_campaign=sidebar&utm_medium=plugin';

		$im_offer = __( '100MB', 'secupress' );
		$im_url   = 'http://app.imagify.io/p/secupress/?utm_source=secupress&utm_campaign=sidebar&utm_medium=plugin';
		?>

		<div class="secupress-sideads">
			<?php if ( empty( $current_screen ) || 'secupress_page_' . SECUPRESS_PLUGIN_SLUG . '_modules' !== $current_screen->base || empty( $_GET['module'] ) || 'get-pro' !== $_GET['module'] ) { ?>
				<div class="secupress-section-dark secupress-pro-ad">

					<i class="icon-secupress" aria-hidden="true"></i>

					<img src="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-pro.png" srcset="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-pro@2x.png" width="80" height="78" alt="SecuPress Pro"/>

					<p class="secupress-text-medium"><?php _e( 'Improve your Security', 'secupress' ); ?></p>
					<p><?php _e( 'Unlock all the features of SecuPress Pro', 'secupress' ); ?></p>
					<a href="<?php echo esc_url( secupress_admin_url( 'get_pro' ) ); ?>" class="secupress-button secupress-button-tertiary secupress-button-getpro">
						<span class="icon">
							<i class="icon-secupress-simple" aria-hidden="true"></i>
						</span>
						<span class="text"><?php _ex( 'Get Pro', 'short', 'secupress' ); ?></span>
					</a>
				</div>
			<?php } ?>

			<div class="secupress-bordered secupress-mail-ad">
				<div class="secupress-ad-header secupress-flex">
					<span><i class="dashicons dashicons-email secupress-primary" aria-hidden="true"></i></span>
					<p><?php _e( 'Join our mailing list', 'secupress' ); ?></p>
				</div>
				<div class="secupress-ad-content">
					<p><label for="mce-EMAIL"><?php _e( 'Get security alerts and news from SecuPress.', 'secupress' ) ?></label></p>

					<form action="https://secupress.us13.list-manage.com/subscribe/post?u=67a6053e2542ab4330a851904&amp;id=2eecd4aed8" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank" novalidate>
						<p>
							<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="<?php esc_attr_e( 'Email address', 'secupress' ); ?>" required="required"/>
						</p>

						<!-- Real people should not fill this in and expect good things - do not remove this or risk form bot signups. -->
						<div style="position:absolute;left:-9999em" aria-hidden="true"><input type="text" name="b_67a6053e2542ab4330a851904_2eecd4aed8" tabindex="-1" value=""/></div>

						<p>
							<button type="submit" name="subscribe" class="secupress-button secupress-button-primary"><?php _e( 'Stay tuned for more', 'secupress' ); ?></button>
						</p>
					</form>
				</div>
			</div>

			<?php if ( ! defined( 'WP_ROCKET_VERSION' ) ) { ?>

			<div class="secupress-wprocket-ad secupress-product-ads">
				<img src="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-wprocket.png" srcset="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-wprocket@2x.png 2x" alt="WP Rocket" width="110" height="30"/>

				<p class="secupress-catch"><?php _e( 'Speed up your website with WP Rocket', 'secupress' ); ?></p>
				<p><?php printf( __( 'Get <span>%1$s OFF</span> with this coupon code: %2$s', 'secupress' ), $rk_offer, '<span class="secupress-coupon">' . $rk_code . '</span>' ); ?></p>

				<p class="secupress-cta">
					<a href="<?php echo esc_url( $rk_url ); ?>" class="secupress-button" target="_blank"><?php printf( __( 'Get %s OFF', 'secupress' ), $rk_offer ); ?></a>
				</p>
			</div>

			<?php } ?>

			<?php if ( ! defined( 'IMAGIFY_VERSION' ) ) { ?>

			<div class="secupress-imagify-ad secupress-product-ads">
				<img src="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-imagify.png" srcset="<?php echo SECUPRESS_ADMIN_IMAGES_URL; ?>logo-imagify@2x.png 2x" alt="Imagify" width="123" height="15"/>

				<p class="secupress-catch"><?php _e( 'Speed Up your website with lighter images', 'secupress' ); ?></p>
				<p><?php printf( __( 'For each new account, get <span>%s Free</span>.', 'secupress' ), $im_offer ); ?></p>

				<p class="secupress-cta">
					<a href="<?php echo esc_url( $im_url ); ?>" class="secupress-button" target="_blank"><?php printf( __( 'Get %s Free', 'secupress' ), $im_offer ); ?></a>
				</p>
			</div>

			<?php } ?>

		</div>
		<?php
	}


	/** Utilities =============================================================================== */

	/**
	 * Tell if the option value is for the pro version and we're not using the pro version.
	 *
	 * @since 1.0
	 * @since 1.3 The method is public.
	 *
	 * @param (string) $value The option value.
	 *
	 * @return (bool) True if the option value is for pro version but w're not using the pro version.
	 */
	public static function is_pro_feature( $value ) {
		return secupress_feature_is_pro( $value ) && ! secupress_is_pro();
	}


	/**
	 * Returns a i18n message to act like a CTA on pro version.
	 *
	 * @since 1.0
	 * @since 1.3 The method is public.
	 *
	 * @param (string) $format You can use it to embed the message in a HTML tag, usage of "%s" is mandatory.
	 *
	 * @return (string)
	 */
	public static function get_pro_version_string( $format = '' ) {
		$message = sprintf( __( 'Available in <a href="%s" target="_blank">Pro Version</a>', 'secupress' ), esc_url( secupress_admin_url( 'get_pro' ) ) );
		if ( $format ) {
			$message = sprintf( $format, $message );
		}
		return $message;
	}


	/** Includes ================================================================================ */

	/**
	 * Include a module settings file. Also, automatically set the current module and print the sections.
	 *
	 * @since 1.0
	 *
	 * @param (string) $module_file Absolute path to the module settings file.
	 * @param (string) $module      The module.
	 *
	 * @return (object) The class instance.
	 */
	final protected function require_settings_file( $module_file, $module ) {

		if ( file_exists( $module_file ) ) {
			$this->set_current_plugin( $module );

			require_once( $module_file );

			$this->do_sections();
		}

		return $this;
	}
}
