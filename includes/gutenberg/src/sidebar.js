/**
 * External dependencies
 */
import currencyToSymbolMap from 'currency-symbol-map/map';
 
/**
 * Internal dependencies
 */
import './style.scss';
import { reverseObject, renameKey } from './utils';
import RadioImageControl from './radio-image-control/';
import  './migration.js';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const  { isUndefined, pickBy } = lodash;

const { registerPlugin } = wp.plugins;

const {
	createBlock,
	getBlockType
} = wp.blocks;

const { MediaUpload } = wp.blockEditor;

const {
	select,
	dispatch,
	withSelect,
	withDispatch,
} = wp.data;

const {
	PluginPostStatusInfo,
	PluginSidebarMoreMenuItem,
	PluginSidebar,
} = wp.editPost;

const {
	Component,
	Fragment,
} = wp.element;

const {
	withState,
	compose,
 } = wp.compose;

const {
	Button,
	FormToggle,
	Modal,
	PanelBody,
	SelectControl,
	TextControl,
	ExternalLink,
} = wp.components;

class WP_Product_Review extends Component {
	constructor() {
		super( ...arguments );

		this.toggleReviewStatus = this.toggleReviewStatus.bind( this );
		this.onChangeTemplate = this.onChangeTemplate.bind( this );
		this.onChangeReviewTitle = this.onChangeReviewTitle.bind( this );
		this.onChangeReviewImage = this.onChangeReviewImage.bind( this );
		this.onChangeImageLink = this.onChangeImageLink.bind( this );
		this.onChangeReviewAffiliateTitle = this.onChangeReviewAffiliateTitle.bind( this );
		this.onChangeReviewAffiliateLink = this.onChangeReviewAffiliateLink.bind( this );
		this.addButton = this.addButton.bind( this );
		this.onChangeReviewPrice = this.onChangeReviewPrice.bind( this );
		this.onChangeOptionText = this.onChangeOptionText.bind( this );
		this.onChangeOptionNumber = this.onChangeOptionNumber.bind( this );
		this.onChangeSchemaType = this.onChangeSchemaType.bind( this );
		this.onChangeSchemaField = this.onChangeSchemaField.bind( this );
		this.addOption = this.addOption.bind( this );
		this.onChangeProText = this.onChangeProText.bind( this );
		this.addPro = this.addPro.bind( this );
		this.onChangeConText = this.onChangeConText.bind( this );
		this.addCon = this.addCon.bind( this );
		this.importReview = this.importReview.bind( this );
		this.migrateToReviewBlock = this.migrateToReviewBlock.bind( this );
		this.disableMigrationNotice = this.disableMigrationNotice.bind( this );

		window.wpprToggleReviewStatus = this.toggleReviewStatus;
		window.wpprDisableMigrationNotice = this.disableMigrationNotice;

		this.state = {
			cwp_meta_box_check: 'No',
			cwp_rev_product_name: '',
			_wppr_review_template: 'default',
			cwp_rev_product_image: '',
			cwp_image_link: 'image',
			wppr_links: {
				'': '',
			},
			cwp_rev_price: '',
			wppr_options: {
				1: {
					name: '',
					value: 0,
				}
			},
			wppr_pros: {
				0: '',
			},
			wppr_cons: {
				0: '',
			},
            wppr_review_type: 'Product',
            wppr_review_custom_fields: {},
            schema_fields: {},
            schema_url: ''
		};
	}

	async componentDidMount() {
		const {
			getCurrentPostId,
			getCurrentPostType,
		} = select( 'core/editor' );

		const post = await select( 'core' ).getEntityRecord( 'postType', getCurrentPostType(), getCurrentPostId() );

		if ( undefined !== post && post.wppr_data ) {
			if ( post.wppr_data.wppr_links && post.wppr_data.wppr_links.length < 1 ) {
				post.wppr_data.wppr_links[''] = '';
			}
			this.setState( { ...post.wppr_data } );
	        
            const data = await wp.apiRequest( { path: `/wppr/v1/schema-fields?type=${this.state.wppr_review_type}` } );
            this.setState( { schema_fields: data.fields, schema_url: data.url } );
        }
	}

