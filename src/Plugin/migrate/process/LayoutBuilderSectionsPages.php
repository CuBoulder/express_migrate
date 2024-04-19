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

    $allowedComponents = array();

    $allowedComponents[] = 'feature_callout';
    $allowedComponents[] = 'block';
    $allowedComponents[] = 'content_row';
    $allowedComponents[] = 'content_sequence';
    $allowedComponents[] = 'video_hero_unit';
    $allowedComponents[] = 'hero_unit';
    $allowedComponents[] = 'expandable';
    $allowedComponents[] = 'title';
    $allowedComponents[] = 'body';
    $allowedComponents[] = 'photogallery';
    $allowedComponents[] = 'slider';
    $allowedComponents[] = 'video_reveal';
    $allowedComponents[] = 'people_list_block';
    $allowedComponents[] = 'block_wrapper';
    $allowedComponents[] = 'article_feature';
    $allowedComponents[] = 'article_grid';
    $allowedComponents[] = 'article_slider';
    $allowedComponents[] = 'articles';
    $allowedComponents[] = 'social_links';



    foreach ($value as $section) {
      $components = [];



//      $config = [
//        'id' => 'field_block:node:basic_page:body',
//        'label_display' => FALSE,
//        'context_mapping' => [
//          'entity' => 'layout_builder.entity'
//        ],
//        'formatter' => [
//          'type' => 'text_default',
//          'label' => 'hidden',
//          'settings' => [],
//          'third_party_settings' => []
//        ]
//      ];
//
//      $components[] = new SectionComponent($generator->generate(), 'first', $config);


      file_put_contents('/tmp/drupaldebug.txt', "section:" . print_r($section, true) . "\n" , FILE_APPEND | LOCK_EX);

      $sectionColumnMap = [];
      $sectionColumnMap[1] = 'first';
      $sectionColumnMap[2] = 'second';
      $sectionColumnMap[3] = 'third';
      $sectionColumnMap[4] = 'fourth';

      $current_column = 0;

      $num_columns = count($section->beans);

      file_put_contents('/tmp/drupaldebug.txt', "Number of columns:" . $num_columns . "\n" , FILE_APPEND | LOCK_EX);

      foreach ($section->beans->item as $column)
      {
        $current_column++;
        foreach ($column->item as $bean)
        {

        $bean_string = (string)$bean;
        $component_id = (integer)explode(" ", $bean_string)[0];
        $component_type = (string)explode(" ", $bean_string)[1];
        $component_display_title = (string)explode(" ", $bean_string)[2];




        file_put_contents('/tmp/drupaldebug.txt', "bid:" . $component_id . "\n" , FILE_APPEND | LOCK_EX);





        if(!in_array($component_type, $allowedComponents))
        {
          file_put_contents('/tmp/drupaldebug.txt', "bid not in allowed list" . "\n" , FILE_APPEND | LOCK_EX);
          continue;
        }

        if ($component_type == 'body')
        {
          file_put_contents('/tmp/drupaldebug.txt', "Body Bean" . "\n" , FILE_APPEND | LOCK_EX);
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

          file_put_contents('/tmp/drupaldebug.txt', "Body column name: " . $sectionColumnMap[$current_column] . "\n" , FILE_APPEND | LOCK_EX);

          $components[] = new SectionComponent($generator->generate(), $sectionColumnMap[$current_column], $config);
          continue;

        }

        if ($component_type == 'title')
        {
          file_put_contents('/tmp/drupaldebug.txt', "Title Bean" . "\n" , FILE_APPEND | LOCK_EX);
          $config = [
            'id' => 'field_block:node:basic_page:title',
            'label_display' => FALSE,
            'context_mapping' => [
              'entity' => 'layout_builder.entity',
              'view_mode' => 'view_mode'
            ],
            'formatter' => [
              'type' => 'text_default',
              'label' => 'hidden',
              'settings' => [],
              'third_party_settings' => []
            ]
          ];

          file_put_contents('/tmp/drupaldebug.txt', "Title column name: " . $sectionColumnMap[$current_column] . "\n" , FILE_APPEND | LOCK_EX);

          $components[] = new SectionComponent($generator->generate(), $sectionColumnMap[$current_column], $config);
          continue;


        }

       if ($component_type == 'photogallery')
       {
         file_put_contents('/tmp/drupaldebug.txt', "Photogallery 'Bean'" . "\n" , FILE_APPEND | LOCK_EX);

         $component_id_array = [$component_id];
         #file_put_contents('/tmp/drupaldebug.txt', print_r($component_id_array, true), FILE_APPEND | LOCK_EX);

         $destid = $lookup->lookup(['express_nodes_photo_gallery_image_gallery'], $component_id_array);

         file_put_contents('/tmp/drupaldebug.txt', "destid:" . print_r($destid, true) . "\n" , FILE_APPEND | LOCK_EX);

         #file_put_contents('/tmp/drupaldebug.txt', print_r($destid, true), FILE_APPEND | LOCK_EX);

         $real_destid = 0;

         foreach ($destid as $destid_element) {
           file_put_contents('/tmp/drupaldebug.txt', print_r($destid_element, true), FILE_APPEND | LOCK_EX);
           $real_destid = $destid_element['id'];
         }

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

         $label_display = 'visible';
         if($component_display_title == 'false')
         {
           $label_display = 0;
         }


         $config = [
           'id' => 'inline_block:'. $block_content->bundle(),
           'label' => $block_content->label(),
           'provider' => 'layout_builder',
           'label_display' => $label_display,
           'view_mode' => 'full',
           'block_revision_id' => $block_content->getRevisionId(),
           'block_serialized' => serialize($block_content),
           'context_mapping' => [],
         ];

         file_put_contents('/tmp/drupaldebug.txt', "Photogallery column name: " . $sectionColumnMap[$current_column] . "\n" , FILE_APPEND | LOCK_EX);

         $components[] = new SectionComponent($generator->generate(), $sectionColumnMap[$current_column], $config);
         continue;


       }





        file_put_contents('/tmp/drupaldebug.txt', "reached" . "\n" , FILE_APPEND | LOCK_EX);

        $component_id_array = [$component_id];
        #file_put_contents('/tmp/drupaldebug.txt', print_r($component_id_array, true), FILE_APPEND | LOCK_EX);

        $destid = $lookup->lookup(['express_beans_feature_callout', 'express_beans_block', 'express_beans_content_row', 'express_beans_content_sequence', 'express_beans_video_hero_unit', 'express_beans_expandable', 'express_beans_slider', 'express_beans_video_reveal', 'express_beans_people_list_block', 'express_beans_block_wrapper', 'express_beans_article_slider', 'express_beans_article_grid', 'express_beans_article_feature', 'express_beans_articles', 'express_beans_hero_unit', 'express_beans_social_links'], $component_id_array);

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

        $label_display = 'visible';
        if($component_display_title == 'false')
        {
          $label_display = 0;
        }

        $config = [
          'id' => 'inline_block:'. $block_content->bundle(),
          'label' => $block_content->label(),
          'provider' => 'layout_builder',
          'label_display' => $label_display,
          'view_mode' => 'full',
          'block_revision_id' => $block_content->getRevisionId(),
          'block_serialized' => serialize($block_content),
          'context_mapping' => [],
        ];

        file_put_contents('/tmp/drupaldebug.txt', "Component column name: " . $sectionColumnMap[$current_column] . "\n" , FILE_APPEND | LOCK_EX);

        $components[] = new SectionComponent($generator->generate(), $sectionColumnMap[$current_column], $config);

      }

      }
      /*
       * field_block_section_bg_effect = background_effect
       * field_block_section_bg_image = background_image
       * field_block_section_content_bg = ???
       * field_block_section_padding = section_padding_x
       */



      $layoutSettings = [];
      $layoutSettings['background_color'] = 'white';
      $layoutSettings['container_width'] = 'contained';
      $layoutSettings['content_frame_color'] = 'none';
      $layoutSettings['overlay_color'] = 'none';
      $layoutSettings['background_effect'] = 'fixed';
      $layoutSettings['column_width'] = 12;
      $layoutSettings['section_padding_top'] = '0px';
      $layoutSettings['section_padding_right'] = '0px';
      $layoutSettings['section_padding_bottom'] = '0px';
      $layoutSettings['section_padding_left'] = '0px';

      if($current_column == 0)
      {
        file_put_contents('/tmp/drupaldebug.txt', "Number of columns: " . $current_column . "\n" , FILE_APPEND | LOCK_EX);
        $layoutSettings['column_width'] = '12';
      }

      if($current_column == 1)
      {
        file_put_contents('/tmp/drupaldebug.txt', "Number of columns: " . $current_column . "\n" , FILE_APPEND | LOCK_EX);
        $layoutSettings['column_width'] = '12';
      }

      if($current_column == 2)
      {
        file_put_contents('/tmp/drupaldebug.txt', "Number of columns: " . $current_column . "\n" , FILE_APPEND | LOCK_EX);

        $layoutSettings['column_width'] = '6-6';

        if(property_exists($section, 'distribution'))
        {
          $distribution = (string)$section->distribution;
          if($distribution == 'left')
          {
            $layoutSettings['column_width'] = '8-4';
          }
          elseif($distribution == 'right')
          {
            $layoutSettings['column_width'] = '4-8';
          }
        }

      }

      if($current_column == 3)
      {
        file_put_contents('/tmp/drupaldebug.txt', "Number of columns: " . $current_column . "\n" , FILE_APPEND | LOCK_EX);
        $layoutSettings['column_width'] = '4-4-4';

        if(property_exists($section, 'distribution'))
        {
          $distribution = (string)$section->distribution;
          if($distribution == 'left')
          {
            $layoutSettings['column_width'] = '6-3-3';
          }
          elseif($distribution == 'right')
          {
            $layoutSettings['column_width'] = '3-3-6';
          }
        }

      }

      if($current_column == 4)
      {
        file_put_contents('/tmp/drupaldebug.txt', "Number of columns: " . $current_column . "\n" , FILE_APPEND | LOCK_EX);
        $layoutSettings['column_width'] = '3-3-3-3';
      }


      file_put_contents('/tmp/drupaldebug.txt', "Section again:" . print_r($section, true) . "\n" , FILE_APPEND | LOCK_EX);

//      $nodeChildren = $section->getChildren();
//      foreach($nodeChildren as $name => $data)
//      {
//        file_put_contents('/tmp/drupaldebug.txt', "XML child:" . "The $name is '$data' from the class " . get_class($data) . "\n" , FILE_APPEND | LOCK_EX);
//      }
//
      if(property_exists($section, 'container_width'))
      {
        $layoutSettings['container_width'] = (string)$section->container_width;
      }
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

      if(property_exists($section, 'overlay'))
      {
        if((string)$section->overlay == '1')
        {
          if(property_exists($section, 'text_color'))
          {
            if((string)$section->text_color == 'white')
            {
              $layoutSettings['overlay_color'] = 'black';
            }
            else
            {
              $layoutSettings['overlay_color'] = 'white';
            }
          }
        }
      }

      if(property_exists($section, 'bg_color'))
      {

        $bg_color = (string)$section->bg_color;

        if($bg_color == 'white')
        {
          $layoutSettings['background_color'] = 'white';
        }
        if($bg_color == 'gray')
        {
          $layoutSettings['background_color'] = 'light-gray';
        }
        if($bg_color == 'black')
        {
          $layoutSettings['background_color'] = 'black';
        }
        if($bg_color == 'dark_gray')
        {
          $layoutSettings['background_color'] = 'dark-gray';
        }
        if($bg_color == 'gold')
        {
          $layoutSettings['background_color'] = 'gold';
        }
        if($bg_color == 'tan')
        {
          $layoutSettings['background_color'] = 'tan';
        }
        if($bg_color == 'light_blue')
        {
          $layoutSettings['background_color'] = 'light-blue';
        }

        if($bg_color == 'medium_blue')
        {
          $layoutSettings['background_color'] = 'medium-blue';
        }

        if($bg_color == 'dark_blue')
        {
          $layoutSettings['background_color'] = 'dark-blue';
        }

        if($bg_color == 'light_green')
        {
          $layoutSettings['background_color'] = 'light-green';
        }

        if($bg_color == 'brick')
        {
          $layoutSettings['background_color'] = 'brick';
        }


      }

      if(property_exists($section, 'content_frame_color'))
      {
        $content_frame_color = (string)$section->content_frame_color;
        if($content_frame_color == 'none')
        {
          $layoutSettings['content_frame_color'] = 'none';
        }
      }

      if(property_exists($section, 'frame_bg'))
      {
        $frame_bg = (string)$section->frame_bg;
        if($frame_bg == 'hidden')
        {
          $layoutSettings['content_frame_color'] = 'none';
        }
        else
        {
          if(property_exists($section, 'text_color'))
          {
            if((string)$section->text_color == 'white')
            {
              $layoutSettings['content_frame_color'] = 'dark-gray';
            }
            else
            {
              $layoutSettings['content_frame_color'] = 'light-gray';
            }
          }
        }
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




      $columnMap = [];
      $columnMap[0] = 'ucb_bootstrap_layouts__one_column';
      $columnMap[1] = 'ucb_bootstrap_layouts__one_column';
      $columnMap[2] = 'ucb_bootstrap_layouts__two_column';
      $columnMap[3] = 'ucb_bootstrap_layouts__three_column';
      $columnMap[4] = 'ucb_bootstrap_layouts__four_column';


      //$sections[] = new Section('layout_onecol', [], $components);
      $sections[] = new Section($columnMap[$current_column], $layoutSettings, $components);


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
