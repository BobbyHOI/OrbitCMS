Simple JSON Parsing
-------

This package is designed to bridge the gap between the default `json_*` methods, ensuring a fluent and easy to use structure.


Installation
-------

This package can be installed via [Composer]:

``` bash
$ composer require jedkirby/json
```

It requires **PHP >= 7.0.0**.


Usage
-------

The following guide assumes that you've imported the class `Jedkirby\Json` into your namespace.

The `Json` constructor requires you to pass the exact same methods as the [`json_decode`](https://www.php.net/manual/en/function.json-decode.php) method, however, is provides additional functionality once initilised.

The following code should help explain using this package, thus providing the fluent and easy to use structure:

```
$json = new Json('{"name":"James Kirby"}');

if (false === $json->isValid()) {
    throw new RuntimeException(sprintf(
        'Parsing failed with error "%s"',
        $json->getErrorMessage()
    ));
}

return $json->getResponse();
```

Helpers
-------

There's a couple of helper methods built in, which provide additional functionality, these are listed below:

| Method  | Description |
| --- | --- |
| `Json::decode()`  | Alias for the `new Json()` method, passing the same arguments.  |
| `Json::decodeFromPath()`  | Helper to read contents from a provided `$path`, which is the first argument instead of a JSON string.  |


Testing
-------

Unit tests can be run within the package, however, it utilises [Docker](https://www.docker.com/community-edition) &amp; [Docker Compose](https://docs.docker.com/compose/install):

``` bash
$ docker-compose -f ./docker-compose.yml run --rm cli php ./vendor/bin/phpunit
```

License
-------

**jedkirby/json** is licensed under the MIT license.  See the [LICENSE](LICENSE) file for more details.