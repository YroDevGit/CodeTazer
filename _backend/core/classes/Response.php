<?php

namespace Classes;

class Response
{
    protected static $code = 200;
    protected static $data = null;
    protected static $details = null;
    protected static $errors = null;
    protected static $message = null;
    protected static $status = 200;
    protected static $text = null;

    static function json(array $data, int $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($data);
        exit;
    }

    static function success(string $message = "Success", array|null $details = [])
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("success_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response);
    }

    static function error(string $message = "Error", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("error_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function failed(string $message = "Failed", array|null $details = [], int $status = 200)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("failed_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function not_found(string $message = "Not found", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("notfound_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function forbidden(string $message = "Forbidden", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("forbidden_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function unauthorized(string $message = "Unauthorized", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("unauthorized_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function bad_request(string $message = "Bad Request", array|null $details = [], int $status = 210)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("badrequest_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function warning(string|null $message = "Warning", array|null $details = [], int $status = 200)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("warning_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function network_error(string|null $message = "Network error", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("no_internet_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function server_error(string|null $message = "Server error", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("backend_error_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    static function db_error(string|null $message = "Database error", array|null $details = [], int $status = 500)
    {
        $details = is_null($details) ? [] : $details;
        $response = [
            "code" => getenv("db_error_code"),
            "message" => $message,
            "details" => $details
        ];
        self::json($response, $status);
    }

    private static function array_data()
    {
        return ["code" => self::$code, "message" => self::$message, "details" => self::$details, "data" => self::$data, "errors" => self::$errors, "status" => self::$status];
    }

    public static function code(int $code)
    {
        self::$code = $code;
        return new self;
    }

    public static function message(string $message)
    {
        self::$message = $message;
        return new self;
    }

    public static function text(string $text)
    {
        self::$text = $text;
        return new self;
    }

    public static function details(mixed $details)
    {
        self::$details = $details;
        return new self;
    }

    public static function errors(mixed $errors)
    {
        self::$errors = $errors;
        return new self;
    }

    public static function data(mixed $data)
    {
        self::$data = $data;
        return new self;
    }

    public static function push(int $status = 200): void
    {
        $response = [];
        $details = self::$details;
        $data = self::$data;
        $errors = self::$errors;
        $message =  self::$message;
        $code = self::$code;
        $text = self::$text;

        if (! is_null($details)) {
            $response['details'] = $details;
        }
        if (! is_null($data)) {
            $response['data'] = $data;
        }
        if (! is_null($errors)) {
            $response['errors'] = $errors;
        }
        if (! is_null($message)) {
            $response['message'] = $message;
        }
        if (! is_null($text)) {
            $response['text'] = $text;
        }

        $response['code'] = $code;

        self::json($response, $status);
    }

    public static function pack(int $status = 200): void
    {
        self::push($status);
    }

    public static function x(int $status = 200): void
    {
        self::push($status);
    }
}
