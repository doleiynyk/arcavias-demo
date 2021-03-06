<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

abstract class Application_Controller_Action_Abstract extends Zend_Controller_Action
{
	private $_arcavias;
	private $_serverUrl;


	public function init()
	{
		parent::init();

		$this->config = Zend_Registry::get('config');

		if ( !isset( $this->config['defaultLimit'] ) ) {
			$this->defaultLimit = 24;
		} else {
			$this->defaultLimit = (int) $this->config['defaultLimit'];
		}

		$basescript = $this->getFrontController()->getBaseUrl();
		$pathstart = dirname( $basescript );

		$params = $this->_getAllParams();

		/*
		 * prepare parameters for the router and view
		 */
		if ( !isset( $params['site'] ) ) {
			$this->_setParam( 'site', $this->config['defaultSite'] );
			$site = $params['site'] = $this->config['defaultSite'];
		} else {
			$site = $params['site'];
		}

		if( isset( $this->config['content-baseurl'] ) ) {
			$contentUrl = $this->config['content-baseurl'];
		} else {
			$contentUrl = rtrim( dirname( dirname( $pathstart ) ), '/' ) . '/images';
		}

		if( isset( $this->config['template-baseurl'] ) ) {
			$templateUrl = $this->config['template-baseurl'];
		} else {
			$templateUrl = rtrim( dirname( dirname( $pathstart ) ), '/' ) . '/vendor/arcavias/arcavias-core/client/html/lib/classic';
		}

		$viewParts = array(
			'basescript' => $basescript,
			'pathstart' => $pathstart,
			'templateUrl' => $templateUrl,
			'defaultLimit' => $this->defaultLimit,
		);
		$this->view->assign( $viewParts );


		$config = array( 'client' => array( 'html' => array(
			'common' => array(
				'content' => array( 'baseurl' => $contentUrl ),
				'template' => array( 'baseurl' => $templateUrl ),
			),
			'account' => array(
				'history' => array( 'url' => array(
					'target' => 'routeDefault',
					'controller' => 'account',
					'action' => 'index'
				) ),
				'favorite' => array( 'url' => array(
					'target' => 'routeDefault',
					'controller' => 'account',
					'action' => 'index'
				) ),
				'watch' => array( 'url' => array(
					'target' => 'routeDefault',
					'controller' => 'account',
					'action' => 'index'
				) ),
			),
			'basket' => array(
				'standard' => array( 'url' => array( 'target' => 'routeDefault' ) ),
			),
			'catalog' => array(
				'count' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'list' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'listsimple' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'detail' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'stock' => array( 'url' => array( 'target' => 'routeDefault' ) ),
			),
			'checkout' => array(
				'confirm' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'update' => array( 'url' => array( 'target' => 'routeDefault' ) ),
				'standard' => array(
					'url' => array( 'target' => 'routeDefault' ),
					'summary' => array( 'option' => array( 'terms' => array(
						'url' => array(
							'target' => 'routeDefault',
							'controller' => 'index',
							'action' => 'terms'
						),
						'privacy' => array( 'url' => array(
							'target' => 'routeDefault',
							'controller' => 'index',
							'action' => 'terms'
						) )
					) ) )
				),
			),
		) ) );


		$arcavias = $this->_getArcavias();
		$ctx = new MShop_Context_Item_Default();

		$configPaths = $arcavias->getConfigPaths( 'mysql' );
		$configPaths[] = dirname( ZFAPP_ROOT ) . DIRECTORY_SEPARATOR . 'config';
		$configPaths[] = ZFAPP_ROOT . DIRECTORY_SEPARATOR . 'config';

		$conf = new MW_Config_Array( $config, $configPaths );
		if( function_exists( 'apc_store' ) === true ) {
			$conf = new MW_Config_Decorator_APC( $conf );
		}
		$conf = new MW_Config_Decorator_Memory( $conf );
		$ctx->setConfig( $conf );

		$dbm = new MW_DB_Manager_PDO( $conf );
		$ctx->setDatabaseManager( $dbm );

		$i18n_en = new MW_Translation_Zend( self::_getArcavias()->getI18nPaths(), 'gettext', 'en_GB', array('disableNotices'=>true) );
		$i18n_de = new MW_Translation_Zend( self::_getArcavias()->getI18nPaths(), 'gettext', 'de', array('disableNotices'=>true) );

		if( function_exists( 'apc_store' ) === true )
		{
			$i18n_en = new MW_Translation_Decorator_APC( $i18n_en );
			$i18n_de = new MW_Translation_Decorator_APC( $i18n_de );
		}
		$ctx->setI18n( array( 'en' => $i18n_en, 'de' => $i18n_de ) );

		$session = new MW_Session_PHP();
		$ctx->setSession( $session );

		$logger = MAdmin_Log_Manager_Factory::createManager( $ctx );
		$ctx->setLogger( $logger );

		$cache = new MAdmin_Cache_Proxy_Default( $ctx );
		$ctx->setCache( $cache );

		$ctx->setEditor( 'test' );


		$current = $session->get( 'arcavias/locale/languageid', 'en' );
		$language = $conf->get( 'mshop/locale/language', $current );

		if( isset( $params['loc-language'] ) ) {
			$language = $params['loc-language'];
		}

		if( $language !== $current ) {
			$session->set( 'arcavias/locale/languageid', $language );
		}

		$current = $session->get( 'arcavias/locale/currencyid', 'EUR' );
		$currency = $conf->get( 'mshop/locale/currency', $current );

		if( isset( $params['loc-currency'] ) ) {
			$currency = $params['loc-currency'];
		}

		if( $currency !== $current ) {
			$session->set( 'arcavias/locale/currencyid', $currency );
		}

		$localeManager = MShop_Locale_Manager_Factory::createManager($ctx);
		$localeItem = $localeManager->bootstrap( $site, $language, $currency, false );
		$ctx->setLocale($localeItem);


		$customerManager = MShop_Customer_Manager_Factory::createManager( $ctx );
		$search = $customerManager->createSearch( true );
		$search->setConditions( $search->compare( '==', 'customer.code', 'demo-test' ) );
		$result = $customerManager->searchItems( $search );

		if( ( $customerItem = reset( $result ) ) !== false ) {
			$ctx->setUserId( $customerItem->getId() );
		}


		Zend_Registry::set('ctx', $ctx);


		try
		{
			$catalogManager = MShop_Catalog_Manager_Factory::createManager( $ctx );
			Zend_Registry::set('MShop_Catalog_Manager', $catalogManager);

			$catIdRoot = $catalogManager->getTree( null, array(), MW_Tree_Manager_Abstract::LEVEL_ONE )->getId();
			$this->_setParam( 'catid-root', $catIdRoot );
			$params['catid-root'] = $catIdRoot;

			if ( !isset( $params['f-search-text'] ) && !isset( $params['f-catalog-id'] ) ) {
				$this->_setParam( 'f-catalog-id', $catIdRoot );
				$params['f-catalog-id'] = $catIdRoot;
			}
		}
		catch( Exception $e )
		{
			$ctx->getLogger()->log( 'Unable to retrieve root catalog node: ' . $e->getMessage() );
		}

		$this->view->params = $params;


		$templatePaths = $arcavias->getCustomPaths( 'client/html' );
		$this->view->localeSelect = Client_Html_Locale_Select_Factory::createClient( $ctx, $templatePaths );
		$this->view->localeSelect->setView( $this->_createView() );
		$this->view->localeSelect->process();
	}


