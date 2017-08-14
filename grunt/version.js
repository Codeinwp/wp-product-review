/* jshint node:true */
// https://github.com/kswedberg/grunt-version
module.exports = {
	options: {
		pkg: {
			version: '<%= package.version %>'
		}
	},
	project: {
		src: [
			'package.json'
		]
	},
	style: {
		options: {
			prefix: 'Version\\:\.*\\s'
		},
		src: [
			'wp-product-review.php',
			'assets/css/frontpage.css',
		]
	},
	functions: {
		options: {
			prefix: 'WPPR_LITE_VERSION\'\,\\s+\''
		},
		src: [
			'wp-product-review.php',
		]
	},
	class: {
		options: {
			prefix: '\\.*version\.*\\s=\.*\\s\''
		},
		src: [
			'includes/class-wppr.php',
		]
	}
};
