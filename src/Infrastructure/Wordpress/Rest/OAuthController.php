<?php

declare(strict_types=1);

namespace N3XT0R\XPub\Infrastructure\Wordpress\Rest;

use N3XT0R\XPub\Infrastructure\OAuth\OAuthTokenProviderFactory;
use WP_Error;
use WP_REST_Request;
use WP_REST_Response;

class OAuthController
{
    public static function register(): void
    {
        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/start', [
            'methods' => 'GET',
            'callback' => [self::class, 'start'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/callback', [
            'methods' => 'GET',
            'callback' => [self::class, 'callback'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route('xpub/v1', '/oauth/(?P<provider>[a-z0-9_-]+)/client-token', [
            'methods' => 'POST',
            'callback' => [self::class, 'clientCredentialsToken'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function start(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');

        try {
            $provider = OAuthTokenProviderFactory::createFromPublisherSlug($slug);
            $authUrl = $provider->getAuthorizationUrl();
            update_option("xpub_oauth_{$slug}_state", $provider->getState());

            return new WP_REST_Response(['url' => $authUrl]);
        } catch (\Throwable $e) {
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public static function callback(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');
        $expectedState = get_option("xpub_oauth_{$slug}_state");

        if (!$expectedState || $expectedState !== $request->get_param('state')) {
            return new WP_Error('invalid_state', 'Invalid OAuth state', ['status' => 403]);
        }

        try {
            $provider = OAuthTokenProviderFactory::createFromPublisherSlug($slug);
            $accessToken = $provider->fetchAccessTokenByCode($request->get_param('code'));
            $provider->storeAccessToken($accessToken);

            return new WP_REST_Response(['success' => true]);
        } catch (\Throwable $e) {
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }

    public static function clientCredentialsToken(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        $slug = $request->get_param('provider');

        try {
            $provider = OAuthTokenProviderFactory::createFromPublisherSlug($slug);
            $accessToken = $provider->fetchAccessTokenByClientCredentials();

            return new WP_REST_Response([
                'access_token' => $accessToken->getToken(),
                'expires' => $accessToken->getExpires(),
                'token_type' => 'Bearer',
                'success' => true,
            ]);
        } catch (\Throwable $e) {
            return new WP_Error('oauth_error', $e->getMessage(), ['status' => 500]);
        }
    }
}