	static getDerivedStateFromProps( nextProps, state ) {
		if ( ( nextProps.isPublishing || nextProps.isSaving ) && !nextProps.isAutoSaving ) {
			wp.apiRequest( { path: `/wppr/v1/update-review?id=${nextProps.postId}&postType=${nextProps.postType}`, method: 'POST', data: state } ).then(
				( data ) => {
					return data;
				},
				( err ) => {
					return err;
				}
			);
		}
	}

	componentDidUpdate( prevProps, prevState ) {
		if ( this.state.cwp_meta_box_check !== prevState.cwp_meta_box_check && this.state.cwp_meta_box_check === 'Yes' ) {
			this.props.openReviewSidebar();
		}
	}

	toggleReviewStatus() {
		this.setState( { cwp_meta_box_check: this.state.cwp_meta_box_check === 'Yes' ? 'No' : 'Yes' } );
		this.props.editPostStatus( {
			edited: true,
			meta: {
				'cwp_meta_box_check': this.state.cwp_meta_box_check === 'Yes' ? 'No' : 'Yes'
			}
		} );
	}

	onChangeTemplate( value ) {
		this.setState( { _wppr_review_template: value } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeReviewTitle( value ) {
		this.setState( { cwp_rev_product_name: value } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeReviewImage( value ) {
		if ( value.url !== undefined && value.url !== '' ) {
			this.setState( { cwp_rev_product_image: value.url } );
		} else if ( value.id !== undefined ){
			this.setState( { cwp_rev_product_image: value.id } );
		}
		this.props.editPostStatus( { edited: true } );
	}

	onChangeImageLink( value ) {
		this.setState( { cwp_image_link: value } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeReviewAffiliateTitle( e, key ) {
		let wppr_links = { ...this.state.wppr_links };
		if ( ( Object.keys( this.state.wppr_links ).length === 2 ) ) {
			if ( e === Object.keys( wppr_links )[0] || e === Object.keys( wppr_links )[1] ) {
				e = e + ' ';
			}
		}
		if ( Object.keys( wppr_links )[0] === key ) {
			renameKey( wppr_links, key, e );
			wppr_links = reverseObject( wppr_links );
		} else {
			renameKey( wppr_links, key, e );
		}
		this.setState( { wppr_links } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeReviewAffiliateLink( e, key ) {
		const wppr_links = { ...this.state.wppr_links };
		wppr_links[key] = e;
		this.setState( { wppr_links } );
		this.props.editPostStatus( { edited: true } );
	}

	addButton() {
		const wppr_links = { ...this.state.wppr_links };
		wppr_links['Buy Now'] = '';
		this.setState( { wppr_links } );
	};

	onChangeReviewPrice( value ) {
		this.setState( { cwp_rev_price: value } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeOptionText( e, key ) {
		const wppr_options = { ...this.state.wppr_options };
		wppr_options[key]['name'] = e;
		this.setState( { wppr_options } );
		this.props.editPostStatus( { edited: true } );
	}

	onChangeOptionNumber( e, key ) {
		const wppr_options = { ...this.state.wppr_options };
		if ( e === '' ) e = 0;
		wppr_options[key]['value'] = e;
		this.setState( { wppr_options } );
		this.props.editPostStatus( { edited: true } );
	}

    onChangeSchemaType( e, key ) {
		if ( e === '' ) return;

        // remove existing fields first.
		this.setState( { schema_fields: {}, schema_url: '' } );

        wp.apiRequest( { path: `/wppr/v1/schema-fields?type=${e}` } ).then(
            ( data ) => {
                this.setState( { schema_fields: data.fields, schema_url: data.url, wppr_review_type: e } );
		        this.props.editPostStatus( { edited: true } );
            },
            ( err ) => {
                return err;
            }
        );
    }

	onChangeSchemaField( e, field ) {
		const fields = { ...this.state.wppr_review_custom_fields };
		const schema_fields = { ...this.state.schema_fields };
        fields[field] = e;
		this.setState( { wppr_review_custom_fields: fields } );
		this.props.editPostStatus( { edited: true } );
	}

	addOption() {
		const key = Object.keys( this.state.wppr_options ).length + 1;
		const wppr_options = { ...this.state.wppr_options };
		wppr_options[key] = {
			name: '',
			value: 0,
		};
		this.setState( { wppr_options } );
	};

	onChangeProText( e, key ) {
		const wppr_pros = { ...this.state.wppr_pros };
		wppr_pros[key] = e;
		this.setState( { wppr_pros } );
		this.props.editPostStatus( { edited: true } );
	}

	addPro() {
		const key = Object.keys( this.state.wppr_pros ).length;
		const wppr_pros = { ...this.state.wppr_pros };
		wppr_pros[key] = '';
		this.setState( { wppr_pros } );
	};

	onChangeConText( e, key ) {
		const wppr_cons = { ...this.state.wppr_cons };
		wppr_cons[key] = e;
		this.setState( { wppr_cons } );
		this.props.editPostStatus( { edited: true } );
	}

	addCon() {
		const key = Object.keys( this.state.wppr_cons ).length;
		const wppr_cons = { ...this.state.wppr_cons };
		wppr_cons[key] = '';
		this.setState( { wppr_cons } );
	};

	importReview( key ) {
		this.setState( {
			wppr_options: this.props.posts[key].wppr_data.wppr_options,
			wppr_pros: this.props.posts[key].wppr_data.wppr_pros,
			wppr_cons: this.props.posts[key].wppr_data.wppr_cons
		} );
		this.props.editPostStatus( { edited: true } );
		this.props.setState( { isOpen: false } );
	};

	migrateToReviewBlock() {
		const attrs = {};

		if ( this.state.cwp_rev_product_name ) {
			attrs.title = this.state.cwp_rev_product_name;
		}

		if ( this.state.cwp_rev_product_image ) {
			attrs.image = {
				id: 0,
				alt: '',
				url: this.state.cwp_rev_product_image
			};
		}

		if ( this.state.wppr_links && 0 < Object.keys( this.state.wppr_links).length ) {
			attrs.links = Object.keys( this.state.wppr_links).map( link => ({
				label: link,
				href: this.state.wppr_links[ link ]
			}) );
		}

		if ( this.state.cwp_rev_price ) {
			const reg = /[0-9.,]/g;
			let currency = this.state.cwp_rev_price.replace( reg, '' );

			if ( '' !== currency ) {
				attrs.price = Number( this.state.cwp_rev_price.replace( currency, '' ) );

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
				attrs.price = Number( this.state.cwp_rev_price );
			}
		}

		if ( this.state.wppr_options && 0 < this.state.wppr_options.length ) {
			attrs.features = this.state.wppr_options.map( i => ({
				title: i.name || '',
				rating: Math.round( Number( i.value ) / 10 )
			}) );
		}

		if ( this.state.wppr_pros && 0 < this.state.wppr_pros.length ) {
			attrs.pros = this.state.wppr_pros;
		}

		if ( this.state.wppr_cons && 0 < this.state.wppr_cons.length ) {
			attrs.cons = this.state.wppr_cons;
		}

		dispatch( 'core/block-editor' ).insertBlock(
			createBlock( 'themeisle-blocks/review', { ...attrs } )
		);

		dispatch( 'core/notices' ).createNotice(
			'info',
			__( 'Migrated to Review Block' ),
			{
				isDismissible: true,
				type: 'snackbar'
			}
		);

		this.toggleReviewStatus();
	};

	disableMigrationNotice() {
		const model = new wp.api.models.Settings({
			// eslint-disable-next-line camelcase
			'cwppos_options_migration': true
		});

		const save = model.save();

		save.success( () => {
			window.wpprguten.showMigrationNotice = false;
			this.props.setState({ showMigration: false });
		});

		save.error( ( response, status ) => {
			console.warning( response.responseJSON.message );
		});
	}

	render() {
		return (
			<Fragment>
				<PluginPostStatusInfo>
					<label htmlFor='is-this-a-review'>{ __( 'Is this post a review?' ) }</label>
					<FormToggle
						checked={ this.state.cwp_meta_box_check === 'Yes' ? true : false }
						onChange={ this.toggleReviewStatus }
						id='is-this-a-review'
					/>
				</PluginPostStatusInfo>
				{ ( this.state.cwp_meta_box_check === 'Yes' ) && [
					<PluginSidebarMoreMenuItem
						target="wp-product-review"
					>
						{ __( 'WP Product Review' ) }
					</PluginSidebarMoreMenuItem>,
					<PluginSidebar
						name="wp-product-review"
						title={ __( 'WP Product Review' ) }
					>
						{ this.props.showMigration && (
							<PanelBody
								title={ __( 'Migrate to Otter\'s Review Block' ) }
							>
								<p>{ __( 'WP Product Review is not being maintained anymore. You can migrate your data to Otter\'s Review Block and keep most of the functionality and continue receiving updates.' ) }</p>

								{ getBlockType( 'themeisle-blocks/review' ) && (
									<Button
										isPrimary
										onClick={ this.migrateToReviewBlock }
									>
										{ __( 'Migrate to Block' ) }
									</Button>
								) }

								{ ( ! getBlockType( 'themeisle-blocks/review' ) && getBlockType( 'themeisle-blocks/advanced-columns' ) ) && (
									<Button
										isPrimary
										href={ window.wpprguten.installOtter }
										target="_blank"
									>
										{ __( 'Update' ) }
									</Button>
								) }

								{ ! getBlockType( 'themeisle-blocks/advanced-columns' ) && (
									<Button
										isPrimary
										href={ window.wpprguten.installOtter }
										target="_blank"
									>
										{ __( 'Install & Activate' ) }
									</Button>
								) }

								<Button
									isTertiary
									onClick={ this.disableMigrationNotice }
								>
									{ __( 'Dismiss notice' ) }
								</Button>

								<br/><br/>

								<ExternalLink href="https://docs.themeisle.com/article/1360-migrating-from-wp-product-review-to-otters-review-block">
									{ __( 'Learn more' ) }
								</ExternalLink>
							</PanelBody>
						) }

						<PanelBody
							title={ __( 'Product Details' ) }
							className="wp-product-review-product-details"
							initialOpen={ true }
							>
							{ ( wpprguten.isPro ) && (
								<RadioImageControl
									label={ __( 'Review Template' ) }
									selected={ this.state._wppr_review_template }
									options={ [
										{
											label: __( 'Default' ),
											src: wpprguten.path + '/assets/img/templates/default.png',
											value: 'default',
										},
										{
											label: __( 'Style 1' ),
											src: wpprguten.path + '/assets/img/templates/style1.png',
											value: 'style1',
										},
										{
											label: __( 'Style 2' ),
											src: wpprguten.path + '/assets/img/templates/style2.png',
											value: 'style2',
										},
									] }
									onChange={ this.onChangeTemplate }
								/>
							) }
							{ ( this.props.postType !== 'wppr_review' ) && [
								<TextControl
									label={ __( 'Product Name' ) }
									type="text"
									value={ this.state.cwp_rev_product_name }
									onChange={ this.onChangeReviewTitle }
								/>
							] }
							<div className="wp-product-review-sidebar-base-control">
								<label className="blocks-base-control__label" for="inspector-media-upload">{ __( 'Product Image' ) }</label>
								<MediaUpload
									type="image"
									id="inspector-media-upload"
									value={ this.state.cwp_rev_product_image }
									onSelect={ this.onChangeReviewImage }
									render={ ( { open } ) => [
										( this.state.cwp_rev_product_image !== '' ) && [
											<img
												onClick={ open }
												src={ this.state.cwp_rev_product_image }
												alt={ __( 'Review image' ) }
											/>,
											<Button
												isLarge
												onClick={ () => this.setState( { cwp_rev_product_image: '' } ) }
												style={ { marginTop: '10px' } }
											>
												{ __( 'Remove Image' ) }
											</Button>
										],
										<Button
											isLarge
											onClick={ open }
											style={ { marginTop: '10px' } }
											className={ ( this.state.cwp_rev_product_image === '' ) && 'wppr_image_upload' }
										>
											{ __( 'Choose or Upload an Image' ) }
										</Button>
									] }
								/>
							</div>
							<SelectControl
								label={ __( 'Product Image Click' ) }
								value={ this.state.cwp_image_link }
								options={ [
									{
										label: __( 'Show Whole Image' ),
										value: 'image',
									},
									{
										label: __( 'Open Affiliate Link' ),
										value: 'link',
									},
								] }
								onChange={ this.onChangeImageLink }
							/>
							<div className="wppr-review-links-list">
							{ Object.keys( this.state.wppr_links ).map( ( key ) => (
								<Fragment>
									<TextControl
										label={ __( 'Affiliate Button Text' ) }
										type="text"
										value={ key != 1 ? key : '' }
										onChange={ ( e ) => this.onChangeReviewAffiliateTitle( e, key ) }
									/>

									<TextControl
										label={ __( 'Affiliate Button Link' ) }
										type="url"
										value={ this.state.wppr_links[key] }
										onChange={ ( e ) => this.onChangeReviewAffiliateLink( e, key ) }
									/>
								</Fragment>
							) ) }
							{ ( Object.keys( this.state.wppr_links ).length < 2 ) && (
								<Button
									isLarge
									onClick={ this.addButton }
								>
									{ __( 'Add another button' ) }
								</Button>
							) }
							</div>
							<TextControl
								label={ __( 'Product Price' ) }
								type="text"
								value={ this.state.cwp_rev_price }
								onChange={ this.onChangeReviewPrice }
							/>
						</PanelBody>
						<PanelBody
							title={ __( 'Product Options' ) }
							className="wp-product-review-product-options"
							initialOpen={ false }
						>
							<div className="wppr-review-options-list">
							{ Object.keys( this.state.wppr_options ).map( ( key ) => (
								<div className="wppr-review-options-item">
									<label for={`wppr-option-item-${key}`}>{ key }</label>
									<TextControl
										type="text"
										id={`wppr-option-item-${key}`}
										className="wppr-text"
										placeholder={ __( 'Option' ) }
										value={ this.state.wppr_options[key].name }
										onChange={ ( e ) => this.onChangeOptionText( e, key ) }
									/>
									<TextControl
										type="number"
										className="wppr-text wppr-option-number"
										placeholder={ __( '0' ) }
										min={ 0 }
										max={ 100 }
										value={ this.state.wppr_options[key].value }
										onChange={ ( e ) => this.onChangeOptionNumber( e, key ) }
									/>
								</div>
								) ) }
								{ ( Object.keys( this.state.wppr_options ).length < wpprguten.length ) && (
									<Button
										isLarge
										onClick={ this.addOption }
									>
										{ __( 'Add another option' ) }
									</Button>
								) }
							</div>
						</PanelBody>
						<PanelBody
							title={ __( 'Pro Features' ) }
							className="wp-product-review-product-pros"
							initialOpen={ false }
						>
							<div className="wppr-review-pro-list">
							{ Object.keys( this.state.wppr_pros ).map( ( key ) => (
								<div className="wppr-review-pro-item">
									<label for={`wppr-pro-item-${key}`}>{ parseInt( key ) + 1 }</label>
									<TextControl
										type="text"
										id={`wppr-pro-item-${key}`}
										className="wppr-text"
										placeholder={ __( 'Option' ) }
										value={ this.state.wppr_pros[key] }
										onChange={ ( e ) => this.onChangeProText( e, key ) }
									/>
								</div>
								) ) }
								{ ( Object.keys( this.state.wppr_pros ).length < wpprguten.length ) && (
									<Button
										isLarge
										onClick={ this.addPro }
									>
										{ __( 'Add another option' ) }
									</Button>
								) }
							</div>
						</PanelBody>
						<PanelBody
							title={ __( 'Con Features' ) }
							className="wp-product-review-product-cons"
							initialOpen={ false }
						>
							<div className="wppr-review-con-list">
								{ Object.keys( this.state.wppr_cons ).map( ( key ) => (
									<div className="wppr-review-con-item">
										<label for={`wppr-con-item-${key}`}>{ parseInt( key ) + 1 }</label>
										<TextControl
											type="text"
											id={`wppr-con-item-${key}`}
											className="wppr-text"
											placeholder={ __( 'Option' ) }
											value={ this.state.wppr_cons[key] }
											onChange={ ( e ) => this.onChangeConText( e, key ) }
										/>
									</div>
								) ) }
								{ ( Object.keys( this.state.wppr_cons ).length < wpprguten.length ) && (
									<Button
										isLarge
										onClick={ this.addCon }
									>
										{ __( 'Add another option' ) }
									</Button>
								) }
							</div>
						</PanelBody>

                    { ( wpprguten.schema_types && wpprguten.schema_types.length > 0 ) && (
						<PanelBody
							title={ __( 'Schema Details' ) }
							className="wp-product-review-schema"
							initialOpen={ false }
						>
							<div className="wppr-review-schema">
                                <SelectControl
                                    label={ __( 'Review Type' ) }
                                    value={ this.state.wppr_review_type }
                                    options={ wpprguten.schema_types }
                                    onChange={ this.onChangeSchemaType }
                                />
							</div>
							<div className="wppr-review-schema-fields">
                                { !!this.state.schema_url && (
                                    <ExternalLink href={ this.state.schema_url } title={ __( 'View Schema Description ' ) }>{ __( 'View Schema Description ' ) }</ExternalLink>
                                ) }
								{ Object.values( this.state.schema_fields ).map( ( field, key ) => (
									<div className="wppr-review-schema-field">
										<label for={`wppr-schema-field-${key}`}>{ field }</label>
										<TextControl
											type="text"
											id={`wppr-schema-field-${key}`}
											name={`wppr-schema-field-${field}`}
											className="wppr-text"
											value={ this.state.wppr_review_custom_fields[field] ? this.state.wppr_review_custom_fields[field] : ''}
											onChange={ ( e ) => this.onChangeSchemaField( e, field ) }
										/>
									</div>
								) ) }
							</div>
						</PanelBody>
                        ) }

						{ ( wpprguten.isPro ) && (
							<div className="wppr-review-import-review-button">
								<Button
									isLarge
									isPrimary
									onClick={ () => this.props.setState( { isOpen: true } ) }
								>
									{ __( 'Import Review' )  }
								</Button>
								{ this.props.isOpen ?
									<Modal
										title={ __( 'Import Review' ) }
										className="wppr-review-import-modal"
										onRequestClose={ () => this.props.setState( { isOpen: false } ) }>
										{ ( this.props.posts ) && 
											 Object.keys( this.props.posts ).map( ( key ) => (
												<PanelBody
													title={ this.props.posts[key].title.raw }
													initialOpen={ false }
												>
													<div className="cwp_pitem_info">
														<ul class="cwp_pitem_options_content">
															<h4>{ __( 'Options' ) }</h4>
															{ Object.keys( this.props.posts[key].wppr_data.wppr_options ).map( ( i ) => (
																<li>{ this.props.posts[key].wppr_data.wppr_options[i].name }</li>
															) ) }
														</ul>

														<ul class="cwp_pitem_options_pros">
															<h4>{ __( 'Pros' ) }</h4>
															{ Object.keys( this.props.posts[key].wppr_data.wppr_pros ).map( ( i ) => (
																<li>{ this.props.posts[key].wppr_data.wppr_pros[i] }</li>
															) ) }
														</ul>

														<ul class="cwp_pitem_options_cons">
															<h4>{ __( 'Cons' ) }</h4>
															{ Object.keys( this.props.posts[key].wppr_data.wppr_cons ).map( ( i ) => (
																<li>{ this.props.posts[key].wppr_data.wppr_cons[i] }</li>
															) ) }
														</ul>
														<Button
															isLarge
															onClick={ () => this.importReview( key ) }
														>
															{ __( 'Import Review' ) }
														</Button>
													</div>
												</PanelBody>
											 ) )
										}
									</Modal> 
								: null }
							</div>
						) }
					</PluginSidebar>
				] }
			</Fragment>
		)
	}
}

const WPPR = compose( [
	withSelect( ( select, { forceIsSaving } ) => {
		const {
			getCurrentPostId,
			isSavingPost,
			isPublishingPost,
			isAutosavingPost,
			getCurrentPostType,
		} = select( 'core/editor' );
		const latestPostsQuery = pickBy( {
			per_page: 100,
			meta_key: 'cwp_meta_box_check',
			meta_value: 'Yes'
		}, ( value ) => ! isUndefined( value ) );
		return {
			postId: getCurrentPostId(),
			postType: getCurrentPostType(),
			posts: select( 'core' ).getEntityRecords( 'postType', 'post', latestPostsQuery ),
			isSaving: forceIsSaving || isSavingPost(),
			isAutoSaving: isAutosavingPost(),
			isPublishing: isPublishingPost(),
		};
	} ),

	withState( {
		isOpen: false,
		showMigration: Boolean( wpprguten.showMigrationNotice ),
	} ),

	withDispatch( ( dispatch ) => ( {
		openReviewSidebar: () => dispatch( 'core/edit-post' ).openGeneralSidebar( 'wp-product-review/wp-product-review' ),
		editPostStatus: dispatch( 'core/editor' ).editPost,
	} ) ),
] )( WP_Product_Review );

registerPlugin( 'wp-product-review', {
	icon: 'star-empty',
	render: WPPR,
} );