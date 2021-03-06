<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.arcavias.com/en/license
 */

/**
 * Product controller
 */
class CatalogController extends Application_Controller_Action_Abstract
{
	public function indexAction()
	{
		$this->_forward( 'list' );
	}


	public function listsimpleAction()
	{
		$startaction = microtime( true );

		$arcavias = $this->_getArcavias();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$this->view->listsimple = Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths, 'Simple' );
		$this->view->listsimple->setView( $this->_createView() );
		$this->_helper->layout()->disableLayout();

		$msg = 'Simple list total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	/**
	 * Shows the catalog with or without given search, pagination criteria
	 */
	public function listAction()
	{
		$startaction = microtime( true );

		$arcavias = $this->_getArcavias();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );


		$conf = array( 'client' => array( 'html' => array(
			'catalog' => array( 'filter' => array(
				'default' => array( 'subparts' => array( 'search' ) )
			) )
		) ) );

		$localContext = clone $context;
		$localConfig = new MW_Config_Decorator_Memory( $localContext->getConfig(), $conf );
		$localContext->setConfig( $localConfig );

		$this->view->searchfilter = Client_Html_Catalog_Filter_Factory::createClient( $localContext, $templatePaths );
		$this->view->searchfilter->setView( $this->_createView() );


		$this->view->filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
		$this->view->filter->setView( $this->_createView() );
		$this->view->filter->process();

		$this->view->stage = Client_Html_Catalog_Stage_Factory::createClient( $context, $templatePaths );
		$this->view->stage->setView( $this->_createView() );
		$this->view->stage->process();

		$this->view->list = Client_Html_Catalog_List_Factory::createClient( $context, $templatePaths );
		$this->view->list->setView( $this->_createView() );
		$this->view->list->process();

		$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$this->view->minibasket->setView( $this->_createView() );
		$this->view->minibasket->process();

		$this->render( 'list' );


		$msg = 'Product::catalog total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	public function detailAction()
	{
		$startaction = microtime( true );


		$arcavias = $this->_getArcavias();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );


		$conf = array( 'client' => array( 'html' => array(
			'catalog' => array( 'filter' => array(
				'default' => array( 'subparts' => array( 'search' ) )
			) )
		) ) );

		$localContext = clone $context;
		$localConfig = new MW_Config_Decorator_Memory( $localContext->getConfig(), $conf );
		$localContext->setConfig( $localConfig );

		$this->view->searchfilter = Client_Html_Catalog_Filter_Factory::createClient( $localContext, $templatePaths );
		$this->view->searchfilter->setView( $this->_createView() );


		$this->view->filter = Client_Html_Catalog_Filter_Factory::createClient( $context, $templatePaths );
		$this->view->filter->setView( $this->_createView() );
		$this->view->filter->process();

		$this->view->stage = Client_Html_Catalog_Stage_Factory::createClient( $context, $templatePaths );
		$this->view->stage->setView( $this->_createView() );
		$this->view->stage->process();

		$this->view->detail = Client_Html_Catalog_Detail_Factory::createClient( $context, $templatePaths );
		$this->view->detail->setView( $this->_createView() );
		$this->view->detail->process();

		$this->view->session = Client_Html_Catalog_Session_Factory::createClient( $context, $templatePaths );
		$this->view->session->setView( $this->_createView() );
		$this->view->session->process();

		$this->view->minibasket = Client_Html_Basket_Mini_Factory::createClient( $context, $templatePaths );
		$this->view->minibasket->setView( $this->_createView() );
		$this->view->minibasket->process();

		$this->render( 'detail' );


		$msg = 'Product::detail total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	public function stockAction()
	{
		$startaction = microtime( true );

		$arcavias = $this->_getArcavias();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$this->view->stock = Client_Html_Catalog_Stock_Factory::createClient( $context, $templatePaths );
		$this->view->stock->setView( $this->_createView() );
		$this->_helper->layout()->disableLayout();

		$this->getResponse()->setHeader( 'Content-Type', 'text/javascript', true );

		$msg = 'Stock total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}


	public function countAction()
	{
		$startaction = microtime( true );

		$arcavias = $this->_getArcavias();
		$context = Zend_Registry::get( 'ctx' );
		$templatePaths = $arcavias->getCustomPaths( 'client/html' );

		$this->view->count = Client_Html_Catalog_Count_Factory::createClient( $context, $templatePaths );
		$this->view->count->setView( $this->_createView() );
		$this->_helper->layout()->disableLayout();

		$this->getResponse()->setHeader( 'Content-Type', 'text/javascript', true );

		$msg = 'Stock total time: ' . ( ( microtime( true ) - $startaction ) * 1000 ) . 'ms';
		$context->getLogger()->log( $msg, MW_Logger_Abstract::INFO, 'performance' );
	}

}
