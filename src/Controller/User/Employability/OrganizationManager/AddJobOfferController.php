<?php

    namespace App\Controller\User\Employability\OrganizationManager;

    use App\Entity\JobOffers;
    use App\Entity\User;
    use App\Form\Fields\Users\Employability\OrganizationManager\AddJobOfferFields;
    use App\Form\Types\Users\Employability\OrganizationManager\AddJobOfferType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\Form\FormError;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class AddJobOfferController extends AbstractController
    {
        public function __construct(
            private readonly RequestStack $requestStack,
            private readonly EntityManagerInterface $entityManager
        ){}


        #[Route(path: '/organization/add-job-offer', name: 'organization_add_job_offer')]
        #[IsGranted('ROLE_ENT')]
        public function addJobOffer(): Response
        {
            $user = $this->getUser();

            if(!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page');
            }

            // Get current organization
            $organization = $user->getOrganization();

            // Show a message if the subscription of the organization is free
            if ($organization->getSubscription() === 'free') {
                $this->addFlash('premium_message', 'Passer en premium pour avoir accès au service');

                return $this->redirectToRoute('organization_dashboard_preview', [
                    'id' => $organization->getId(),
                ]);
            }

            $jobOfferFields = new AddJobOfferFields();
            $jobOfferEntity = new JobOffers();

            $jobOfferForm = $this->createForm(AddJobOfferType::class, $jobOfferFields);
            $jobOfferForm->handleRequest($this->requestStack->getCurrentRequest());

            if($jobOfferForm->isSubmitted() && $jobOfferForm->isValid()) {
                // connect entities
                $organization->addJobOffer($jobOfferEntity);
                $jobOfferEntity->setOrganization($organization);

                /**
                 * Use explode() to transform strings entered into an array
                 * Here, trim allows to delete unused space during the transformation into an array
                 *
                 * This method is used with $missions, $profil, $whatWeOffer
                 */
                $missions = array_map('trim', explode(',', $jobOfferFields->getMissions()));
                $profileSought = array_map('trim', explode(',', $jobOfferFields->getProfilSought()));
                $whatWeOffer = array_map('trim', explode(',', $jobOfferFields->getWhatWeOffer()));

                $jobOfferEntity->setJobTitle($jobOfferFields->getJobTitle());
                $jobOfferEntity->setTypeOfContract($jobOfferFields->getTypeOfContract());
                $jobOfferEntity->setTown($jobOfferFields->getTown());
                $jobOfferEntity->setJobPreferences($jobOfferFields->getJobPreferences());
                $jobOfferEntity->setOrganizationAbout($jobOfferFields->getOrganizationAbout());
                $jobOfferEntity->setMissions($missions);
                $jobOfferEntity->setProfilSought($profileSought);
                $jobOfferEntity->setWhatWeOffer($whatWeOffer);
                $jobOfferEntity->setDocsToProvide($jobOfferFields->getDocsToProvide());
                $jobOfferEntity->setDateOfPublication((new \DateTimeImmutable())->setTime(0, 0, 0));
                $jobOfferEntity->setExpirationDate($jobOfferFields->getExpirationDate());
                $jobOfferEntity->setStatu(true);


                $expirationDate = $jobOfferFields->getExpirationDate()->format('Y-m-d');
                $currentDate = (new \DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d');

                // Date comparison
                if($expirationDate <= $currentDate) {
                    $jobOfferForm->get('expirationDate')->addError(new FormError('La date d\'expiration doit être supérieur à la date de publication'));

                    // If expiration date is less than or equal to current date,
                    // display error in the view
                    return $this->render('user/employability/organizationManager/addJobOffer.html.twig', [
                        'job_offer_form' => $jobOfferForm->createView(),
                        'organization' => $organization,
                    ]);
                }

                $this->entityManager->persist($jobOfferEntity);
                $this->entityManager->flush();

                $this->addFlash('add_job_offer_success', 'Une offre d\'emploi vient d\'être ajouté');

                return $this->redirectToRoute('organization_dashboard_preview', [
                    'id' => $organization->getId(),
                ]);
            }

            return $this->render('user/employability/organizationManager/addJobOffer.html.twig', [
                'job_offer_form' => $jobOfferForm->createView(),
            ]);
        }
    }