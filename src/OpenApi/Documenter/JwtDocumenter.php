<?php


declare(strict_types=1);


namespace App\OpenApi\Documenter;


use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;
use App\OpenApi\Documenter\Ability\TranslatorAbility;
use App\OpenApi\DocumenterInterface;
use Symfony\Component\HttpFoundation\Response;


/** @noinspection PhpUnused */


class JwtDocumenter implements DocumenterInterface
{

    use TranslatorAbility;

    /**
     * Adds custom data to the $openapi and returns it, or a copy of it.
     *
     * @param OpenApi $openApi
     * @param array $context
     * @return OpenApi
     */
    public function document(OpenApi $openApi, array $context = []): OpenApi
    {
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'john@dismoi.fr',
                    'description' => 'The username you chose during registration.'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'red horse stapler xkcd yahoo',
                    'description' => 'The password you chose during registration.  '
                ],
            ],
        ]);

        $jwt = new PathItem(
            'jwt',
            '',
            '',
            null,
            null,
            new Operation(
        'postCredentialsItem',
                ['Authentication'],  // tags
                [ // responses
                    Response::HTTP_OK => [
                        'description' => "Get a Json Web Token (JWT).  Use this token in your requests headers: `Authorization: Bearer {token}`.",
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                    Response::HTTP_BAD_REQUEST => [
                        'description' => 'Bad credentials.',
                    ],
                    Response::HTTP_UNAUTHORIZED => [
                        'description' => 'Unauthorized credentials.',
                    ],
                ],
                "Returns an authentication token (JWT) from login credentials.",
                "
Usage of this API require authentication.
The Token returned is a [JWT](https://jwt.io/) valid for ten hours.

Use this token in the `Authorization` header, prefixed by `Bearer `,
like so: `Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9…`.
You may use the `Authorize 🔒` button in the sandbox to do this, if you're using the sandbox.
                ",
                null,
                [],
                new RequestBody(
                    'Generate a new Json Web Token (JWT)',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ]),
                    true
                )
            ),
        );
        $openApi->getPaths()->addPath('/api/v4/_jwt', $jwt);

        return $openApi;
    }

    /**
     * REMOVE ME EVENTUALLY (once the above method is ok)
     *
     * Adds custom data to the $docs and returns them.
     *
     * The $context helps knowing whether we're in OASv2 or OASv3.
     *
     * $format is "json"
     * $context is [ "spec_version" => 2, "api_gateway" => false ]
     *
     * @param $docs
     * @param $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function documentRaw($docs, $object, string $format = null, array $context = []): array
    {
        $version = $context['spec_version'];

        $tokenSchema = [
            'type' => 'object',
            'description' => 'An authentication token ([JWT](https://jwt.io/)) for the `Authorization: Bearer` header.',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ];

        $credentialsSchema = [
            'type' => 'object',
            'description' => "User credentials to submit to the login endpoint in order to get a perishable authentication token (Json Web Token).",
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'michel',
                    'description' => 'The username you chose during registration.',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => '~5Up3®$3cR3741337',
                    'description' => 'The password or passphrase you chose during registration.',
                ],
            ],
        ];

        switch ($version) {
            case 2:
                $docs['definitions']['Token'] = $tokenSchema;
                $docs['definitions']['Credentials'] = $credentialsSchema;
                break;
            case 3:
            default:
                $docs['components']['schemas']['Token'] = $tokenSchema;
                $docs['components']['schemas']['Credentials'] = $credentialsSchema;
        }


        $tokenDocumentation = [
            'paths' => [
                '/api/v4/_jwt' => [
                    'post' => [
                        'tags' => ['Login', 'User'],
                        'operationId' => 'postCredentialsItem',
                        'summary' => "Returns an authentication Token from login Credentials.",
                        'description' => "Creating and participating to private polls require authentication.  The Token returned is a [JWT](https://jwt.io/) valid for one hour.",
                        'responses' => [
                            Response::HTTP_OK => [
                                'description' => 'A JSON Web Token (JWT)',
                            ],
                            Response::HTTP_BAD_REQUEST => [
                                'description' => 'Bad credentials.',
                            ],
                            Response::HTTP_UNAUTHORIZED => [
                                'description' => 'Unauthorized credentials.',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        switch ($version) {
            case 2:
                $tokenDocumentation = array_merge_recursive($tokenDocumentation, [
                    'paths' => [
                        '/api/v4/_jwt' => [
                            'post' => [
                                'consumes' => [
                                    'application/ld+json',
                                    'application/json',
                                ],
                                'produces' => [
                                    'application/ld+json',
                                    'application/json',
//                                    'text/html',
                                ],
                                'parameters' => [
                                    [
                                        'name' => 'Credentials',
                                        'in' => "body",
                                        'description' => 'User Credentials',
                                        'schema' => [
                                            '$ref' => '#/definitions/Credentials',
                                        ],
                                    ],
                                ],
                                'responses' => [
                                    Response::HTTP_OK => [
                                        'content' => [
                                            "application/ld+json" => [
                                                "schema" => [
                                                    '$ref' =>  '#/definitions/Token',
                                                ],
                                            ],
                                            "application/json" => [
                                                "schema" => [
                                                    '$ref' =>  '#/definitions/Token',
                                                ],
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
                break;
            case 3:
            default:
                $tokenDocumentation = array_merge_recursive($tokenDocumentation, [
                    'paths' => [
                        '/api/v4/_jwt' => [
                            'post' => [
                                'requestBody' => [
                                    "description" => "User Credentials",
                                    'content' => [
                                        'application/ld+json' => [
                                            'schema' => [
                                                '$ref' => '#/components/schemas/Credentials',
                                            ],
                                        ],
                                        'application/json' => [
                                            'schema' => [
                                                '$ref' => '#/components/schemas/Credentials',
                                            ],
                                        ],
                                    ],
                                ],
                                'responses' => [
                                    Response::HTTP_OK => [
                                        'schema' => [
                                            '$ref' => '#/components/schemas/Token',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]);
        }

        return array_merge_recursive($docs, $tokenDocumentation);
    }

    /**
     * Documenters are applied in increasing order.
     * Negative values are allowed.  The default value should be 0.
     * You may use the ORDER_XXX constants for this, if you wish.
     * When two or more documenters have the same order,
     * they are applied in the lexicographical order of their class name/.
     *
     * @return int
     */
    public function getOrder(): int
    {
        return self::ORDER_DEFAULT;
    }
}