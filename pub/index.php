<?php

include '../vendor/autoload.php';

ini_set('display_errors', 1);

$mvc = new \Michcald\Mvc\Mvc();

// adding routes

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}/_info')
        ->setRequirement('repository', '[a-z0-9_]+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('GET'))
    ->setUri($uri)
    ->setId('dummy.repo.info')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('infoAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}')
        ->setRequirement('repository', '[a-z0-9_]+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('GET'))
    ->setUri($uri)
    ->setId('dummy.repo.list')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('listAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}/{id}')
        ->setRequirement('repository', '[a-z0-9_]+')
        ->setRequirement('id', '\d+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('GET'))
    ->setUri($uri)
    ->setId('dummy.repo.read')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('readAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}')
        ->setRequirement('repository', '[a-z0-9_]+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('POST'))
    ->setUri($uri)
    ->setId('dummy.repo.create')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('createAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}/{id}')
        ->setRequirement('repository', '[a-z0-9_]+')
        ->setRequirement('id', '\d+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('PUT'))
    ->setUri($uri)
    ->setId('dummy.repo.update')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('updateAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('api/{repository}/{id}')
        ->setRequirement('repository', '[a-z0-9_]+')
        ->setRequirement('id', '\d+');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('DELETE'))
    ->setUri($uri)
    ->setId('dummy.repo.delete')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('deleteAction');

$mvc->addRoute($route);

// --

$uri = new \Michcald\Mvc\Router\Route\Uri();
$uri->setPattern('{any}')
        ->setRequirement('any', '.*');

$route = new \Michcald\Mvc\Router\Route();
$route->setMethods(array('GET', 'POST', 'PUT', 'DELETE', 'LINK', 'UNLINK', 'PATCH'))
    ->setUri($uri)
    ->setId('dummy.error')
    ->setControllerClass('\Michcald\Dummy\Controller\RepositoryController')
    ->setActionName('errorAction');

$mvc->addRoute($route);


// building the request

$request = new \Michcald\Mvc\Request();

if (PHP_SAPI == 'cli') {
    buildCliRequest($request);
} else {
    buildHttpRequest($request);
}

$mvc->run($request);







function buildCliRequest(\Michcald\Mvc\Request $request)
{
    $uri = isset($argv[1]) ? $argv[1] : '';

    $query = array();
    for ($i = 0; $i < $argc; $i++) {
        $query['arg' . $i] = $argv[$i];
    }

    $request->setMethod('CLI')
        ->setQueryParams($query)
        ->setUri($uri);
}

function buildHttpRequest(\Michcald\Mvc\Request $request)
{
    $uri = str_replace('?' . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']);
    $uri = substr($uri, 1);

    $tmp = __DIR__;
    $tmp = str_replace($_SERVER['DOCUMENT_ROOT'], '', $tmp);
    $tmp = str_replace('/pub', '', $tmp);
    $tmp = substr($tmp, 1);

    $uri = str_replace($tmp, '', $uri);
    $uri = substr($uri, 1);

    $request->setMethod($_SERVER['REQUEST_METHOD'])
        ->setQueryParams($_GET)
        ->setUri($uri)
        ->setHeaders($_SERVER);

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            $data = array_merge($_POST, $_FILES);
            $request->setData($data);
            break;
        default:

            $put = _parsePut();

            $data = array_merge($put, $_FILES);

            //parse_str(file_get_contents("php://input"), $params);
            $request->setData($data);
    }
}

function _parsePut()
{
    /* PUT data comes in on the stdin stream */
    $putdata = fopen("php://input", "r");

    /* Open a file for writing */
    // $fp = fopen("myputfile.ext", "w");

    $raw_data = '';

    /* Read the data 1 KB at a time
        and write to the file */
    while ($chunk = fread($putdata, 1024))
        $raw_data .= $chunk;

    /* Close the streams */
    fclose($putdata);

    // Fetch content and determine boundary
    $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

    if (empty($boundary)) {
        parse_str($raw_data, $data);
        return $data;
    }

    // Fetch each part
    $parts = array_slice(explode($boundary, $raw_data), 1);
    $data = array();

    foreach ($parts as $part) {
        // If this is the last part, break
        if ($part == "--\r\n")
            break;

        // Separate content from headers
        $part = ltrim($part, "\r\n");
        list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

        // Parse the headers list
        $raw_headers = explode("\r\n", $raw_headers);
        $headers = array();
        foreach ($raw_headers as $header) {
            list($name, $value) = explode(':', $header);
            $headers[strtolower($name)] = ltrim($value, ' ');
        }

        // Parse the Content-Disposition to get the field name, etc.
        if (isset($headers['content-disposition'])) {
            $filename = null;
            $tmp_name = null;
            preg_match(
                '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/', $headers['content-disposition'], $matches
            );
            list(, $type, $name) = $matches;

            //Parse File
            if (isset($matches[4])) {
                //if labeled the same as previous, skip
                if (isset($_FILES[$matches[2]])) {
                    continue;
                }

                //get filename
                $filename = $matches[4];

                //get tmp name
                $filename_parts = pathinfo($filename);
                $tmp_name = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                //populate $_FILES with information, size may be off in multibyte situation
                $_FILES[$matches[2]] = array(
                    'error' => 0,
                    'name' => $filename,
                    'tmp_name' => $tmp_name,
                    'size' => strlen($body),
                    'type' => $value
                );

                //place in temporary directory
                file_put_contents($tmp_name, $body);
            }
            //Parse Field
            else {
                $data[$name] = substr($body, 0, strlen($body) - 2);
            }
        }
    }
    return $data;
}
