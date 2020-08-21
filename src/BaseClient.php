<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace Larva\Baidu\Cloud;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

/**
 * Class BaseClient
 * @author Tongle Xu <xutongle@gmail.com>
 */
class BaseClient implements BceInterface
{
    /**
     * @var string AccessKey ID
     */
    protected $accessId;

    /**
     * @var string AccessKey
     */
    protected $accessKey;

    /**
     * @var float
     */
    public $timeout = 5.0;

    /**
     * BaseClient constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $name => $value) {
                $this->$name = $value;
            }
        }
        $this->init();
    }

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {

    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getAccessId()
    {
        return $this->accessId;
    }

    /**
     * @return string
     */
    public function getAccessKey()
    {
        return $this->accessKey;
    }

    /**
     * @return HandlerStack
     */
    public function getHttpStack()
    {
        $stack = HandlerStack::create();
        $middleware = new HttpStack([
            'accessId' => $this->accessId,
            'accessKey' => $this->accessKey,
        ]);
        $stack->push($middleware);
        return $stack;
    }

    /**
     * Return base Guzzle options.
     *
     * @return array
     */
    protected function getBaseOptions()
    {
        return [
            'base_uri' => $this->getBaseUri(),
            'timeout' => $this->timeout,
            'handler' => $this->getHttpStack(),
            'verify' => false
        ];
    }

    /**
     * Return http client.
     *
     * @param array $options
     * @return \GuzzleHttp\Client
     * @codeCoverageIgnore
     */
    protected function getHttpClient(array $options = [])
    {
        return new Client($options);
    }

    /**
     * Make a get request.
     *
     * @param string $endpoint
     * @param array $query
     * @param array $headers
     * @return array
     */
    protected function get($endpoint, $query = [], $headers = [])
    {
        return $this->request('get', $endpoint, [
            'headers' => $headers,
            'query' => $query,
        ]);
    }

    /**
     * Make a put request.
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function put($endpoint, $params = [], $headers = [])
    {
        $options = ['headers' => $headers];
        if (!is_array($params)) {
            $options['body'] = $params;
        } else {
            $options['form_params'] = $params;
        }
        return $this->request('put', $endpoint, $options);
    }

    /**
     * Make a delete request.
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array|mixed
     */
    protected function delete($endpoint, $params = [], $headers = [])
    {
        $options = ['headers' => $headers];
        if (!is_array($params)) {
            $options['body'] = $params;
        } else {
            $options['form_params'] = $params;
        }
        return $this->request('delete', $endpoint, $options);
    }

    /**
     * Make a post request.
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function post($endpoint, $params = [], $headers = [])
    {
        $options = ['headers' => $headers];
        if (!is_array($params)) {
            $options['body'] = $params;
        } else {
            $options['form_params'] = $params;
        }
        return $this->request('post', $endpoint, $options);
    }

    /**
     * Make a post request.
     *
     * @param string $endpoint
     * @param array $params
     * @param array $headers
     * @return array
     */
    protected function postJSON($endpoint, $params = [], $headers = [])
    {
        $options = ['headers' => $headers];
        $options['headers']['Accept'] = 'application/json';
        $options['headers']['Content-Type'] = 'application/json';
        $options['body'] = json_encode($params, 320);
        return $this->request('post', $endpoint, $options);
    }

    /**
     * Make a http request.
     *
     * @param string $method
     * @param string $endpoint
     * @param array $options http://docs.guzzlephp.org/en/latest/request-options.html
     * @return mixed
     */
    protected function request($method, $endpoint, $options = [])
    {
        return $this->unwrapResponse($this->getHttpClient($this->getBaseOptions())->{$method}($endpoint, $options));
    }

    /**
     * Convert response contents to json.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array|mixed
     */
    protected function unwrapResponse(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();
        if (!empty($content)) {
            $contentType = $response->getHeaderLine('Content-Type');
            $format = $this->detectFormatByContentType($contentType);
            if ($format === null) {
                $format = $this->detectFormatByContent($content);
            }
            switch ($format) {
                case 'json':
                    return json_decode((string)$content, true);
                    break;
                case 'urlencoded':
                    $data = [];
                    parse_str((string)$content, $data);
                    return $data;
                    break;
                case 'xml':
                    if (preg_match('/charset=(.*)/i', $contentType, $matches)) {
                        $encoding = $matches[1];
                    } else {
                        $encoding = 'UTF-8';
                    }
                    $dom = new \DOMDocument('1.0', $encoding);
                    $dom->loadXML((string)$content, LIBXML_NOCDATA);
                    return $this->convertXmlToArray(simplexml_import_dom($dom->documentElement));
                    break;
                default:
                    return $content;
            }
        }
        return $content;
    }

    /**
     * Converts XML document to array.
     * @param string|\SimpleXMLElement $xml xml to process.
     * @return array XML array representation.
     */
    protected function convertXmlToArray($xml)
    {
        if (is_string($xml)) {
            $xml = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        }
        $result = (array)$xml;
        foreach ($result as $key => $value) {
            if (!is_scalar($value)) {
                $result[$key] = $this->convertXmlToArray($value);
            }
        }
        return $result;
    }

    /**
     * Detects format from headers.
     * @param string $contentType source content-type.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByContentType($contentType)
    {
        if (!empty($contentType)) {
            if (stripos($contentType, 'json') !== false) {
                return 'json';
            }
            if (stripos($contentType, 'urlencoded') !== false) {
                return 'urlencoded';
            }
            if (stripos($contentType, 'xml') !== false) {
                return 'xml';
            }
        }
        return null;
    }

    /**
     * Detects response format from raw content.
     * @param string $content raw response content.
     * @return null|string format name, 'null' - if detection failed.
     */
    protected function detectFormatByContent($content)
    {
        if (preg_match('/^\\{.*\\}$/is', $content)) {
            return 'json';
        }
        if (preg_match('/^([^=&])+=[^=&]+(&[^=&]+=[^=&]+)*$/', $content)) {
            return 'urlencoded';
        }
        if (preg_match('/^<.*>$/s', $content)) {
            return 'xml';
        }
        return null;
    }
}