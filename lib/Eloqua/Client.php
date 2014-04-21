<?php

/**
 * @file
 * Contains \Eloqua\Client.
 */

namespace Eloqua;

use Eloqua\Api\ApiInterface;
use Eloqua\Exception\InvalidArgumentException;
use Eloqua\HttpClient\HttpClient;
use Eloqua\HttpClient\HttpClientInterface;

/**
 * REST client for Eloqua's API.
 */
class Client
{

  /**
   * @var array
   */
  private $options = array(
    'base_url' => 'https://secure.eloqua.com/API/REST',
    'version' => '1.0',
    'user_agent' => 'Elomentary (http://github.com/tableau-mkt/elomentary)',
    'timeout' => 10,
    'count' => 100,
  );

  /**
   * The Guzzle instance used to communicate with Eloqua.
   *
   * @var HttpClient
   */
  private $httpClient;

  /**
   * Instantiate a new Eloqua client.
   *
   * @param null|HttpClientInterface $httpClient Eloqua HTTP client.
   */
  public function __construct(HttpClientInterface $httpClient = null) {
    $this->httpClient = $httpClient;
  }

  /**
   * The primary interface for interacting with different Eloqua objects.
   *
   * @param string $name
   *   The name of the API instance to return. One of:
   *   - contact: To interact with Eloqua contacts.
   *   - contact_subscription: To interact with Eloqua contact subscriptions.
   *
   * @return ApiInterface
   *
   * @throws InvalidArgumentException
   */
  public function api($name) {
    switch ($name) {
      case 'contact':
      case 'contacts':
        $api = new Api\Data\Contact($this);
        break;

      default:
        throw new InvalidArgumentException(sprintf('Undefined API instance: "%s"', $name));
    }

    return $api;
  }

  /**
   * Authenticate a user for all subsequent requests.
   *
   * @param string $site
   *   Eloqua site name for the instance against which requests should be made.
   *
   * @param string $login
   *   Eloqua user name with which requests should be made.
   *
   * @param string $password
   *   Password associated with the aforementioned Eloqua user.
   *
   * @throws InvalidArgumentException if any arguments are not specified.
   */
  public function authenticate($site, $login, $password) {
    if (empty($site) || empty($login) || empty($password)) {
      throw new InvalidArgumentException('You must specify authentication details.');
    }

    $this->getHttpClient()->authenticate($site, $login, $password);
  }

  /**
   * Returns the HttpClient.
   *
   * @return HttpClient
   */
  public function getHttpClient() {
    if ($this->httpClient === NULL) {
      $this->httpClient = new HttpClient($this->options);
    }

    return $this->httpClient;
  }

  /**
   * Sets the HttpClient.
   *
   * @param HttpClientInterface $httpClient
   */
  public function setHttpClient(HttpClientInterface $httpClient) {
    $this->httpClient = $httpClient;
  }

  /**
   * Clears headers.
   */
  public function clearHeaders() {
    $this->getHttpClient()->clearHeaders();
  }

  /**
   * Sets headers.
   *
   * @param array $headers
   */
  public function setHeaders(array $headers) {
    $this->getHttpClient()->setHeaders($headers);
  }

  /**
   * Returns a named option.
   *
   * @param string $name
   *
   * @return mixed
   *
   * @throws InvalidArgumentException
   */
  public function getOption($name) {
    if (!array_key_exists($name, $this->options)) {
      throw new InvalidArgumentException(sprintf('Undefined option: "%s"', $name));
    }

    return $this->options[$name];
  }

  /**
   * Sets a named option.
   *
   * @param string $name
   * @param mixed  $value
   *
   * @throws InvalidArgumentException
   */
  public function setOption($name, $value) {
    if (!array_key_exists($name, $this->options)) {
      throw new InvalidArgumentException(sprintf('Undefined option: "%s"', $name));
    }

    $this->options[$name] = $value;
  }

}
