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
        $field = $attrCode; // This will either be the original attribute code or 'value'

        if ($attribute->getBackendType() != 'static'){
            $field = 'value';
            $table = $entityTable. '_'.$attribute->getBackendType();

            $collection->getSelect()
                ->joinLeft(array($alias => $table),
                    'main_table.'.$mainTableForeignKey.' = '.$alias.'.entity_id and '.$alias.'.attribute_id = '. $attr->getId(),
                    array($attribute->getAttributeCode() => $alias . "." . $field)
                );
        }else{
            $collection->getSelect()
                ->joinLeft(array($alias => $entityTable),
                'main_table.'.$mainTableForeignKey.' = '. $alias.'.entity_id',
                    $attribute->getAttributeCode()
                );
        }

        // Return the table alias and field name (either $attrCode or value) so we can use the table in future queries
        return array(
            "table" => $alias,
            "field" => $field
        );

    }
}