<?php

namespace OWolf\Google;

use GuzzleHttp\Exception\BadResponseException;
use League\OAuth2\Client\Token\AccessToken;
use OWolf\Laravel\Contracts\OAuthHandler;
use OWolf\Laravel\ProviderHandler;
use OWolf\Laravel\Traits\OAuthProvider;

class GoogleOAuthHandler extends ProviderHandler implements OAuthHandler
{
    use OAuthProvider;

    /**
     * @return string
     */
    protected function getRevokeTokenUrl()
    {
        return 'https://accounts.google.com/o/oauth2/revoke';
    }

    /**
     * @param  \League\OAuth2\Client\Token\AccessToken  $token
     * @param  string  $ownerId
     * @return bool
     */
    public function revokeToken(AccessToken $token, $ownerId)
    {
        try {
            $token      = $token->getRefreshToken() ?: $token->getToken();
            $url        = $this->getRevokeTokenUrl() . '?token=' . $token;
            $request    = $this->provider()->getRequestFactory()->getRequest('GET', $url);
            $response   = $this->provider()->getResponse($request);

            return ($response->getStatusCode() == 200);
        } catch (BadResponseException $e) {
            return false;
        }
    }
}