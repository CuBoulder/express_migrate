<?php

namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
use Drupal\block_content\Entity\BlockContent;

/**
 * Process plugin to migrate a source field into a Layout Builder Section.
 *
 * @MigrateProcessPlugin(
 *   id = "layout_builder_sections_pages",
 * )
 */
class LayoutBuilderSectionsPages extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    file_put_contents('/tmp/drupaldebug.txt', "---LayoutBuilderSectionsPages---\n" , FILE_APPEND | LOCK_EX);
    file_put_contents('/tmp/drupaldebug.txt', "value:" . print_r($value, true) . "\n" , FILE_APPEND | LOCK_EX);


    // Setup some variables we'll need:
    // - components holds all the components to be written into our section
    // - generator connects to the uuid generator service
    $generator = \Drupal::service('uuid');
    $lookup = \Drupal::service('migrate.lookup');
    $sections = array();


    foreach ($value as $section) {
      $components = [];

      file_put_contents('/tmp/drupaldebug.txt', "section:" . print_r($section, true) . "\n" , FILE_APPEND | LOCK_EX);

      //foreach($section->beans as $bid)
      //file_put_contents('/tmp/drupaldebug.txt', print_r($bid, true) , FILE_APPEND | LOCK_EX);

      foreach ($section->beans->item as $bid) {


        $component_id = (integer)$bid;

        file_put_contents('/tmp/drupaldebug.txt', "bid:" . $component_id . "\n" , FILE_APPEND | LOCK_EX);

        $allowedComponents = array();
        $allowedComponents[] = 50;
        $allowedComponents[] = 290;
        $allowedComponents[] = 260;
        $allowedComponents[] = 188;
        $allowedComponents[] = 326;
        $allowedComponents[] = 216;
        $allowedComponents[] = 218;
        $allowedComponents[] = 214;
        $allowedComponents[] = 198;
        $allowedComponents[] = 210;
        $allowedComponents[] = 174;
        $allowedComponents[] = 176;
        $allowedComponents[] = 184;
        $allowedComponents[] = 186;
        $allowedComponents[] = 359;
        $allowedComponents[] = 355;

        $allowedComponents[] = 168;
        $allowedComponents[] = 286;
        $allowedComponents[] = 164;
        $allowedComponents[] = 166;



        if(!in_array($component_id, $allowedComponents))
        {
          file_put_contents('/tmp/drupaldebug.txt', "bid not in allowed list" . "\n" , FILE_APPEND | LOCK_EX);
          continue;
        }

        file_put_contents('/tmp/drupaldebug.txt', "reached" . "\n" , FILE_APPEND | LOCK_EX);

        $component_id_array = [$component_id];
        #file_put_contents('/tmp/drupaldebug.txt', print_r($component_id_array, true), FILE_APPEND | LOCK_EX);

        $destid = $lookup->lookup(['express_beans_feature_callout', 'express_beans_block', 'express_beans_content_row', 'express_beans_content_sequence'], $component_id_array);

        file_put_contents('/tmp/drupaldebug.txt', "destid:" . print_r($destid, true) . "\n" , FILE_APPEND | LOCK_EX);

        #file_put_contents('/tmp/drupaldebug.txt', print_r($destid, true), FILE_APPEND | LOCK_EX);

        $real_destid = 0;

        foreach ($destid as $destid_element) {
          file_put_contents('/tmp/drupaldebug.txt', print_r($destid_element, true), FILE_APPEND | LOCK_EX);
          $real_destid = $destid_element['id'];
        }

        file_put_contents('/tmp/drupaldebug.txt', print_r($real_destid, true), FILE_APPEND | LOCK_EX);


        $block_content = BlockContent::load($real_destid);



        if (is_null($block_content)) {
          \Drupal::messenger()->addMessage("Could not load " . $real_destid . ' ???', 'status', TRUE);
          continue;
        }
        else
        {
          file_put_contents('/tmp/drupaldebug.txt', "Block loaded" . "\n", FILE_APPEND | LOCK_EX);
          file_put_contents('/tmp/drupaldebug.txt', "bundle:" . print_r($block_content->bundle(), true) . "\n" , FILE_APPEND | LOCK_EX);

        }

        $config = [
          'id' => 'inline_block:'. $block_content->bundle(),
          'label' => $block_content->label(),
          'provider' => 'layout_builder',
          'label_display' => TRUE,
          'view_mode' => 'full',
          'block_revision_id' => $block_content->getRevisionId(),
          'block_serialized' => serialize($block_content),
          'context_mapping' => [],
        ];

        $components[] = new SectionComponent($generator->generate(), 'content', $config);



      }

      $sections[] = new Section('layout_onecol', [], $components);


    }



    file_put_contents('/tmp/drupaldebug.txt', "---\n" , FILE_APPEND | LOCK_EX);

    return $sections;
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    // Perhaps if multiple() returned TRUE this would help allow
    // multiple Sections. ;)
    return FALSE;
  }

}
