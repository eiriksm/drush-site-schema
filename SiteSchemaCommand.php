<?php

namespace Drush\Commands\site_schema;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Update\UpdateRegistry;
use Drush\Commands\DrushCommands;

/**
 * Command file for site-schema.
 */
class SiteSchemaCommand extends DrushCommands {

  /**
   * Get the site schema for the current site.
   *
   * @command site:schema
   * @option format The format to output.
   * @usage site:schema
   *   Get the complete schema in text
   * @bootstrap full
   * @aliases site-schema
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   */
  public function schema($options = ['format' => 'table']) {
    require_once DRUPAL_ROOT . '/core/includes/install.inc';
    require_once DRUPAL_ROOT . '/core/includes/update.inc';
    drupal_load_updates();
    /** @var \Drupal\Core\Update\UpdateRegistry $registry */
    $registry = \Drupal::service('update.post_update_registry');
    $reflected = new \ReflectionClass(UpdateRegistry::class);
    $method = $reflected->getMethod('scanExtensionsAndLoadUpdateFiles');
    $method->setAccessible(TRUE);
    $method->invoke($registry);
    $method = $reflected->getMethod('getAvailableUpdateFunctions');
    $method->setAccessible(TRUE);
    $post_update_functions = $method->invoke($registry);
    $modules = drupal_get_installed_schema_version(NULL, FALSE, TRUE);
    // Now build the table.
    foreach ($modules as $module => $schema) {
      $rows[] = [
        'type' => 'schema',
        'module' => $module,
        'value' => $schema,
      ];
    }
    foreach ($post_update_functions as $function) {
      $rows[] = [
        'type' => 'post_update',
        'value' => $function,
      ];
    }
    return new RowsOfFields($rows);
  }

}
