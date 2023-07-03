<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpOptions;

class GraphqlClient {
    private HttpClientInterface $client;

    public function __construct( HttpClientInterface $client ) {
        $this->client = $client;
    }

    public function query(
        string $query,
        string $endpoint,
        array $variables = [],
        ?string $token = null,
        ?array $headers = [],
    ): string|array
    {

        $options = ( new HttpOptions() )
            ->setJson( [ 'query' => $query, 'variables' => $variables ] )
            ->setHeaders( array_merge( [
                'Content-Type' => 'application/json',
                'User-Agent' => 'Symfony GraphQL client'
            ], $headers ) );

        if (null !== $token) {
            $options->setAuthBearer( $token );
        }

        $response = $this->client
            ->request( 'POST', $endpoint, $options->toArray() )
            ->toArray();

        return array_key_exists( 'errors', $response )
            ? new \Exception( $response['errors'][0]['message'] )
            : $response['data'];
    }
}