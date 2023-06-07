<?php

namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;


/**
 * Process plugin to arrange multiple values for sub_process
 *
 * @MigrateProcessPlugin(
 *   id = "multiple_values",
 * )
 */

class MultipleValues extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    file_put_contents('/tmp/drupaldebug.txt', "---MultipleValues Start---\n" , FILE_APPEND | LOCK_EX);
    file_put_contents('/tmp/drupaldebug.txt', print_r($value, true) , FILE_APPEND | LOCK_EX);
    file_put_contents('/tmp/drupaldebug.txt', "---MultipleValues End---\n" , FILE_APPEND | LOCK_EX);

    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return FALSE;
  }

}
