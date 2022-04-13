<?php

include_once 'Libs/php-jwt/src/JWT.php';

use Firebase\JWT\JWT;

function generateToken(array $data): string
{
    $payload = [
        'iss' => ISS, // Issuer of the token
        'aud' => AUD, // Audience of the token
        'iat' => time(), // Issued at: time when the token was generated
        'nbf' => time(), // Not before
        'exp' => time() + (2 * 7 * 24 * 60 * 60), // Expiration time
        'data' => $data // Data related to the signer user
    ];

    return JWT::encode($payload, KEY, ALGORITHM);
}

function validateToken(string $token, array $data = null): bool
{
    $token = JWT::decode($token, KEY, [ALGORITHM]);

    if ($token->aud !== AUD) {
        throw new UnexpectedValueException('Invalid audience');
    } elseif ($token->iss !== ISS) {
        throw new UnexpectedValueException('Invalid issuer');
    } elseif (time() > $token->exp) {
        throw new UnexpectedValueException('Expired token');
    } elseif (time() < $token->nbf) {
        throw new UnexpectedValueException('Cannot handle token prior to ' . $token->nbf);
    } elseif ($data !== null && $token->data !== $data) {
        throw new UnexpectedValueException('Invalid signature');
    } else {
        return true;
    }
}

function getAuthorizationHeader(): ?string
{
    $headers = null;

    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }

    return $headers;
}

function getBearerToken(): ?string
{
    $headers = getAuthorizationHeader();

    // HEADER: Get the access token from the header
    if (!empty($headers) & preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
        return $matches[1];
    } else{
        throw new UnexpectedValueException('Invalid authorization header');
    }
}