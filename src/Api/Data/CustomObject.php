<?php

/**
 * @file
 * Contains \Eloqua\Api\Assets\Email.
 */

namespace Eloqua\Api\Data;

use Eloqua\DataStructures\CustomObjectData;

use Eloqua\Api\AbstractApi;
use Eloqua\Api\CreatableInterface;
use Eloqua\Api\SearchableInterface;
use Eloqua\Exception\InvalidArgumentException;

/**
 * Eloqua Custom Objects.
 *
 * Currently the API for custom object records only supports creating new
 * records, and parameter-less searching.
 *
 * This API is limited to using Eloqua REST v1.0
 */
class CustomObject extends AbstractApi implements CreatableInterface, SearchableInterface {

  /**
   * @var number Identifier representing the customObject to interact with
   */
  private $_id;

  /**
   * Gets metadata class for Custom Objects.  Used for the object's definition.
   * @return \Eloqua\Api\Assets\CustomObject
   */
  public function meta() {
    return new \Eloqua\Api\Assets\CustomObject($this->client);
  }

  /**
   * Eloqua accounts can have multiple defined custom objects, this identifies
   * which one to interface with
   *
   * @param number $id
   * @returns number
   */
  public function identify($id) {
    return $this->_id = rawurlencode($id);
  }

  /**
   * {@inheritdoc}
   */
  public function search($search = '', array $options = array()) {
    if (!empty($search)) {
      throw new InvalidArgumentException('Sorry, non-empty search parameters are not currently supported');
    }

    $customObjectData = $this->get("data/customObject/$this->_id", array_merge(array(
      'search' => $search,
    ), $options));

    return array_map(array('Eloqua\DataStructures\CustomObjectData', 'load'), $customObjectData['elements']);
  }

  /**
   * {@inheritdoc}
   *
   * @see http://topliners.eloqua.com/docs/DOC-3097
   */
  public function create($customObject) {
    if (!$customObject instanceof CustomObjectData) {
      throw new InvalidArgumentException('An input of type Eloqua\DataStructures\CustomObjectData is expected.');
    }

    return $this->post("data/customObject/$this->_id", $customObject);
  }
}