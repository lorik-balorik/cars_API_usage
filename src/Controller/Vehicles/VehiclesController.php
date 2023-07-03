<?php
/**
 * Created by lorik.
 */

namespace App\Controller\Vehicles;

use App\Form\Vehicles\VehiclesType;
use App\Service\Extensions\Cadar\CadarApi;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VehiclesController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController {
    private CadarApi $cadarApi;

    public function __construct( CadarApi $cadarApi ) {
        $this->cadarApi = $cadarApi;
    }

    #[Route('/', name: 'vehicles_index')]
    public function indexAction( Request $request, FormFactoryInterface $formFactory ) {

        $form = $formFactory->create( VehiclesType::class );
        $formData = $form->handleRequest( $request )->getViewData();

        $apiParams = [];
        if( $form->isSubmitted() && $form->isValid() ) {
            if( $formData['brand'] !== [] ) {
                $apiParams['brand'] = $formData['brand'];
            }
            if( !is_null( $formData['productionYearMin'] ) ) {
                $apiParams['productionYear']['min'] = $formData['productionYearMin'];
            }
            if( !is_null( $formData['productionYearMax'] ) ) {
                $apiParams['productionYear']['max'] = $formData['productionYearMax'];
            }
        }

        $vehicles = $this->cadarApi->getVehicles( $apiParams );

        return $this->render('Vehicles/vehicles.html.twig', [
            'form' => $form->createView(),
            'vehicles' => $vehicles
        ]);
    }

    #[Route('/{vehicleId}', name: 'vehicle_details')]
    public function vehicleDetailsAction( $vehicleId ) {

        return $this->render('Vehicles/vehicle-details.html.twig', [
            'vehicle' => $this->cadarApi->getSingleVehicle( $vehicleId )
        ]);
    }
}