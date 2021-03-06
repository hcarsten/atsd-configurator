<?php

/**
 * Aquatuning Software Development - Configurator - Component
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

namespace AtsdConfigurator\Components\Selection;

use AtsdConfigurator\Models\Repository;
use AtsdConfigurator\Models\Selection;
use AtsdConfigurator\Components\Configurator\FilterService as ConfiguratorFilterService;
use AtsdConfigurator\Components\Configurator\ParserService as ConfiguratorParserService;
use Shopware\Components\Model\ModelManager;
use AtsdConfigurator\Models\Configurator;



/**
 * Aquatuning Software Development - Configurator - Component
 */

class ParserService
{

    /**
     * ...
     *
     * @var ModelManager
     */

    protected $modelManager;




    /**
     * ...
     *
     * @var Repository
     */

    protected $repository;



    /**
     * ...
     *
     * @var ConfiguratorFilterService
     */

    protected $configuratorFilterService;



    /**
     * ...
     *
     * @var ConfiguratorParserService
     */

    protected $configuratorParserService;



    /**
     * ...
     *
     * @var ValidatorService
     */

    protected $validatorService;



    /**
     * ...
     *
     * @param ModelManager                $modelManager
     * @param ConfiguratorFilterService   $configuratorFilterService
     * @param ConfiguratorParserService   $configuratorParserService
     * @param ValidatorService            $validatorService
     */

    public function __construct( ModelManager $modelManager, ConfiguratorFilterService $configuratorFilterService, ConfiguratorParserService $configuratorParserService, ValidatorService $validatorService )
    {
        // set params
        $this->modelManager              = $modelManager;
        $this->repository                = $modelManager->getRepository( Configurator::class );
        $this->configuratorFilterService = $configuratorFilterService;
        $this->configuratorParserService = $configuratorParserService;
        $this->validatorService          = $validatorService;
    }



    /**
     * Get a configurator with all article information - just for the selection.
     * Returns the configurator or null if validation fails.
     *
     * @param integer   $configuratorId
     * @param array     $selection
     * @param boolean   $validate
     * @param boolean   $includeMaster
     *
     * @return array|null
     */

    public function getParsedConfiguratorForSelection( $configuratorId, array $selection, $validate = true, $includeMaster = true )
    {
        // get builder to get the full configurator again
        $builder = $this->repository
            ->getPartialConfiguratorWithArticlesQueryBuilder();

        // just the one
        $builder->andWhere( "configurator.id = :configuratorId" )
            ->setParameter( "configuratorId", $configuratorId );

        // get all
        $configurator = array_shift( $builder->getQuery()->getArrayResult() );



        // remove invalid stuff
        $configurator = $this->configuratorFilterService->filter( $configurator );

        // do we want to validate?
        if ( ( $validate == true ) and ( $this->validatorService->validate( $configurator, $selection ) == false ) )
            // invalid
            return null;



        // we only want selected articles
        foreach ( $configurator['fieldsets'] as $fieldsetKey => $fieldset )
        {
            // loop the elements
            foreach ( $fieldset['elements'] as $elementKey => $element )
            {
                // loop the articles
                foreach ( $element['articles'] as $articleKey => $article )
                {
                    // is this article not selected?
                    if ( !isset( $selection[$article['id']] ) )
                        // remove it
                        unset( $configurator['fieldsets'][$fieldsetKey]['elements'][$elementKey]['articles'][$articleKey] );
                }
            }
        }



        // parse it now
        $configurator = $this->configuratorParserService->parse( $configurator, $includeMaster );

        // return it
        return $configurator;
    }



    /**
     * ...
     *
     * @param Selection   $selection
     * @param boolean     $validate
     *
     * @return array|null
     */

    public function getParsedConfiguratorForSelectionBySelection( Selection $selection, $validate = true )
    {
        // the selection array
        $selectionArr = array();

        // loop all articles
        /* @var $article Selection\Article */
        foreach ( $selection->getArticles() as $article )
            // add it
            $selectionArr[$article->getArticle()->getId()] = $article->getQuantity();

        // call by array
        return $this->getParsedConfiguratorForSelection( $selection->getConfigurator()->getId(), $selectionArr, $validate );
    }

}
