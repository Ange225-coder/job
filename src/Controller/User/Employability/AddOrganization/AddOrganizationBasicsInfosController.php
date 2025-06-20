<?php

    namespace App\Controller\User\Employability\AddOrganization;

    use App\Entity\Organization;
    use App\Entity\User;
    use App\Form\Fields\Users\Employability\AddOrganization\OrganizationBasicsInfosFields;
    use App\Form\Types\Users\Employability\AddOrganization\OrganizationBasicsInfosType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;
    use App\Security\UserAuthenticator;
    use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

    class AddOrganizationBasicsInfosController extends AbstractController
    {

        public function __construct(
            private readonly RequestStack $requestStack,
            private readonly EntityManagerInterface $entityManager,
            private readonly UserAuthenticator $authenticator,
            private readonly UserAuthenticatorInterface $userAuthenticator
        ){}


        #[Route(path: '/organization/add', name: 'organization_add')]
        #[IsGranted('ROLE_USER')]
        public function addOrganization(): Response
        {
            $user = $this->getUser();

            if(!$user instanceof User) {
                throw $this->createAccessDeniedException('Utilisateur invalid');
            }

            $organizationAddFields = new OrganizationBasicsInfosFields();
            $organizationEntity = $user->getOrganization() ?? new Organization();

            // Redirect to employer phone number if organization exist and
            // employer phone does not exist
            if($user->getOrganization() !== null && $user->getPhone() === null) {
                return $this->redirectToRoute('organisation_employer_phone_number');
            }

            // redirect to organization dashboard if a user is associated to
            // an organization and user phone exist
            if($user->getOrganization() !== null && $user->getPhone() !== null) {
                return $this->redirectToRoute('organization_dashboard_preview', [
                    'id' => $user->getOrganization()->getId()
                ]);
            }

            $organizationAddType = $this->createForm(OrganizationBasicsInfosType::class, $organizationAddFields);

            $organizationAddType->handleRequest($this->requestStack->getCurrentRequest());

            if($organizationAddType->isSubmitted() && $organizationAddType->isValid()) {
                // connect entities
                $user->setOrganization($organizationEntity);
                $organizationEntity->setUser($user);

                $organizationEntity->setTown($organizationAddFields->getTown());
                $organizationEntity->setOrganizationName($organizationAddFields->getOrganizationName());
                $organizationEntity->setOrganizationPreferences($organizationAddFields->getOrganizationPreferences());
                $organizationEntity->setSectorOfActivity($organizationAddFields->getSectorOfActivity());
                $organizationEntity->setOrganizationRegistrationNumber($organizationAddFields->getOrganizationRegistrationNumber());

                // add ROLE_ENT to the users which create new organization
                $roles = $user->getRoles();
                if (!in_array('ROLE_ENT', $roles, true)) {
                    $roles[] = 'ROLE_ENT';
                    $user->setRoles($roles);
                }

                $this->entityManager->persist($organizationEntity);
                $this->entityManager->flush();

                // re-authenticate user to add the new role
                $this->userAuthenticator->authenticateUser($user, $this->authenticator, $this->requestStack->getCurrentRequest());

                return $this->redirectToRoute('organisation_employer_phone_number');
            }

            return $this->render('user/employability/addOrganization/addOrganizationBasicsInfos.html.twig', [
                'add_organization_form' => $organizationAddType->createView(),
            ]);
        }
    }