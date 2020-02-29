<?php
namespace Drush\Commands\site_schema;

use Drupal\Core\Site\Settings;
use Drush\Commands\DrushCommands;

/**
 * Command file for setting-get.
 */
class SiteSchemaCommand extends DrushCommands {

  /**
   * Get the site schema for the current site.
   *
   * @command site:schema
   * @usage site:schema
   *   Get the complete schema in text
   * @bootstrap configuration
   * @aliases site-schema
   */
  public function schema() {
    print 'test';
  }

}
