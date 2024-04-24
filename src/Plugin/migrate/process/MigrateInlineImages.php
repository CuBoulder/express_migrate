<?php

namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use simplehtmldom\HtmlDocument;



/**

 * @MigrateProcessPlugin(
 *   id = "migrate_inline_images",
 * )
 */

class MigrateInlineImages extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    file_put_contents('/tmp/drupaldebug.txt', "Migrate Inline Images Start\n" , FILE_APPEND | LOCK_EX);

    file_put_contents('/tmp/drupaldebug.txt', $value . "\n" , FILE_APPEND | LOCK_EX);


    $html = new HtmlDocument();

    $html->load($value);

    $replace='img';
    foreach($html->find($replace) as $key=>$element){
      $newelement = $html->find($replace,$key);

//       file_put_contents('/tmp/drupaldebug.txt', print_r($newelement, true) . "\n" , FILE_APPEND | LOCK_EX);

      $src = $newelement->src;
      $style = $newelement->style;
      $dataalign = $newelement->getAttribute('data-align');

//       file_put_contents('/tmp/drupaldebug.txt', $dataalign . "\n" , FILE_APPEND | LOCK_EX);


      $filename = urldecode(explode('?', basename($src))[0]);


      $query = \Drupal::entityQuery('media')->condition('bundle', 'image')->condition('name', $filename)->accessCheck(FALSE);
      $result = $query->execute();
      $value = reset($result);


      $mediaobject = \Drupal::entityTypeManager()->getStorage('media')->load($value);

      $uuid = '';

      if(!is_null($mediaobject))
      {
        $uuid = $mediaobject->uuid();
      }



      $newelement->outertext = '<drupal-media data-entity-type="media" data-align="' . $dataalign . '"  data-entity-uuid="' . $uuid . '"></drupal-media>';

    }

    $value = (string)$html;

    file_put_contents('/tmp/drupaldebug.txt', $value . "\n" , FILE_APPEND | LOCK_EX);


// * text-align:center - text-align-center
// drupal-media float:right - align-right



    file_put_contents('/tmp/drupaldebug.txt', "Migrate Inline Images End\n" , FILE_APPEND | LOCK_EX);

    return $value;
  }
}
