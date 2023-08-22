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

    $html = new HtmlDocument();

    $html->load($value);

    $replace='img';
    foreach($html->find($replace) as $key=>$element){
      $newelement = $html->find($replace,$key);
      $src = $newelement->src;
      $style = $newelement->style;
      $newelement->outertext = '<drupal-media data-entity-type="media" data-entity-uuid="' . $src . '"></drupal-media>';

    }

    $img = $html->find('drupal-media', 0);
    if(!is_null($img))
    {
      $img->setAttribute('data-entity-type', 'media');
      $img->setAttribute('data-entity-uuid', 'uuid-to-be-looked-up');
      //$img->alt = "Magic method test";

      file_put_contents('/tmp/drupaldebug.txt', "src attribute" . $img->src ."\n" , FILE_APPEND | LOCK_EX);
    }

    $value = (string)$html;

    file_put_contents('/tmp/drupaldebug.txt', $value . "\n" , FILE_APPEND | LOCK_EX);




    file_put_contents('/tmp/drupaldebug.txt', "Migrate Inline Images End\n" , FILE_APPEND | LOCK_EX);

    return $value;
  }
}
