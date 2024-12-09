<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use DomainException;
use InvalidArgumentException;
use UnexpectedValueException;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $key = getenv('jwtKey');
        $header = $request->getHeader("Authorization");
        $token = null;
        $messageError = null;
  
        // extract the token from the header
        if(!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                $token = $matches[1];
            }
        }
  
        // check if token is null or empty
        if(is_null($token) || empty($token)) {
            $model = [
                'status'    => 400,
                'error'     => 400,
                'message'   => 'Akses ditolak, token tidak diketahui',
                'messageError'  => $messageError
            ];

            $response = service('response');
            $response->setBody(json_encode($model));
            $response->setStatusCode(400);
            return $response;
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));

            $user= new \StdClass();
            $user->user_id = $this->binaryToStr($decoded->id);

            $request->user = $user;
            return $request;
        } catch (InvalidArgumentException $e) {
            // provided key/key-array is empty or malformed.
            $messageError = $e->getMessage();
        } catch (DomainException $e) {
            // provided algorithm is unsupported OR
            // provided key is invalid OR
            // unknown error thrown in openSSL or libsodium OR
            // libsodium is required but not available.
            $messageError = $e->getMessage();
        } catch (LogicException $e) {
            // errors having to do with environmental setup or malformed JWT Keys
            $messageError = $e->getMessage();
        } catch (SignatureInvalidException $e) {
            // provided JWT signature verification failed.
            $messageError = $e->getMessage();
        } catch (BeforeValidException $e) {
            // provided JWT is trying to be used before "nbf" claim OR
            // provided JWT is trying to be used before "iat" claim.
            $messageError = $e->getMessage();
        } catch (ExpiredException $e) {
            // provided JWT is trying to be used after "exp" claim.
            $messageError = $e->getMessage();
        } catch (UnexpectedValueException $e) {
            // provided JWT is malformed OR
            // provided JWT is missing an algorithm / using an unsupported algorithm OR
            // provided JWT algorithm does not match provided key OR
            // provided key ID in key/key-array is empty or invalid.
            $messageError = $e->getMessage();
        }
  
        if($messageError) {
            $model = [
                'status'        => 401,
                'error'         => 401,
                'message'       => 'Akses ditolak, token bermasalah',
                'messageError'  => $messageError
            ];
    
            $response = service('response');
            $response->setBody(json_encode($model));
            $response->setStatusCode(401);
            return $response;
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }

    /**
     * decode binary to string
     * @param $binary
     * @return string
     */
    private static function binaryToStr($binary) 
    {
        $string = '';
        for ($i = 0; $i < strlen($binary); $i += 8) {
            $string .= chr(bindec(substr($binary, $i, 8)));
        }
        return (isset($string) ? $string : null);
    }
}
