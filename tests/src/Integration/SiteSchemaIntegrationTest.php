<?php

namespace eiriksm\SiteSchema\Tests\Integration;

use PHPUnit\Framework\TestCase;

class SiteSchemaIntegrationTest extends TestCase {

  public function testOutput() {
    $filepath = '/tmp/test-schema.json';
    // Can be overridden in a enviroment variable.
    if (getenv('SCHEMA_PATH')) {
      $filepath = getenv('SCHEMA_PATH');
    }
    $data = json_decode(file_get_contents($filepath));
    // It should at least contain all of the things we had when we made this.
    $expected_contents = json_decode(file_get_contents(__DIR__ . '/../../assets/test-schema.json'));
    if (getenv('DRUPAL_VERSION')) {
      $filename = __DIR__ . '/../../assets/test-schema' . getenv('DRUPAL_VERSION') . '.json';
      if (file_exists($filename)) {
        $expected_contents = json_decode(file_get_contents($filename));
      }
    }
    foreach ($expected_contents as $item) {
      // We expect it to be in the generated one.
      foreach ($data as $delta => $generated_item) {
        if ($item->type !== $generated_item->type || $item->module !== $generated_item->module || $item->value !== $generated_item->value) {
          continue;
        }
        unset($data[$delta]);
        continue 2;
      }
      throw new \Exception('An expected item was not found in the generated file: ' . print_r($item, TRUE));
    }
    if (count($data)) {
      print_r($data);
    }
    self::assertCount(0, $data, 'An unexpected item was found in the generated JSON file');
    $this->assertTrue(TRUE, 'All expected items were found in the generated json file');
  }

}
