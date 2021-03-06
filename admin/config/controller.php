<?php

/**
 * Media controller configuration file example
 *
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */


return array(
	'extjs' => array(
		'media' => array(
			'default' => array(

				/*
				 * Base directory to the document root of the website
				 * Must be absolute by beginning with /
				 */
				'basedir' => dirname( dirname( dirname( __FILE__ ) ) ) . DIRECTORY_SEPARATOR . 'images',

				/*
				 * Upload related settings
				 */
				'upload' => array(

					/*
					 * Media directory where the uploaded files will be stored
					 * Must be relative to the path in "basedir"
					 */
					'directory' => '',

					/*
					 * Directory permissions (in octal notation)
					 * which are applied to newly created directories
					 */
					'dirperms' => 0775,

					/*
					 * File permissions (in octal notation)
					 * which are applied to newly created files
					 */
					'fileperms' => 0664,
				),

				/*
				 * Mime icon related settings
				 */
				'mimeicon' => array(
					/*
					 * Directory where icons for the mime types stored
					 * Must be relative to the path in "basedir"
					 */
					'directory' => 'mimeicons',

					/*
					 * File extension of mime type icons
					 */
					'extension' => '.png',
				),

				/*
				 * Unix commands executed on a shell
				 */
				'command' => array(

					/*
					 * "file" command for identifying the mime type of a file
					 */
					'file' => 'file -b --mime-type %1$s',

					/*
					 * ImageMagick "identfy" command for identifying the type of an image
					 */
					'identify' => 'identify -format "%%m" %1$s',

					/*
					 * ImageMagick "convert" command for converting an image
					 * The "-flatten" creates only one image even if there are multiple layers
					 */
					'convert' => 'convert %1$s -resize %3$sx%4$s -flatten %2$s',
				),

				/*
				 * Parameter for images
				 */
				'files' => array(

					/*
					 * Allowed image mime types
					 * Other image types will be converted
					 */
					'allowedtypes' => array( 'image/jpeg', 'image/png', 'image/gif' ),

					/*
					 * Image type to which all other image types will be converted to
					 */
					'defaulttype' => 'jpeg',

					/*
					 * Maximum width of an image
					 * Image will be scaled up or down to this size without changing the
					 * width/height ratio. A value of "null" doesn't scale the image or
					 * doesn't restrict the size of the image if it's scaled due to a value
					 * in the "maxheight" parameter
					 */
					'maxwidth' => null,

					/*
					 * Maximum height of an image
					 * Image will be scaled up or down to this size without changing the
					 * width/height ratio. A value of "null" doesn't scale the image or
					 * doesn't restrict the size of the image if it's scaled due to a value
					 * in the "maxwidth" parameter
					 */
					'maxheight' => null,
				),

				/*
				 * Parameter for preview images
				 */
				'preview' => array(

					/*
					 * Allowed image mime types
					 * Other image types will be converted
					 */
					'allowedtypes' => array( 'image/jpeg', 'image/png', 'image/gif' ),

					/*
					 * Image type to which all other image types will be converted to
					 */
					'defaulttype' => 'jpeg',

					/*
					 * Maximum width of a preview image
					 * Image will be scaled up or down to this size without changing the
					 * width/height ratio. A value of "null" doesn't scale the image or
					 * doesn't restrict the size of the image if it's scaled due to a value
					 * in the "maxheight" parameter
					 */
					'maxwidth' => 320,

					/*
					 * Maximum height of a preview image
					 * Image will be scaled up or down to this size without changing the
					 * width/height ratio. A value of "null" doesn't scale the image or
					 * doesn't restrict the size of the image if it's scaled due to a value
					 * in the "maxwidth" parameter
					 */
					'maxheight' => 240,
				),
			),
		),
	),
);
