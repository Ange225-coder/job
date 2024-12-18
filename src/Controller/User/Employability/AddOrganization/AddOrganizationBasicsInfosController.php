<?php

    namespace App\Controller\User\Employability\AddOrganization;

    use App\Entity\Organization;
    use App\Form\Fields\Users\Employability\AddOrganization\OrganizationBasicsInfosFields;
    use App\Form\Types\Users\Employability\AddOrganization\OrganizationBasicsInfosType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class AddOrganizationBasicsInfosController extends AbstractController
    {
        private RequestStack $requestStack;
        private EntityManagerInterface $entityManager;


        public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
        {
            $this->entityManager = $entityManager;
            $this->requestStack = $requestStack;
        }


        #[Route(path: '/organization/add', name: 'organization_add')]
        #[IsGranted('ROLE_USER')]
        public function addOrganization(): Response
        {
            $user = $this->getUser();

            $organizationAddFields = new OrganizationBasicsInfosFields();
            $organizationEntity = $user->getOrganization() ?? new Organization();

            $organizationAddType = $this->createForm(OrganizationBasicsInfosType::class, $organizationAddFields);

            $organizationAddType->handleRequest($this->requestStack->getCurrentRequest());

            if($organizationAddType->isSubmitted() && $organizationAddType->isValid()) {
                // connect entities
                $user->setOrganization($organizationEntity);
                $organizationEntity->setUser($user);

                $organizationEntity->setCountry($organizationAddFields->getCountry());
                $organizationEntity->setOrganizationName($organizationAddFields->getOrganizationName());
                $organizationEntity->setOrganizationRegistrationNumber($organizationAddFields->getOrganizationRegistrationNumber());

                $this->entityManager->persist($organizationEntity);
                $this->entityManager->flush();

                return $this->redirectToRoute('organisation_employer_phone_number');
            }

            return $this->render('user/employability/addOrganization/addOrganizationBasicsInfos.html.twig', [
                'add_organization_form' => $organizationAddType->createView(),
            ]);
        }
    }