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

      file_put_contents('/tmp/drupaldebug.txt', print_r($newelement, true) . "\n" , FILE_APPEND | LOCK_EX);

      $src = $newelement->src;
      $style = $newelement->style;
      $dataalign = $newelement->getAttribute('data-align');

      $filepath = explode('?', $src)[0];
      $filepath = substr($filepath, 20);
      $filepath = urldecode($filepath);
      $filearray = explode('/', $filepath);
      if($filearray[0] == 'styles')
      {
        array_shift($filearray);
        array_shift($filearray);
        array_shift($filearray);
      }
      $filepath = 'public://' . implode('/', $filearray);

      file_put_contents('/tmp/drupaldebug.txt', "Filepath: " . $filepath . "\n" , FILE_APPEND | LOCK_EX);

      $fid = array_key_first(\Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $filepath]));

      file_put_contents('/tmp/drupaldebug.txt', "FID: " . $fid . "\n" , FILE_APPEND | LOCK_EX);

      $fileobject = \Drupal::entityTypeManager()->getStorage('file')->load($fid);

      if(is_null($fid))
      {
        file_put_contents('/tmp/drupaldebug.txt', "Could not load FID" . "\n" , FILE_APPEND | LOCK_EX);

        continue;
      }


      $result = \Drupal::service('file.usage')->listUsage($fileobject);
      $mid = array_key_first($result['file']['media']);





      file_put_contents('/tmp/drupaldebug.txt', "MID: " . $mid . "\n" , FILE_APPEND | LOCK_EX);



//       $filename = urldecode(explode('?', basename($src))[0]);


//       $query = \Drupal::entityQuery('media')->condition('bundle', 'image')->condition('name', $filename)->accessCheck(FALSE);
//       $result = $query->execute();
//       $value = reset($result);


//       $mediaobject = \Drupal::entityTypeManager()->getStorage('media')->load($value);
      $mediaobject = \Drupal::entityTypeManager()->getStorage('media')->load($mid);

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
