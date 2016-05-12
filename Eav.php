<?php
class Llapgoch_Core_Helper_Eav extends Mage_Core_Helper_Abstract{
    protected $_aliasIndex = 0;

    public function joinEAV($collection, $mainTableForeignKey, $eavType, $attrCode){
        $this->_aliasIndex++;

        $entityType = Mage::getModel('eav/entity_type')->loadByCode($eavType);
        $entityTable = $collection->getTable($entityType->getEntityTable());

        //Use an incremented index to make sure all of the aliases for the eav attribute tables are unique.
        $attribute = Mage::getModel("eav/config")->getAttribute($eavType, $attrCode);

        $attr =  Mage::getModel('eav/entity_attribute')->loadByCode($eavType, $attrCode);
        
        $alias = 'table_' . $this->_aliasIndex;

        if ($attribute->getBackendType() != 'static'){
            $field = $alias.'.value';
            $table = $entityTable. '_'.$attribute->getBackendType();

            $collection->getSelect()
                ->joinLeft(array($alias => $table),
                    'main_table.'.$mainTableForeignKey.' = '.$alias.'.entity_id and '.$alias.'.attribute_id = '. $attr->getId(),
                    array($attribute->getAttributeCode() => $field)
                );
        }else{
            $collection->getSelect()
                ->joinLeft(array($alias => $entityTable),
                'main_table.'.$mainTableForeignKey.' = '. $alias.'.entity_id',
                    $attribute->getAttributeCode()
                );
        }

        // Return the alias so we can use the table in future queries
        return $alias;
    }
}