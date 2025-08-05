<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Rest;

use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use Psr\Log\LoggerInterface;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class OAuthController
{
    public function __construct(private OAuthTokenProviderFactory $factory, private LoggerInterface $logger)
    {
    }

    public function register(): void
    {
        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/start', [
            'methods' => 'GET',
            'callback' => [$this, 'start'],
            'permission_callback' => function () {
                return current_user_can('manage_options'); // oder dein passendes Cap
            }
        ]);

        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/callback', [
            'methods' => 'GET',
            'callback' => [$this, 'callback'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/client-token', [
            'methods' => 'POST',
            'callback' => [$this, 'clientCredentialsToken'],
            'permission_callback' => function () {
                return current_user_can('manage_options'); // oder dein passendes Cap
            }
        ]);

        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/status', [
            'methods' => 'GET',
            'callback' => [$this, 'status'],
            'permission_callback' => function () {
                return current_user_can('manage_options'); // oder dein passendes Cap
            }
        ]);
    }

    public function start(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');

        try {
            $provider = $this->factory->createFromPublisherSlug($slug);
            $authUrl = $provider->getAuthorizationUrl();
            update_option("xpub_oauth_{$slug}_state", $provider->getState());

            return new WP_REST_Response(['url' => $authUrl]);
        } catch (\Throwable $e) {
            $this->logger->error('oAuth Error: '.$e->getMessage(), ['exception' => $e]);
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public function callback(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');
        $expectedState = get_option("xpub_oauth_{$slug}_state");

        if (!$expectedState || $expectedState !== $request->get_param('state')) {
            return new WP_Error('invalid_state', 'Invalid OAuth state', ['status' => 403]);
        }

        try {
            $provider = $this->factory->createFromPublisherSlug($slug, ['grant_type' => 'authorization_code']);
            $accessToken = $provider->fetchAccessTokenByCode((string)$request->get_param('code'));
            $provider->storeAccessToken($accessToken);

            update_option('xpub_oauth_'.$slug.'_connected', true);

            return new WP_REST_Response(['success' => true]);
        } catch (\Throwable $e) {
            $this->logger->error('oAuth Error: '.$e->getMessage(), ['exception' => $e]);
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public function clientCredentialsToken(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');

        try {
            $provider = $this->factory->createFromPublisherSlug($slug);
            $accessToken = $provider->fetchAccessTokenByClientCredentials();

            return new WP_REST_Response([
                'access_token' => $accessToken->getToken(),
                'expires' => $accessToken->getExpires(),
                'token_type' => 'Bearer',
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            $this->logger->error('oAuth Error: '.$e->getMessage(), ['exception' => $e]);
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public function status(WP_REST_Request $request): WP_REST_Response
    {
        $slug = $request->get_param('provider');
        $connected = get_option('xpub_oauth_'.$slug.'_connected', false);
        

        return new WP_REST_Response([
            'connected' => (bool)$connected,
        ]);
    }

}
