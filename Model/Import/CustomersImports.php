<?php

/**
 * Import Customer Data
 *
 */

namespace Wtc\CustomerImport\Model\Import;
 
use Magento\CustomerImportExport\Model\Import\Customer;
 
class CustomersImports extends Customer
{
    /**
     * Function to Import Customer Data
     *
     */
    public function importCustomer(array $rowData)
    {
        $this->prepareCustomerData($rowData);
        $entityCreate = [];
        $entityUpdate = [];
        $entityDelete = [];
        $attributeSave = [];
        
        $proceedData = $this->_prepareDataForUpdate($rowData);
        $entityCreate = array_merge($entityCreate, $proceedData[self::ENTITIES_TO_CREATE_KEY]);
        $entityUpdate = array_merge($entityUpdate, $proceedData[self::ENTITIES_TO_UPDATE_KEY]);
        foreach ($proceedData[self::ATTRIBUTES_TO_SAVE_KEY] as $table => $customerAttributes) {
            if (!isset($attributeSave[$table])) {
                $attributeSave[$table] = [];
            }
            $attributeSave[$table] = array_diff_key(
                $attributeSave[$table],
                $customerAttributes
            ) + $customerAttributes;
        }
        
        $this->updateItemsCounterStats($entityCreate, $entityUpdate, $entityDelete); //UpdateItemcounter using entiry
        
        /**
        * Save prepared data
        */
        if ($entityCreate || $entityUpdate) {
            $this->_saveCustomerEntities($entityCreate, $entityUpdate);
        }
        if ($attributeSave) {
            $this->_saveCustomerAttributes($attributeSave);
        }
        
        return $entityCreate[0]['entity_id'] ?? $entityUpdate[0]['entity_id'] ?? null;
    }
}
