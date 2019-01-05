/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { select, withSelect } from '@wordpress/data';
import edit from './edit';

const name = 'advanced-archive-blocks/archives';
registerBlockType(
	name,
	{
		title: __( 'Advanced Archive Blocks', 'advanced-archive-blocks' ),

		icon: 'admin-post',

		category: 'widgets',

		supports: {
			align: [ 'wide', 'full' ],
			html: false,
		},

		edit: withSelect( ( _, props ) => {
			const { attributes } = props;
			const { postType } = attributes;
			const { getPostTypes } = select( 'core' );
			const postTypes = getPostTypes() || [];
			return {
				postType,
				postTypes: postTypes
					.filter( ( { slug } ) => slug !== 'attachment' && slug !== 'page' )
					.filter( ( { viewable } ) => viewable ),
			};
		} )( edit ),

		save() {
			return null;
		},
	}
);
