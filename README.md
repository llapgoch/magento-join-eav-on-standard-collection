# magento-join-eav-on-standard-collection
Allows the joining of EAV based tables on a non-EAV Collection

- This is a atandalone helper file - not a Magento module. It might be made into one in the future but at the moment it's a copy-paste per-project

## Example One:

This will pull customer email, firstname, and lastname from the customer EAV tables with the download_log collection.


``` 
$collection = Mage::getModel('stormkingskin/download_log')->getCollection();

$this->_aliasTables['email'] = $helper->joinEAV($collection, 'customer_id', 'customer', 'email');
$this->_aliasTables['firstname'] = $helper->joinEAV($collection, 'customer_id', 'customer', 'firstname');
$this->_aliasTables['lastname'] = $helper->joinEAV($collection, 'customer_id', 'customer', 'lastname');
```

### Adding company from the customer address
This requires us to add an additional left join onto the customer address table in order to make the joinEav work correctly:

```
$collection->getSelect()->joinLeft(
array('customer_address' => $collection->getTable('customer/address_entity')),
'customer_address.parent_id=main_table.customer_id',
array("")
);

$this->_aliasTables['company'] = $helper->joinEAV($collection, 'entity_id', 'customer_address', 'company', 'customer_address');
```

The last parameter of the joinEav method is overridden, so instead of it using main_table to join on it uses the customer_address table



