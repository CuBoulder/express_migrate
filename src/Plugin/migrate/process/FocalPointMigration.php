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

    if(is_null($value))
    {
      return;
    }

//    file_put_contents('/tmp/drupaldebug.txt', "focal_point:" . print_r(gettype($value), true) . "\n" , FILE_APPEND | LOCK_EX);


    $fid_src = explode(',', $value)[0];
    $x = explode(',', $value)[1];
    $y = explode(',', $value)[2];

    $lookup = \Drupal::service('migrate.lookup');

    $fid_dst = $lookup->lookup(['express_files_images'], [$fid_src]);




    $fid_dst = $fid_dst[0]['fid'];

//    file_put_contents('/tmp/drupaldebug.txt', "fid_dst:" . print_r($fid_dst, true) . "\n" , FILE_APPEND | LOCK_EX);
//
//


    $fp = \Drupal::service('focal_point.manager');
    $image_factory = \Drupal::service('image.factory');
    $file = \Drupal::entityTypeManager()->getStorage('file')->load($fid_dst);

    if(is_null($file))
    {
      return null;
    }

    $image = $image_factory->get($file->getFileUri());
    $width = $image->getWidth();
    $height = $image->getHeight();

    $crop = $fp->getCropEntity($file, 'focal_point');


    if(!(is_null($width) or is_null($height)))
    {
      $fp->saveCropEntity($x, $y, $width, $height, $crop);
    }

    return null;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return FALSE;
  }

}
