<?php
namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Determine the most recent entity revision id given an entity id
 *
 * @MigrateProcessPlugin(
 *   id = "array_wrap"
 * )
 */
class ArrayWrap extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    return array($value);
  }

}
