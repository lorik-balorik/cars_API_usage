<?php
/**
 * Created by lorik.
 */

namespace App\Form\Vehicles;

use App\Service\Extensions\Cadar\CadarApi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VehiclesType extends AbstractType {
    private CadarApi $cadarApi;

    public function __construct( CadarApi $cadarApi ) {

        $this->cadarApi = $cadarApi;
    }
    public function buildForm( FormBuilderInterface $builder, array $options ) {
        $builder->add( 'brand', ChoiceType::class, [ 'label' => 'Brand',
                 'choices' => $this->getAvailableBrandOptions(),
                 'multiple' => true,
                 'attr' => [ 'class' => 'brand-select m-lg-2 rounded' ],
                 'required' => false ] )
            ->add( 'productionYearMin', ChoiceType::class, [
                'choices' => array_combine( range( 1995, date( 'Y' ) ),  range( 1995, date( 'Y' ) ) ),
                'attr' => [ 'class' => 'm-lg-2' ],
                'required' => false ] )
            ->add( 'productionYearMax', ChoiceType::class, [
                'choices' => array_combine( range( 1995, date( 'Y' ) ),  range( 1995, date( 'Y' ) ) ),
                'required' => false,
                'attr' => [ 'class' => 'm-lg-2' ] ] )
            ->add( 'Filter', SubmitType::class, [
                'attr' => [ 'class' => 'vehicle-form-submit bg-info' ]
            ] );
    }

    private function getAvailableBrandOptions() {
        return $this->cadarApi->getBrandOptions();
    }
}