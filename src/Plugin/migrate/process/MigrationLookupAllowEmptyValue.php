<?php


namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\MigrationLookup;


/**
* This process plugin can be used instead of a Migration_Lookup > Default_Value pipeline,
* which does not work correctly at the moment.
*
* This process plugin calls the MigrationLookup process plugin in a try catch and returns NULL in the catch.
* This allows one to use the default_value process plugin after it:
*
* It is a workaround for this issue:
* https://www.drupal.org/project/drupal/issues/2976796
*
* Example usage in migration yml:
*
* uid:
*  -
*    plugin: migration_lookup_allow_empty_value
*    migration: users
*    source: author
*  -
*    plugin: default_value
*    default_value: 0 # anonymous
*
* @MigrateProcessPlugin(
*   id = "migration_lookup_allow_empty_value",
* )
*/

class MigrationLookupAllowEmptyValue extends MigrationLookup {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    try {
//call the original migration_lookup process plugin
      return parent::transform($value, $migrate_executable, $row, $destination_property);
    } catch(Exception $exception) {
//allow the default_value process plugin in the pipeline to provide a default value
      return NULL;
    }
  }
}
