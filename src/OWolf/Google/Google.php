<?php

namespace OWolf\Google;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use League\OAuth2\Client\Token\AccessToken;

class Google extends GoogleProvider
{
    public function __construct(array $options = [], array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
    }

    /**
     * @param  array  $response
     * @param  \League\OAuth2\Client\Grant\AbstractGrant  $grant
     * @return \League\OAuth2\Client\Token\AccessToken
     */
    protected function createAccessToken(array $response, AbstractGrant $grant)
    {
        if (isset($response['id_token']) && ! ($response['id_token'] instanceof IdToken)) {
            $idToken = (new IdTokenVerify)->verify($response['id_token']);
            if ($idToken) {
//                ($idToken->get(''))
                $response['id_token'] = $idToken;
            }
        }
        return new AccessToken($response);
    }
}