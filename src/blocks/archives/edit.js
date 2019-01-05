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
	const { align, showPostCounts, displayAsDropdown, postType, type } = attributes;
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
						label={ __( 'Type', 'advanced-archive-blocks' ) }
						value={ undefined !== type ? type : 'monthly' }
						// `undefined` is required for the postType attribute to be unset.
						onChange={ ( value ) => setAttributes( { type: value } ) }
						options={
							[
								{
									value: 'yearly',
									label: __( 'Yearly', 'advanced-archive-blocks' ),
								},
								{
									value: 'monthly',
									label: __( 'Monthly', 'advanced-archive-blocks' ),
								},
								{
									value: 'daily',
									label: __( 'Daily', 'advanced-archive-blocks' ),
								},
								{
									value: 'weekly',
									label: __( 'Weekly', 'advanced-archive-blocks' ),
								},
							]
						}
					/>
					<SelectControl
						label={ __( 'Post Type', 'advanced-archive-blocks' ) }
						value={ undefined !== postType ? postType : 'post' }
						// `undefined` is required for the postType attribute to be unset.
						onChange={ ( value ) => setAttributes( { postType: value } ) }
						options={
							postTypes.map( ( { slug, name } ) => ( { value: slug, label: name } ) )
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
