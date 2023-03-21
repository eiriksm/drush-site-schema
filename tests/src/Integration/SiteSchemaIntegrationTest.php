<?php

namespace eiriksm\SiteSchema\Tests\Integration;

use PHPUnit\Framework\TestCase;

class SiteSchemaIntegrationTest extends TestCase {

  /**
   * @dataProvider getFileVariations
   */
  public function testOutput($filename) {
    $dir = '/tmp';
    // Can be overridden in a environment variable.
    if (getenv('SCHEMA_PATH')) {
      $dir = getenv('SCHEMA_PATH');
    }
    $filepath = "$dir/$filename";
    $data = json_decode(file_get_contents($filepath));
    // It should at least contain all of the things we had when we made this.
    $expected_contents = json_decode(file_get_contents(__DIR__ . '/../../assets/' . $filename));
    if (getenv('DRUPAL_VERSION')) {
      $abs_filename = __DIR__ . '/../../assets/' . $filename . getenv('DRUPAL_VERSION');
      if (file_exists($abs_filename)) {
        $expected_contents = json_decode(file_get_contents($abs_filename));
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
    self::assertCount(0, $data, 'An unexpected item was found in the generated JSON file');
    $this->assertTrue(TRUE, 'All expected items were found in the generated json file');
  }

  public function getFileVariations() {
    return [
      [
        'filename' => 'test-schema.json',
      ],
      [
        'filename' => 'test-schema.json-excluded',
      ]
    ];
  }

}
