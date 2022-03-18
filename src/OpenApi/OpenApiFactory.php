<?php 

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        # Custom Schemas

        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'utilisateur@monmail.com'
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'Fi@15UA2:XzaP1S'
                ]
            ]
        ]);

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
            ]
        ]);
       
        # Custom Paths

        $registerPathItem = new PathItem(
            post: new Operation(
                operationId: 'register',
                tags: ['Authentication'],
                requestBody: new RequestBody(
                    description: 'Your Credentials',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                summary: 'Authentication : registration.',
                responses: [
                    '204' => [
                        'description' => 'Success feedback.',
                    ],
                    '422' => [
                        'description' => 'Error feedback',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => [
                                            'type' => 'int',
                                            'example' => 422
                                        ],
                                        'message' => [
                                            'type' => 'string',
                                            'example' => 'This user email is already in use, please chose another one.'
                                        ],                                        
                                    ]
                                ]
                            ]
                        ]                        
                    ],
                ],
                security: []
            )
        );
        $openApi->getPaths()->addPath('/api/register', $registerPathItem);

        $loginPathItem = new PathItem(
            post: new Operation(
                operationId: 'postCredentials',
                tags: ['Authentication'],
                requestBody: new RequestBody(
                    description: 'Your Credentials',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
                summary: 'Authentication : retrieves JSON Web Token from Credentials.',
                responses: [
                    '200' => [
                        'description' => 'JSON Web Token.',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ],
                    '401' => [
                        'description' => 'Invalid Credentials',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'code' => [
                                            'type' => 'int',
                                            'example' => 401
                                        ],
                                        'message' => [
                                            'type' => 'string',
                                            'example' => 'Invalid credentials.'
                                        ],                                        
                                    ]
                                ]
                            ]
                        ]                        
                    ],
                ],
                security: []
            )
        );
        $openApi->getPaths()->addPath('/api/authenticate', $loginPathItem);

        # Path editing (requiring useless id by default)
        $meOperation = $openApi->getPaths()->getPath('/api/me')->getGet()->withParameters([]);
        $mePathItem = $openApi->getPaths()->getPath('/api/me')->withGet($meOperation);
        $openApi->getPaths()->addPath('/api/me', $mePathItem);

        return $openApi;
    }
}
