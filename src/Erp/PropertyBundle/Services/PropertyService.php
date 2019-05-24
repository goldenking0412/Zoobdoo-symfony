<?php

namespace Erp\PropertyBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\UserBundle\Entity\User;
use Erp\CoreBundle\Entity\City;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Goodby\CSV\Import\Standard\LexerConfig;
use Goodby\CSV\Import\Standard\Lexer;
use Goodby\CSV\Import\Standard\Interpreter;

class PropertyService {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
    }

    /**
     * Get list of price range
     *
     * @return array
     */
    public function getListOfRange() {
        return ['min' => 'Min', 'max' => 'Max'];
    }

    /**
     * Get list of price range
     *
     * @return array
     */
    public function getListOfPrice() {
        return [
            50 => '$50',
            100 => '$100',
            500 => '$500',
            1000 => '$1,000',
            2000 => '$2,000',
            3000 => '$3,000',
            4000 => '$4,000',
            5000 => '$5,000',
        ];
    }

    /**
     * Get list of beds
     *
     * @return array
     */
    public function getListOfBeds() {
        return [
            1 => '1',
            2 => '2',
            3 => '3',
            4 => '4',
            5 => '5+',
        ];
    }

    /**
     * Get list of bathsrooms
     *
     * @return array
     */
    public function getListOfBaths() {
        return [
            '1' => '1',
            '1.5' => '1.5',
            '2' => '2',
            '2.5' => '2.5',
            '3' => '3+',
        ];
    }

    /**
     * Estimation Import From Csv
     *
     * @param User         $user
     * @param UploadedFile $file
     *
     * @return array
     */
    public function getEstimationImportFromCsv(User $user, UploadedFile $file) {
        $result = [
            'total' => 0,
            'properties' => []
        ];

        $used = [];

        $cityRepository = $this->em->getRepository('ErpCoreBundle:City');
        $propertyRepository = $this->em->getRepository('ErpPropertyBundle:Property');

        $csv = $file->getRealPath();

        $config = new LexerConfig();
        $config->setDelimiter(';')
                ->setToCharset('UTF-8')
        ;

        $listOfBeds = $this->container->get('erp.property.service')->getListOfBeds();
        $listOfBaths = $this->container->get('erp.property.service')->getListOfBaths();

        $interpreter = new Interpreter();
        $interpreter->addObserver(function (array $data) use (
                $listOfBeds,
                $listOfBaths,
                $cityRepository,
                $propertyRepository,
                $user,
                &$result,
                &$used
                ) {
            $city = $cityRepository->findOneBy(
                    [
                        'name' => ucfirst(trim($data[2])),
                        'stateCode' => strtoupper(trim($data[1]))
                    ]
            );

            $price = (int) $data[5];
            $existProperty = $propertyRepository->findBy(['name' => $data[0], 'city' => $city]);
            if ($city instanceof City && !$existProperty && $price > 0) {
                $property = new Property();

                if ($user->hasRole(User::ROLE_MANAGER)) {
                    $property->setUser($user);
                } elseif ($user->hasRole(User::ROLE_LANDLORD)) {
                    $property->setLandlordUser($user);
                }

                $property
                        ->setName($data[0])
                        ->setCity($city)
                        ->setStateCode($city->getStateCode())
                        ->setAddress($data[3])
                        ->setZip($data[4])
                        ->setPrice($data[5])
                        ->setOfBeds(in_array($data[6], $listOfBeds) ? array_search($data[6], $listOfBeds) : null)
                        ->setOfBaths(in_array($data[7], $listOfBaths) ? array_search($data[7], $listOfBaths) : null)
                        ->setSquareFootage($data[8])
                        ->setAmenities($data[9])
                        ->setAboutProperties($data[10])
                        ->setAdditionalDetails($data[11])
                        ->setStatus(Property::STATUS_DRAFT);

                $usedUniqueName = $property->getName() . '_' . $city->getStateCode() . '_' . $city->getName();
                if (!in_array($usedUniqueName, $used)) {
                    $result['properties'][] = $property;
                }

                $used[] = $usedUniqueName;
            }

            $result['total'] ++;
        });

        $lexer = new Lexer($config);
        $lexer->parse($csv, $interpreter);

        $result['total'] --;

        return $result;
    }

    /**
     * @param Property $property
     */
    public function detachTenant(Property $property) {
        /** @var Property $property */
        $property = $this->em->getRepository('ErpPropertyBundle:Property')
                ->find($property->getId());

        $property->setTenantUser(null);
        $property->setStatus(Property::STATUS_DRAFT);

        $this->em->persist($property);
        $this->em->flush();
    }

}
