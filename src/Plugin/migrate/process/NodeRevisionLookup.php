<?php
namespace Drupal\migrate_express\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Determine the most recent entity revision id given an entity id
 *
 * @MigrateProcessPlugin(
 *   id = "node_revision_lookup"
 * )
 */
class NodeRevisionLookup extends ProcessPluginBase {

    /**
     * {@inheritdoc}
     */
    public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
        $nodes =  \Drupal\node\Entity\Node::loadMultiple([$value]);
        $revision_id = $nodes[$value]->getRevisionId();


        return [$value, $revision_id];
    }

}
