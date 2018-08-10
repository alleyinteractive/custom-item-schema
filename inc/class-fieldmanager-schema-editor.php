<?php
/**
 * Schema_Editor class
 *
 * @package Custom_Item_Schema
 */

namespace Custom_Item_Schema;
use Fieldmanager_TextArea;

if ( ! class_exists( 'Fieldmanager_TextArea' ) ) {
	return;
}

/**
 * CodeMirror-enabled textarea for entering Schema.org data.
 */
class Fieldmanager_Schema_Editor extends \Fieldmanager_TextArea {
	/**
	 * Set up.
	 *
	 * @param string $label  Field label.
	 * @param array  $options Field options.
	 */
	public function __construct( string $label = '', array $options = [] ) {
		parent::__construct( $label, array_merge(
			[
				'default_value' => '[ { "@context": "http://schema.org" } ]',
				'attributes'    => [
					'style' => 'width: 100%; height: 400px;',
				],
			],
			$options
		) );

		// Preserves tabs.
		$this->sanitize = 'sanitize_textarea_field';
	}

	/**
	 * Render the form element.
	 *
	 * @param string $value Field value.
	 * @return string Form element HTML.
	 */
	public function form_element( $value = '' ) {
		$settings = wp_enqueue_code_editor( [
			'type' => 'application/ld+json',
		] );

		if ( $settings ) {
			/*
			 * - `autoCloseBrackets` is inserting an extra double-quote.
			 * - The hints are irrelevant; they're about the JS environment.
			 */
			wp_add_inline_script(
				'code-editor',
				sprintf(
					'jQuery( function() {
						var args = %s;
						args.codemirror.autoCloseBrackets = false;
						args.codemirror.hintOptions = {
							hint: jQuery.noop,
						};
						wp.codeEditor.initialize( %s, args );
					} );',
					wp_json_encode( $settings ),
					wp_json_encode( $this->get_element_id() )
				)
			);
		}

		/*
		 * A blank value means we specifically saved it. @see Schema_Editor::presave_alter_values().
		 * However, present the default so the user always gets a boost when they do start editing.
		 */
		if ( ! $value ) {
			$value = $this->default_value;
		}

		return parent::form_element( $this->format_for_form( (string) $value ) );
	}

	/**
	 * Hook to alter or respond to all the values of a particular element.
	 *
	 * @param  array $values         The new values.
	 * @param  array $current_values The current values.
	 * @return array The filtered values.
	 */
	protected function presave_alter_values( $values, $current_values = array() ) {
		if ( isset( $values[0] ) && json_decode( $values[0], true ) === json_decode( $this->default_value, true ) ) {
			// No point in saving the default.
			$values[0] = null;
		}

		return parent::presave_alter_values( $values, $current_values );
	}

	/**
	 * Format a string of JSON for display in the editor.
	 *
	 * @param string $raw The unformatted JSON.
	 * @return string The formatted JSON.
	 */
	protected function format_for_form( string $raw ) {
		$decoded = json_decode( $raw, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			return $raw;
		}

		return wp_json_encode( $decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}
}
