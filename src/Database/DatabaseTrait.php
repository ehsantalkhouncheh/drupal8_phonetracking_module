<?php


namespace Drupal\mkt_phone_tracking\Database;


use Drupal;

/**
 * Trait databaseTrait
 *
 * @package Drupal\mkt_phone_tracking\Database
 */
trait DatabaseTrait {

  /**
   * @param string $tableName
   * @param array $fields
   *
   * @return int
   * @throws \Exception
   */
  public static function insertRecordInDb(string $tableName, array $fields): int {
    $id = NULL;
    try {
      $query = Drupal::database();
      /**
       * Clear all Null Value in fields array
       */
      $data = array_filter($fields, static function ($item) {
        return ($item !== NULL);
      });

      $id = $query->insert($tableName)->fields($data)->execute();
    } catch (Exception $e) {
      Drupal::logger('mkt_phone_tracking.DatabaseTrait')
        ->error('exception happen in Inserting with Message:' . $e->getMessage());
    }
    return $id;
  }

  /**
   * @param string $tableName
   * @param array $field
   * @param string $where_field
   * @param string $where_value
   */
  public static function updateRecordInDb(string $tableName, array $field, string $where_field, string $where_value): void {
    try {
      $query = Drupal::database();
      $query->update($tableName)
        ->condition($where_field, $where_value)
        ->fields($field)
        ->execute();
    } catch (Exception $e) {
      Drupal::logger('mkt_phone_tracking.DatabaseTrait')
        ->error('exception happen in updating with Message:' . $e->getMessage());
    }
  }

}
