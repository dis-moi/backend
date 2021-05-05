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
     * @param array<string, mixed> $context Context from parent
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
                    'description' => 'The username you chose during registration.',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'red horse stapler xkcd yahoo',
                    'description' => 'The password you chose during registration.  ',
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
                        'description' => 'Get a Json Web Token (JWT).  Use this token in your requests headers: `Authorization: Bearer {token}`.',
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
                'Returns an authentication token (JWT) from login credentials.',
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
     * Documenters are applied in increasing order.
     * Negative values are allowed.  The default value should be 0.
     * You may use the ORDER_XXX constants for this, if you wish.
     * When two or more documenters have the same order,
     * they are applied in the lexicographical order of their class name/.
     */
    public function getOrder(): int
    {
        return self::ORDER_DEFAULT;
    }
}
