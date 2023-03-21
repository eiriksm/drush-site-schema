<?php

namespace Drush\Commands\site_schema;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Site\Settings;
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
    try {
      $enabled_prop = $reflected->getProperty('enabledExtensions');
    }
    catch (\Throwable $e) {
      // On Drupal 8 it's called this.
      $enabled_prop = $reflected->getProperty('enabledModules');
    }
    $enabled_prop->setAccessible(TRUE);
    $current = $enabled_prop->getValue($registry);
    $disabled_modules = Settings::get('drush_site_schema_disabled_modules', []);
    \Drupal::moduleHandler()->alter('drush_site_schema_disabled_modules', $disabled_modules);
    $enabled_prop->setValue($registry, array_diff($current, $disabled_modules));
    $post_update_functions = $method->invoke($registry);
    try {
      // This service was introduced in drupal 9.3.0.
      /** @var \Drupal\Core\Update\UpdateHookRegistry $service */
      $service = \Drupal::service('update.update_hook_registry');
      $modules = $service->getAllInstalledVersions();
    }
    catch (\Throwable $e) {
      // Maybe it's a pre-9.3.0 drupal.
      $modules = drupal_get_installed_schema_version(NULL, FALSE, TRUE);
    }
    // Now build the table.
    foreach ($modules as $module => $schema) {
      if (in_array($module, $disabled_modules)) {
        continue;
      }
      $rows[] = [
        'type' => 'schema',
        'module' => $module,
        'value' => (string) $schema,
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
