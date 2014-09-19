<?php

class acf_field_taxonomies extends acf_field {

	// vars
	var $settings, // will hold info such as dir / path
		$defaults; // will hold default field options


	/*
	*  __construct
	*
	*  Set name / label needed for actions / filters
	*
	*  @since	3.6
	*  @date	23/01/13
	*/

	function __construct()
	{
		// vars
		$this->name = 'taxonomies';
		$this->label = __('Taxonomies');
		$this->category = __("Relational",'acf'); // Basic, Content, Choice, etc
		$this->defaults = array(
			'post_type' => 'post',
			);


		// do not delete!
		parent::__construct();


		// settings
		$this->settings = array(
			'path' => apply_filters('acf/helpers/get_path', __FILE__),
			'dir' => apply_filters('acf/helpers/get_dir', __FILE__),
			'version' => '1.0.0'
			);

	}


	/*
	*  create_options()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like below) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/

	function create_options( $field )
	{
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// key is needed in the field names to correctly save the data
		$key = $field['name'];


		// Create Field Options HTML
		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Post Type",'acf'); ?></label>
			</td>
			<td>
				<?php
				$post_types = get_post_types(array('public' => true));
				do_action('acf/create_field', array(
					'type'		=>	'select',
					'name'		=>	'fields['.$key.'][post_type]',
					'value'		=>	$field['post_type'],
					'choices'	=>	$post_types,
					)
				);
				?>
			</td>
		</tr>
		<?php
	}


	/*
	*  create_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function create_field( $field ) {
		// defaults?
		/*
		$field = array_merge($this->defaults, $field);
		*/

		// perhaps use $field['preview_size'] to alter the markup?


		// create Field HTML
		// vars
		$single_name = $field['name'];

		if( !is_array($field['value']) ) {
			$field['value'] = array( $field['value'] );
		}
		?>
		<div class="acf_relationship" data-post_type= "<?php echo $field['post_type']; ?>">
			<input type="hidden" name="<?php echo $field['name']; ?>" value="" />
			<div class="relationship_left">
				<table class="widefat">
				</table>
				<ul class="bl relationship_list">
					<?php
					$taxes = get_object_taxonomies( $field['post_type'] );
					if (($key = array_search('material', $taxes)) !== false) {
						unset($taxes[$key]);
					}
					$values = $field['value'];
					foreach ($taxes as $tax) {
						$selected = in_array( $tax, $values );
						echo '<li ' . ($selected ? 'class="hide"' : '') . '><a href="#" class="" data-post_id="' . $tax . '">' . $tax . '<span class="acf-button-add"></span></a></li>';}
						?>
					</ul>
				</div>
				<!-- /Left List -->

				<!-- Right List -->
				<div class="relationship_right">
					<ul class="bl relationship_list">
						<?php if( $field['value'] ) {
							foreach( $field['value'] as $p ) {
								$title = '<span class="relationship-item-info">';
								if( defined('ICL_LANGUAGE_CODE') ){
									$title .= ' (' . ICL_LANGUAGE_CODE . ')';
								}
								$title .= '</span>';
								$title .= $p;
								echo '<li><a href="#" class="" data-post_id="' . $p . '">' . $title . '<span class="acf-button-remove"></span></a><input type="hidden" name="' . $single_name . '[]" value="' . $p . '" /></li>';
							}
						}
						?>
					</ul>
				</div>
				<!-- / Right List -->

			</div>
			<?php

		}


	/*
	*  input_admin_enqueue_scripts()
	*
	*  This action is called in the admin_enqueue_scripts action on the edit screen where your field is created.
	*  Use this action to add CSS + JavaScript to assist your create_field() action.
	*
	*  $info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_enqueue_scripts
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/

	function input_admin_enqueue_scripts()
	{
		// Note: This function can be removed if not used


		// register ACF scripts
		wp_register_script( 'acf-input-taxonomies', $this->settings['dir'] . 'js/input.js', array('acf-input'), $this->settings['version'] );
		wp_register_style( 'acf-input-taxonomies', $this->settings['dir'] . 'css/input.css', array('acf-input'), $this->settings['version'] );


		// scripts
		wp_enqueue_script(array(
			'acf-input-taxonomies',
			));

		// styles
		wp_enqueue_style(array(
			'acf-input-taxonomies',
			));
	}
}


// create field
new acf_field_taxonomies();

?>
