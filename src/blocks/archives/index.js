/**
 * External dependencies
 */
import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import { select, subscribe, withSelect } from '@wordpress/data';
import edit from './edit';

let postTypes = [];
const registerPostBlockType = () => {
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
				return {
					postType,
					postTypes: getPostTypes()
						.filter( type => type.viewable )
						.filter( type => type.rest_base !== 'media' ),
				};
			} )( edit ),

			save() {
				return null;
			},
		}
	);
};

const unsubscribe = subscribe( () => {
	postTypes = select( 'core' ).getPostTypes();
	if ( postTypes ) {
		unsubscribe();
		registerPostBlockType();
	}
} );
