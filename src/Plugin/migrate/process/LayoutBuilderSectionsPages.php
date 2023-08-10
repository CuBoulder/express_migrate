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

    if (is_null($value))
    {
        return;
    }



    foreach ($value as $section) {
      $components = [];

      $config = [
        'id' => 'field_block:node:basic_page:body',
        'label_display' => FALSE,
        'context_mapping' => [
          'entity' => 'layout_builder.entity'
        ],
        'formatter' => [
          'type' => 'text_default',
          'label' => 'hidden',
          'settings' => [],
          'third_party_settings' => []
        ]
      ];

      $components[] = new SectionComponent($generator->generate(), 'first', $config);


      file_put_contents('/tmp/drupaldebug.txt', "section:" . print_r($section, true) . "\n" , FILE_APPEND | LOCK_EX);

      foreach ($section->beans->item as $bean) {

        $bean_string = (string)$bean;
        $component_id = (integer)explode(" ", $bean_string)[0];
        $component_type = (string)explode(" ", $bean_string)[1];


        file_put_contents('/tmp/drupaldebug.txt', "bid:" . $component_id . "\n" , FILE_APPEND | LOCK_EX);

        $allowedComponents = array();

        $allowedComponents[] = 'feature_callout';
        $allowedComponents[] = 'block';
        $allowedComponents[] = 'content_row';
        $allowedComponents[] = 'content_sequence';
        $allowedComponents[] = 'video_hero_unit';

        if(!in_array($component_type, $allowedComponents))
        {
          file_put_contents('/tmp/drupaldebug.txt', "bid not in allowed list" . "\n" , FILE_APPEND | LOCK_EX);
          continue;
        }

        file_put_contents('/tmp/drupaldebug.txt', "reached" . "\n" , FILE_APPEND | LOCK_EX);

        $component_id_array = [$component_id];
        #file_put_contents('/tmp/drupaldebug.txt', print_r($component_id_array, true), FILE_APPEND | LOCK_EX);

        $destid = $lookup->lookup(['express_beans_feature_callout', 'express_beans_block', 'express_beans_content_row', 'express_beans_content_sequence', 'express_beans_video_hero_unit'], $component_id_array);

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

        $components[] = new SectionComponent($generator->generate(), 'first', $config);



      }

//      (
//      [background_color] => white
//      [content_frame_color] => none
//      [0] =>
//            [1] =>
//            [overlay_color] => none
//      [background_effect] => fixed
//      [class] =>
//            [column_width] => 12
//            [section_padding_top] => 0px
//      [section_padding_right] => 0px
//      [section_padding_bottom] => 0px
//      [section_padding_left] => 0px
//      [2] =>
//            [3] =>
//            [4] =>
//            [5] =>
//            [background_image] =>
//            [background_image_styles] =>
//            [context_mapping] => Array
//      (
//      )
//
//        )


      /*
       * field_block_section_bg_effect = background_effect
       * field_block_section_bg_image = background_image
       * field_block_section_content_bg = ???
       * field_block_section_padding = section_padding_x
       */



      $layoutSettings = [];
      $layoutSettings['background_color'] = 'white';
      $layoutSettings['content_frame_color'] = 'none';
      $layoutSettings['overlay_color'] = 'none';
      $layoutSettings['background_effect'] = 'fixed';
      $layoutSettings['column_width'] = 12;
      $layoutSettings['section_padding_top'] = '0px';
      $layoutSettings['section_padding_right'] = '0px';
      $layoutSettings['section_padding_bottom'] = '0px';
      $layoutSettings['section_padding_left'] = '0px';


      file_put_contents('/tmp/drupaldebug.txt', "Section again:" . print_r($section, true) . "\n" , FILE_APPEND | LOCK_EX);

//      $nodeChildren = $section->getChildren();
//      foreach($nodeChildren as $name => $data)
//      {
//        file_put_contents('/tmp/drupaldebug.txt', "XML child:" . "The $name is '$data' from the class " . get_class($data) . "\n" , FILE_APPEND | LOCK_EX);
//      }
//
      if(property_exists($section, 'padding_top'))
      {
        $layoutSettings['section_padding_top'] = (string)$section->padding_top;
      }
      if(property_exists($section, 'padding_right'))
      {
        $layoutSettings['section_padding_right'] = (string)$section->padding_right;
      }
      if(property_exists($section, 'padding_bottom'))
      {
        $layoutSettings['section_padding_bottom'] = (string)$section->padding_bottom;
      }
      if(property_exists($section, 'padding_left'))
      {
        $layoutSettings['section_padding_left'] = (string)$section->padding_left;
      }
      if(property_exists($section, 'bg_effect'))
      {
        $layoutSettings['background_effect'] = (string)$section->bg_effect;
      }
      if(property_exists($section, 'bg_image'))
      {
        $bg_image_srcid = (string)$section->bg_image;
        $bg_image_destid = $lookup->lookup(['express_media_images'], [$bg_image_srcid]);
        file_put_contents('/tmp/drupaldebug.txt', "BG Image Source ID: " . $bg_image_srcid . "\n" , FILE_APPEND | LOCK_EX);
        file_put_contents('/tmp/drupaldebug.txt', "BG Image Destination ID: " . print_r($bg_image_destid, true) . "\n" , FILE_APPEND | LOCK_EX);

        foreach ($bg_image_destid as $destid_element) {
          file_put_contents('/tmp/drupaldebug.txt', print_r($destid_element, true), FILE_APPEND | LOCK_EX);
          $layoutSettings['background_image'] = $destid_element['mid'];



          $overlay_styles = "";

          if ($layoutSettings['overlay_color'] == "black"){
            $overlay_styles = "linear-gradient(rgb(20, 20, 20, 0.5), rgb(20, 20, 20, 0.5))";
          }
          elseif ($layoutSettings['overlay_color'] == "white"){
            $overlay_styles = "linear-gradient(rgb(200, 200, 200, 0.7), rgb(200, 200, 200, 0.7))";
          }
          else {
            $overlay_styles = "none";
          }



          $media_entity = Media::load($layoutSettings['background_image']);
          $fid = $media_entity->getSource()->getSourceFieldValue($media_entity);
          $file = File::load($fid);
          $url = $file->createFileUrl();

          $media_image_styles = [
            'background:  ' . $overlay_styles . ', url(' . $url . ');',
            'background-position: center;',
            'background-size: cover;',
            'background-repeat: no-repeat;',
            'padding:' . $layoutSettings['section_padding_top'] . ' ' . $layoutSettings['section_padding_right'] . ' ' . $layoutSettings['section_padding_bottom'] . ' ' . $layoutSettings['section_padding_left'],
          ];
          $background_image_styles = implode(' ', $media_image_styles);

          $layoutSettings['background_image_styles'] = $background_image_styles;
          $layoutSettings['content_frame_color'] = 'light-gray';


        }


//
      }




//      if(array_key_Exists('bg_image', $section))
//      {
//
//        $source_image_id = (integer)$section['bg_image'];
//        $destination_image_id = $lookup->lookup(['express_media_images'], [$source_image_id]);
//        $layoutSettings['background_image'] = $destination_image_id;
//      }





      //$sections[] = new Section('layout_onecol', [], $components);
      $sections[] = new Section('ucb_bootstrap_layouts__one_column', $layoutSettings, $components);


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
