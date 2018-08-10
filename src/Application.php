<?php

/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     3.3.0
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App;

use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Http\BaseApplication;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;

/**
 * Application setup class.
 *
 * This defines the bootstrapping logic and middleware layers you
 * want to use in your application.
 */
class Application extends BaseApplication
{
    /**
     * Setup the middleware your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middleware The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware.
     */
    public function middleware($middleware)
    {
        $middleware
            // Catch any exceptions in the lower layers,
            // and make an error page/response
            ->add(ErrorHandlerMiddleware::class)

            // Handle plugin/theme assets like CakePHP normally does.
            ->add(AssetMiddleware::class)

            // Apply routing
            ->add(RoutingMiddleware::class)


            // Be sure to add SocialAuthMiddleware after RoutingMiddleware
            ->add(new \ADmad\SocialAuth\Middleware\SocialAuthMiddleware([
            // Request method type use to initiate authentication.
            'requestMethod' => 'POST',
            // Login page URL. In case of auth failure user is redirected to login
            // page with "error" query string var.
            'loginUrl' => '/usuarios/login',
            // URL to redirect to after authentication (string or array).
            'loginRedirect' => '/',
            // Boolean indicating whether user identity should be returned as entity.
            'userEntity' => false,
            // User model.
            'userModel' => 'Usuarios',
            // Social profile model.
            'socialProfileModel' => 'ADmad/SocialAuth.SocialProfiles',
            // Finder type.
            'finder' => 'all',
            // Fields.
            'fields' => [
                // 'password' => 'password',
                'password' => 'senha'
            ],
            // Session key to which to write identity record to.
            'sessionKey' => 'Auth.User',
            // The method in user model which should be called in case of new user.
            // It should return a User entity.
            'getUserCallback' => 'getUser',
            // SocialConnect Auth service's providers config. https://github.com/SocialConnect/auth/blob/master/README.md
            'serviceConfig' => [
                'provider' => [
                    'facebook' => [
                        'applicationId' => '721172294889454',
                        'applicationSecret' => '8283bb4bcc4ed82a8dcee71dcfc251d5',
                        "access_token" => "EAAKP5wJ1bZB4BACqz5BWghBszCXY2BPYtJxU1rjV4IySrLSKsnVzfr8Q2reUt5lhZCaJg2ZC00N0vw1dsA5vjua4jIbJlJifRwBxKRe4NrPRhPOA8sVxQt76M5OnZCb9SaooRFTyUneAkoI0mVZCpChj7QxEHrXSXVZCfitlbcUs7b3c5PaN8bvEfXD8bZBIdcGxmsCbhJYoQZDZD",
                        'scope' => [
                            // "id",
                            'email',
                            // "picture"
                        ],
                        'fields' => [
                            "id",
                            'email',
                            "first_name",
                            "last_name",
                            // "profile_pic",
                            // http://graph.facebook.com/2017394771632953/picture?type=large
                            // http://graph.facebook.com/" + id + "/picture?type=large
                            // "profile_pic",
                            "birthday",
                            "gender"
                            // "picture",

                    // To get a full list of all posible values, refer to
                    // https://developers.facebook.com/docs/graph-api/reference/user
                        ],
                    ],
                    // 'google' => [
                    //     'applicationId' => '<application id>',
                    //     'applicationSecret' => '<application secret>',
                    //     'scope' => [
                    //         'https://www.googleapis.com/auth/userinfo.email',
                    //         'https://www.googleapis.com/auth/userinfo.profile',
                    //     ],
                    // ],
                ],
            ],
            // If you want to use CURL instead of CakePHP's Http Client set this to
            // '\SocialConnect\Common\Http\Client\Curl' or another client instance that
            // SocialConnect/Auth's Service class accepts.
            'httpClient' => '\ADmad\SocialAuth\Http\Client',
            // Whether social connect errors should be logged. Default `true`.
            'logErrors' => true,
        ]));

        return $middleware;
    }
}
