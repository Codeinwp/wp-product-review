/**
 * Internal dependencies
 */
import './style.scss';
import renameKey from 'rename-key';
import { reverseObject } from './utils';

/**
 * WordPress dependencies
 */
const { __ } = wp.i18n;

const { registerPlugin } = wp.plugins;


const { MediaUpload } = wp.editor;

const {
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
	compose,
} = wp.element;

const {
	PanelBody,
	TextControl,
	FormToggle,
	Button,
	SelectControl,
	withAPIData,
} = wp.components;

class WP_Product_Review extends Component {
	constructor() {
		super( ...arguments );

		this.toggleReviewStatus = this.toggleReviewStatus.bind( this );
		this.onChangeReviewTitle = this.onChangeReviewTitle.bind( this );
		this.onChangeReviewImage = this.onChangeReviewImage.bind( this );
		this.onChangeImageLink = this.onChangeImageLink.bind( this );
		this.onChangeReviewAffiliateTitle = this.onChangeReviewAffiliateTitle.bind( this );
		this.onChangeReviewAffiliateLink = this.onChangeReviewAffiliateLink.bind( this );
		this.addButton = this.addButton.bind( this );
		this.onChangeReviewPrice = this.onChangeReviewPrice.bind( this );
		this.onChangeOptionText = this.onChangeOptionText.bind( this );
		this.onChangeOptionNumber = this.onChangeOptionNumber.bind( this );
		this.addOption = this.addOption.bind( this );
		this.onChangeProText = this.onChangeProText.bind( this );
		this.addPro = this.addPro.bind( this );
		this.onChangeConText = this.onChangeConText.bind( this );
		this.addCon = this.addCon.bind( this );

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
			}
		};
	}

	componentWillReceiveProps( nextProps ) {
		if ( this.props.review !== nextProps.review ) {
			if ( nextProps.review.isLoading !== true ) {
				this.setState( { ...nextProps.review.data.wppr_data } );
			}
		}
		if ( nextProps.isPublishing || nextProps.isSaving ) {
			wp.apiRequest( { path: `/wp-product-review/update-review?id=${nextProps.postId}&postType=${nextProps.postType}`, method: 'POST', data: this.state } ).then(
				( data ) => {
					return data;
				},
				( err ) => {
					return err;
				}
			);
		}
	}

	componentDidUpdate( prevState ) {
		if ( this.state.cwp_meta_box_check !== prevState.cwp_meta_box_check && this.state.cwp_meta_box_check === 'Yes' ) {
			this.props.openReviewSidebar();
		}
	}

	toggleReviewStatus() {
		this.setState( { cwp_meta_box_check: this.state.cwp_meta_box_check === 'Yes' ? 'No' : 'Yes' } );
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
						<PanelBody
							title={ __( 'Product Details' ) }
							className="wp-product-review-product-details"
							initialOpen={ true }
						>
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
							{ Object.keys( this.state.wppr_links ).map( ( key ) => [
								<TextControl
									label={ __( 'Affiliate Button Text' ) }
									type="text"
									value={ key }
									onChange={ ( e ) => this.onChangeReviewAffiliateTitle( e, key ) }
								/>,
								<TextControl
									label={ __( 'Affiliate Button Link' ) }
									type="url"
									value={ this.state.wppr_links[key] }
									onChange={ ( e ) => this.onChangeReviewAffiliateLink( e, key ) }
								/>
							] ) }
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
								{ ( Object.keys( this.state.wppr_options ).length < 5 ) && (
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
								{ ( Object.keys( this.state.wppr_pros ).length < 5 ) && (
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
								{ ( Object.keys( this.state.wppr_cons ).length < 5 ) && (
									<Button
										isLarge
										onClick={ this.addCon }
									>
										{ __( 'Add another option' ) }
									</Button>
								) }
							</div>
						</PanelBody>
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
			getCurrentPostType,
		} = select( 'core/editor' );
		return {
			postId: getCurrentPostId(),
			postType: getCurrentPostType(),
			isSaving: forceIsSaving || isSavingPost(),
			isPublishing: isPublishingPost(),
		};
	} ),

	withDispatch( ( dispatch ) => ( {
		openReviewSidebar: () => dispatch( 'core/edit-post' ).openGeneralSidebar( 'wp-product-review/wp-product-review' ),
		editPostStatus: dispatch( 'core/editor' ).editPost,
	} ) ),

	withAPIData( ( props ) => {
		return {
			review: `/wp/v2/${ ( props.postType === 'wppr_review' ? props.postType : 'posts' ) }/${props.postId}`,
		};
	} ),
] )( WP_Product_Review );

registerPlugin( 'wp-product-review', {
	icon: 'star-empty',
	render: WPPR,
} );