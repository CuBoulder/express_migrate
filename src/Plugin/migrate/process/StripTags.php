<?php


namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;


/**

 * @MigrateProcessPlugin(
 *   id = "migration_strip_tags",
 * )
 */

class StripTags extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
      return strip_tags($value);
  }
}
