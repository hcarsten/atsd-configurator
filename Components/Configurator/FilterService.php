<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace Shopware\AtsdConfigurator\Components\Configurator;

use Enlight_Config as Config;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class FilterService
{

    /**
     * ...
     *
     * @var Config
     */

    protected $config;



    /**
     * ...
     *
     * @var StockService
     */

    protected $stockService;



    /**
     * ...
     *
     * @param Config         $config
     * @param StockService   $stockService
     *
     * @return FilterService
     */

    public function __construct( Config $config, StockService $stockService )
    {
        // set params
        $this->config       = $config;
        $this->stockService = $stockService;
    }









    /**
     * Filters the configurator and removes invalid articles and empty elements.
     *
     * @param array   $configurator
     *
     * @return array
     */

    public function filter( array $configurator )
    {
        // get category config
        $allowArticlesWithoutCategory = (boolean) $this->config->get( "allowArticlesWithoutCategory" );

        // loop the fieldsets
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // can we use this article?
                    if ( ( $article['article']['active'] == false ) or ( $article['article']['mainDetail']['active'] == false ) or ( $this->stockService->getMaxArticleStock( $article['article']['mainDetail']['inStock'], $article['article']['lastStock'], $article['quantity'] ) < 1 ) )
                        // no we cant
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );

                    // do not allow articles without category
                    if ( ( $allowArticlesWithoutCategory == false ) and ( $this->hasCategory( $article['article']['id'] ) == false ) )
                        // remove the article
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );
                }

                // no articles left for this element?
                if ( count( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'] ) == 0 )
                    // remove the element
                    unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey] );
            }

            // are there any elements left?!
            if ( count( $configurator['fieldsets'][$fieldsetKey]['elements'] ) == 0 )
                // remove the fieldset
                unset( $configurator['fieldsets'][$fieldsetKey] );
        }

        // return the clean configurator
        return $configurator;
    }






    /**
     * ...
     *
     * @param integer   $articleId
     *
     * @return boolean
     */

    private function hasCategory( $articleId )
    {
        // ...
        return ( (integer) Shopware()->Modules()->Categories()->sGetCategoryIdByArticleId( $articleId ) > 0 );
    }




}


