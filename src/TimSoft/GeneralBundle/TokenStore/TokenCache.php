<?php


namespace TimSoft\GeneralBundle\TokenStore;


class TokenCache
{
    public function storeTokens($access_token, $refresh_token, $expires)
    {
        $_SESSION['access_token'] = $access_token;
        $_SESSION['refresh_token'] = $refresh_token;
        $_SESSION['token_expires'] = $expires;
    }

    public function clearTokens()
    {
        unset($_SESSION['access_token']);
        unset($_SESSION['refresh_token']);
        unset($_SESSION['token_expires']);
    }

    public function getAccessToken()
    {
        // Check if tokens exist
        if (empty($_SESSION['access_token']) ||
            empty($_SESSION['refresh_token']) ||
            empty($_SESSION['token_expires'])) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 300;
        if ($_SESSION['token_expires'] <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => 'edc51964-a928-41bd-a092-e5fa78c90147',
                'clientSecret' => ':8]gxh6Zcx4=ayxNWy6SoVVVgiSxasp=',
                'redirectUri' => 'http://localhost:88/PortailTimSoft/web/authorize',
                'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                'urlResourceOwnerDetails' => '',
                'scopes' => 'openid profile offline_access User.Read Mail.Read Calendars.Read'
            ]);

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => $_SESSION['refresh_token']
                ]);

                // Store the new values
                $this->storeTokens($newToken->getToken(), $newToken->getRefreshToken(),
                    $newToken->getExpires());

                return $newToken->getToken();
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        } else {
            // Token is still valid, just return it
            return $_SESSION['access_token'];
        }
    }
}