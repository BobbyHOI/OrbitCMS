<?php

namespace Jedkirby;

use RuntimeException;

class Json
{
    /**
     * @var string
     */
    const DEFAULT_ASSOC = false;
    const DEFAULT_DEPTH = 512;
    const DEFAULT_OPTS = 0;

    /**
     * @var mixed
     */
    private $response;

    /**
     * @var int
     */
    private $error;

    /**
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     */
    public function __construct(
        string $json,
        bool $assoc = self::DEFAULT_ASSOC,
        int $depth = self::DEFAULT_DEPTH,
        int $options = self::DEFAULT_OPTS
    ) {
        $this->response = json_decode($json, $assoc, $depth, $options);
        $this->error = json_last_error();
    }

    /**
     * Static method helper for this classes constructor.
     *
     * @param string $json
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     *
     * @return Json
     */
    public static function decode(
        string $json,
        bool $assoc = self::DEFAULT_ASSOC,
        int $depth = self::DEFAULT_DEPTH,
        int $options = self::DEFAULT_OPTS
    ) : Json {
        return new static(
            $json,
            $assoc,
            $depth,
            $options
        );
    }

    /**
     * Static method helper for decoding json from a file path.
     *
     * @param string $path
     * @param bool $assoc
     * @param int $depth
     * @param int $options
     *
     * @throws RuntimeException
     *
     * @return Json
     */
    public static function decodeFromPath(
        string $path,
        bool $assoc = self::DEFAULT_ASSOC,
        int $depth = self::DEFAULT_DEPTH,
        int $options = self::DEFAULT_OPTS
    ) : Json {
        if (
            false === is_readable($path) ||
            false === is_file($path)
        ) {
            throw new RuntimeException(sprintf(
                'A file at "%s" does not exist.',
                $path
            ));
        }

        return static::decode(
            file_get_contents($path),
            $assoc,
            $depth,
            $options
        );
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return int
     */
    public function getErrorCode() : int
    {
        return $this->error;
    }

    /**
     * Convert the error code into an error message.
     *
     * @return string
     */
    public function getErrorMessage() : string
    {
        switch ($this->getErrorCode()) {
            case JSON_ERROR_NONE:
                $message = 'No errors';
                break;
            case JSON_ERROR_DEPTH:
                $message = 'Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            default:
                $message = 'Unknown error';
        }

        return $message;
    }

    /**
     * Check whether the parsed JSON is valid or not.
     *
     * @return bool
     */
    public function isValid() : bool
    {
        return ($this->getErrorCode() === JSON_ERROR_NONE);
    }
}
