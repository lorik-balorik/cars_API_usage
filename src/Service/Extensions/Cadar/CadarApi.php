<?php
/**
 * Created by lorik.
 */

namespace App\Service\Extensions\Cadar;

use App\Service\GraphqlClient;

class CadarApi {
    private string $clientId;
    private string $clientSecret;
    private string $apiEndpoint;
    private GraphqlClient $graphqlClient;

    public function __construct( string $clientId, string $clientSecret, string $apiEndpoint, GraphqlClient $graphqlClient ) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->apiEndpoint = $apiEndpoint;
        $this->graphqlClient = $graphqlClient;
    }

    public function getSingleVehicle( $urlId ) {
        if( !isset( $urlId ) ) {
            return new \Exception( 'no vehicle ID provided' );
        }

        $query = <<<'GRAPHQL'
            query ($urlId: ID!) {
              vehicle(urlId: $urlId) {
                urlId
                brand
                model
                productionYear
                mileage
                performanceHp
                fuelDisplayName
                images {
                    url
                }
              }
            }
GRAPHQL;

        $variables = [ 'urlId' => $urlId ];
        $arguments = $this->getParams();

        try{
            return $this->graphqlClient->query( $query, $arguments['endpoint'], $variables, $arguments['secret'] )['vehicle'];
        } catch( \Exception $e ) {
            return null;
        }
    }

    public function getVehicles( ?array $filters = [] ) {

        $query = <<<'GRAPHQL'
            query ($filters: VehicleSearchFiltersInput!) {
              vehicleSearch(filters: $filters, settings: {}) {
                totalCount
                vehicles {
                  urlId
                  brand
                  model
                  productionYear
                  mileage
                  fuelDisplayName
                  images {
                    url
                  }
                }
              }
            }
GRAPHQL;

        $variables = [ 'filters' => $filters ];
        $arguments = $this->getParams();

        try{
            return $this->graphqlClient->query( $query, $arguments['endpoint'], $variables, $arguments['secret'] )['vehicleSearch']['vehicles'];
        } catch( \Exception $e ) {
            return null;
        }
    }

    public function getBrandOptions() {

        $query = <<<'GRAPHQL'
            query ($filters: VehicleSearchFiltersInput!) {
              vehicleSearchFilterSuggestions(filters: $filters) {
                brand
              }
            }
GRAPHQL;

        $arguments = $this->getParams();

        try{
            $brands = $this->graphqlClient->query( $query, $arguments['endpoint'], [ 'filters' => [] ], $arguments['secret'] )['vehicleSearchFilterSuggestions']['brand'];
            $brandNames = [];
            foreach( $brands as $value ) {
                $brandNames[ $value ] = $value;
            }

            return $brandNames;
        } catch( \Exception $e ) {
            return null;
        }
    }

    private function getParams( ?string $clientId = null, ?string $clientSecret = null, ?string $apiEndpoint = null ): array {
        $trustedClientSecret = $clientSecret ?? $this->clientSecret;

        $trustedClientId = $clientId ?? $this->clientId;
        $endpoint = $apiEndpoint ?? $this->apiEndpoint . $trustedClientId . '/graphql';

        return [ 'secret' => $trustedClientSecret, 'endpoint' => $endpoint ];
    }
}