	protected function _createView()
	{
		$context = Zend_Registry::get( 'ctx' );
		$router = Zend_Controller_Front::getInstance()->getRouter();
		$router->setGlobalParam( 'site', $this->_getParam( 'site' ) );

		// Required to generate URLs to reload the current page
		$params = $this->_getAllParams();
		$params['target'] = 'routeDefault';

		$view = new MW_View_Default();

		$helper = new MW_View_Helper_Url_Zend( $view, $router, $this->_getServerUrl() );
		$view->addHelper( 'url', $helper );

		$helper = new MW_View_Helper_Translate_Default( $view, $context->getI18n() );
		$view->addHelper( 'translate', $helper );

		$helper = new MW_View_Helper_Parameter_Default( $view, $params );
		$view->addHelper( 'param', $helper );

		$helper = new MW_View_Helper_Config_Default( $view, $context->getConfig() );
		$view->addHelper( 'config', $helper );

		$helper = new MW_View_Helper_Number_Default( $view, '.', ' ' );
		$view->addHelper( 'number', $helper );

		$helper = new MW_View_Helper_FormParam_Default( $view );
		$view->addHelper( 'formparam', $helper );

		$helper = new MW_View_Helper_Encoder_Default( $view );
		$view->addHelper( 'encoder', $helper );

		return $view;
	}


	protected function _getArcavias()
	{
		if( !isset( $this->_arcavias ) ) {
			$this->_arcavias = new Arcavias( array( dirname( ZFAPP_ROOT ) . DIRECTORY_SEPARATOR . 'ext' ) );
		}

		return $this->_arcavias;
	}


	protected function _getServerUrl()
	{
		if( $this->_serverUrl === null )
		{
			$scheme = 'http';

			if( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] === true )
				|| isset( $_SERVER['HTTP_SCHEME'] ) && $_SERVER['HTTP_SCHEME'] == 'https'
				|| isset( $_SERVER['SERVER_PORT'] ) && $_SERVER['SERVER_PORT'] == 443
			) {
				$scheme = 'https';
			}

			if( isset( $_SERVER['HTTP_HOST'] ) && !empty( $_SERVER['HTTP_HOST'] ) ) {
				$host = $_SERVER['HTTP_HOST'];
			} elseif( isset( $_SERVER['SERVER_NAME'] ) && !empty( $_SERVER['SERVER_NAME'] ) ) {
				$host = $_SERVER['SERVER_NAME'];
			} elseif( isset( $_SERVER['SERVER_ADDR'] ) && !empty( $_SERVER['SERVER_ADDR'] ) ) {
				$host = $_SERVER['SERVER_ADDR'];
			} else {
				$host = 'localhost';
			}

			if( isset( $_SERVER['SERVER_PORT'] )
				&& ( ( $scheme == 'http' && $_SERVER['SERVER_PORT'] != 80 )
				|| ( $scheme == 'https' && $_SERVER['SERVER_PORT'] != 443 ) )
			) {
				$host .= ':' . $_SERVER['SERVER_PORT'];
			}

			$this->_serverUrl = $scheme . '://' . $host;
		}

		return $this->_serverUrl;
	}
}
