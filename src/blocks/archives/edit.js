/**
 * WordPress dependencies
 */
import { Fragment } from '@wordpress/element';
import {
	PanelBody,
	ToggleControl,
	Disabled,
	SelectControl,
} from '@wordpress/components';

import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import {
	InspectorControls,
	BlockAlignmentToolbar,
	BlockControls,
	ServerSideRender,
} from '@wordpress/editor';

export default function ArchivesEdit( { attributes, setAttributes, postTypes } ) {
	const { align, showPostCounts, displayAsDropdown, postType } = attributes;
	return (
		<Fragment>
			<InspectorControls>
				<PanelBody title={ __( 'Archives Settings' ) }>
					<ToggleControl
						label={ __( 'Display as Dropdown' ) }
						checked={ displayAsDropdown }
						onChange={ () => setAttributes( { displayAsDropdown: ! displayAsDropdown } ) }
					/>
					<ToggleControl
						label={ __( 'Show Post Counts' ) }
						checked={ showPostCounts }
						onChange={ () => setAttributes( { showPostCounts: ! showPostCounts } ) }
					/>
					<SelectControl
						label={ __( 'Post Type' ) }
						value={ undefined !== postType ? postType : 'post' }
						// `undefined` is required for the postType attribute to be unset.
						onChange={ ( value ) => setAttributes( { postType: value } ) }
						options={
							postTypes.map( ( type ) => ( { value: type.slug, label: type.name } ) )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<BlockControls>
				<BlockAlignmentToolbar
					value={ align }
					onChange={ ( nextAlign ) => {
						setAttributes( { align: nextAlign } );
					} }
					controls={ [ 'left', 'center', 'right' ] }
				/>
			</BlockControls>
			<Disabled>
				<ServerSideRender block="advanced-archive-blocks/archives" attributes={ attributes } />
			</Disabled>
		</Fragment>
	);
}
