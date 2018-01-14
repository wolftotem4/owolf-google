<?php

namespace OWolf\Google;

use League\OAuth2\Client\Grant\AbstractGrant;
use League\OAuth2\Client\Provider\Google as GoogleProvider;
use League\OAuth2\Client\Token\AccessToken;

class Google extends GoogleProvider
{
    /**
     * Google constructor.
     * @param array $options
     * @param array $collaborators
     */
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
        if (isset($response['id_token']) && is_string($response['id_token'])) {
            $idToken = (new IdTokenVerify)->verify($response['id_token']);
            if ($idToken) {
                $idToken->has('sub') and ($response['resource_owner_id'] = $idToken->get('sub'));
                $response['id_token'] = $idToken;
            }
        }
        return new AccessToken($response);
    }
}