/**
 * External dependencies
 */
import currencyToSymbolMap from 'currency-symbol-map/map';

/**
 * WordPress dependencies.
 */
const { __ } = wp.i18n;

const {
	Button,
	ExternalLink,
	PanelBody
} = wp.components;

const { createHigherOrderComponent } = wp.compose;

const { InspectorControls } = wp.blockEditor;

const { Fragment } = wp.element;

const { select } = wp.data;

const { addFilter } = wp.hooks; 

const withInspectorControls = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { setAttributes } = props;

		const { getEditedPostAttribute } = select( 'core/editor' );

		const metaValues = getEditedPostAttribute( 'meta' );

		const importFromWPPR = () => {
			const attrs = {};
	
			if ( metaValues['cwp_rev_product_name']) {
				attrs.title = metaValues['cwp_rev_product_name'];
			}
	
			if ( metaValues['cwp_rev_product_image']) {
				attrs.image = {
					id: 0,
					alt: '',
					url: metaValues['cwp_rev_product_image']
				};
			}
	
			if ( metaValues['wppr_links'] && 0 < Object.keys( metaValues['wppr_links']).length ) {
				attrs.links = Object.keys( metaValues['wppr_links']).map( link => ({
					label: link,
					href: metaValues['wppr_links'][ link ]
				}) );
			}
	
			if ( metaValues['cwp_rev_price']) {
				const reg = /[0-9.,]/g;
				let currency = metaValues['cwp_rev_price'].replace( reg, '' );
	
				if ( '' !== currency ) {
					attrs.price = Number( metaValues['cwp_rev_price'].replace( currency, '' ) );
	
					if ( '$' === currency ) {
						currency = 'USD';
					} else if ( '£' === currency ) {
						currency = 'GBP';
					} else if ( '€' === currency ) {
						currency = 'EUR';
					} else {
						currency = Object.keys( currencyToSymbolMap ).find( key => currency === currencyToSymbolMap[ key ]);
					}
	
					attrs.currency = currency;
				} else {
					attrs.price = Number( metaValues['cwp_rev_price']);
				}
			}
	
			if ( metaValues['wppr_options'] && 0 < metaValues['wppr_options'].length ) {
				attrs.features = metaValues['wppr_options'].map( i => ({
					title: i.name || '',
					rating: Math.round( Number( i.value ) / 10 )
				}) );
			}
	
			if ( metaValues['wppr_pros'] && 0 < metaValues['wppr_pros'].length ) {
				attrs.pros = metaValues['wppr_pros'];
			}
	
			if ( metaValues['wppr_cons'] && 0 < metaValues['wppr_cons'].length ) {
				attrs.cons = metaValues['wppr_cons'];
			}
	
			setAttributes({ ...attrs });
	
			return window.wpprToggleReviewStatus();
		};

		const isReviewBlock = 'themeisle-blocks/review' === props.name && ( metaValues['cwp_meta_box_check'] && 'Yes' === metaValues['cwp_meta_box_check'] ) ;

		if ( isReviewBlock && props.isSelected && Boolean( wpprguten.showMigrationNotice ) ) {
			return (
				<Fragment>
					<InspectorControls>
						<PanelBody
							title={ __( 'Migrate from WP Product Review' ) }
						>
							<p>{ __( 'A prior review made with WP Product Review exists on this post. Would you like to import the data?' ) }</p>

							<Button
								isPrimary
								onClick={ importFromWPPR }
							>
								{ __( 'Import data' ) }
							</Button>

							<Button
								isTertiary
								onClick={ window.wpprDisableMigrationNotice }
							>
								{ __( 'Dismiss notice' ) }
							</Button>

							<br/><br/>

							<ExternalLink href="https://docs.themeisle.com/article/1360-migrating-from-wp-product-review-to-otters-review-block">
								{ __( 'Learn more' ) }
							</ExternalLink>
						</PanelBody>
					</InspectorControls>

					<BlockEdit { ...props } />
				</Fragment>
			);
		}

		return <BlockEdit { ...props } />;
	};
}, 'withInspectorControl' );

addFilter( 'editor.BlockEdit', 'wp-product-review/with-inspector-controls', withInspectorControls );
