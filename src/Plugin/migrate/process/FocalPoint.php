<?php

namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\file\Entity\File;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\block_content\Entity\BlockContent;
use Drupal\media\Entity\Media;

/**
 * Migrate Focal Point Data
 *
 * @MigrateProcessPlugin(
 *   id = "focal_point_migration",
 * )
 */
class FocalPointMigration extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
//    $fp = \Drupal::service('focal_point.manager');
//    $image_factory = \Drupal::service('image.factory');
//    $file = \Drupal::entityTypeManager()->getStorage('file')->load($value);
//    $image = $image_factory->get($file->getFileUri());
//    $width = $image->getWidth();
//    $height = $image->getHeight();
//
//    $crop = $fp->getCropEntity($file, 'focal_point');
//
//    $fp->saveCropEntity($x, $y, $width, $height, $crop);


    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return FALSE;
  }

}
