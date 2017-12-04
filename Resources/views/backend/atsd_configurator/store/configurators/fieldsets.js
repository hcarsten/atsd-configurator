
/**
 * Aquatuning Software Development - Configurator - Backend
 *
 * @category  Aquatuning
 * @package   Shopware\Plugins\AtsdConfigurator
 * @copyright Copyright (c) 2015, Aquatuning GmbH
 */

// {namespace name="backend/atsd_configurator/app"}

Ext.define( "Shopware.apps.AtsdConfigurator.store.configurators.Fieldsets",
{
    // parent
    extend: 'Ext.data.Store',

    // page size
    pageSize: 9999,

    // autoload
    autoLoad: false,

    // remote filtering
    remoteFilter: true,

    // model used for this store
    model: 'Shopware.apps.AtsdConfigurator.model.configurator.Fieldset',

    // communication proxy
    proxy:
        {
            // type
            type: "ajax",
            
            // url to call
            url: '{url controller="AtsdConfigurator" action="getFieldsetList"}',
            
            // reader
            reader:
                {
                    type: "json",
                    root: "data",
                    totalProperty: "total"
                }
        }
});


