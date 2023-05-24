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
    #file_put_contents('/tmp/drupaldebug.txt', print_r($value, true) , FILE_APPEND | LOCK_EX);


    // Setup some variables we'll need:
    // - components holds all the components to be written into our section
    // - generator connects to the uuid generator service
    $components = [];
    $generator = \Drupal::service('uuid');
    $lookup = \Drupal::service('migrate.lookup');


    foreach ($value as $section) {

      foreach($section->beans as $bid)
      file_put_contents('/tmp/drupaldebug.txt', print_r($bid, true) , FILE_APPEND | LOCK_EX);

      foreach ($section->beans as $bid) {




        $component_id = (integer)$bid->item;

        if($component_id != 50)
        {
          continue;
        }

        $component_id_array = [$component_id];
        file_put_contents('/tmp/drupaldebug.txt', print_r($component_id_array, true), FILE_APPEND | LOCK_EX);

        $destid = $lookup->lookup('express_beans_feature_callout', $component_id_array);

        file_put_contents('/tmp/drupaldebug.txt', print_r($destid, true), FILE_APPEND | LOCK_EX);

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
          file_put_contents('/tmp/drupaldebug.txt', "Block loaded", FILE_APPEND | LOCK_EX);
        }

        $config = [
          'id' => 'inline_block:content_grid',
          'label' => $block_content->label(),
          'provider' => 'layout_builder',
          'label_display' => FALSE,
          'view_mode' => 'full',
          'block_revision_id' => $block_content->getRevisionId(),
          'block_serialized' => serialize($block_content),
          'context_mapping' => [],
        ];

        $components[] = new SectionComponent($generator->generate(), 'content', $config);



      }




    }

    // If you were doing multiple sections, you'd want this to be an array
    // somehow. @TODO figure out how to do that ;)
    // PARAMS: $layout_id, $layout_settings, $components
    $sections = new Section('layout_onecol', [], $components);

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
