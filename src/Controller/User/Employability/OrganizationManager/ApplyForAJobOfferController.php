<?php

    namespace App\Controller\User\Employability\OrganizationManager;

    use App\Entity\Applicant;
    use App\Entity\JobOffers;
    use App\Entity\User;
    use App\Form\Fields\Users\Employability\OrganizationManager\ApplyForAJobOfferFields;
    use App\Form\Types\Users\Employability\OrganizationManager\ApplyForAJobOfferType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class ApplyForAJobOfferController extends AbstractController
    {
        public function __construct(
            private readonly EntityManagerInterface $entityManager,
            private readonly RequestStack $requestStack,
        ){}


        #[Route(path: '/organization/job-offer/apply/{id}', name: 'organization_job_offer_apply')]
        #[IsGranted('ROLE_USER')]
        public function applyForAJobOffer(int $id): Response
        {
            // Get a job offer based on his id
            $jobOffer = $this->entityManager->getRepository(JobOffers::class)->find($id);

            $user = $this->getUser();

            if(!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette offre');
            }

            $applyJobOfferFields = new ApplyForAJobOfferFields();
            $applyJobOfferFields->setFirstName($user->getFirstName());
            $applyJobOfferFields->setLastName($user->getLastName());
            $applyJobOfferFields->setEmail($user->getEmail());
            $applyJobOfferFields->setPhone($user->getPhone() ?? '');

            $applicantEntity = new Applicant();

            $applyJobOfferForm = $this->createForm(ApplyForAJobOfferType::class, $applyJobOfferFields);
            $applyJobOfferForm->handleRequest($this->requestStack->getCurrentRequest());

            if($applyJobOfferForm->isSubmitted() && $applyJobOfferForm->isValid()) {
                // Connect entities
                $user->setApplicant($applicantEntity);
                $applicantEntity->setUser($user);

                $applicantEntity->setEmail($applyJobOfferFields->getEmail());
                $applicantEntity->setPhone($applyJobOfferFields->getPhone());
                $applicantEntity->setLastName($applyJobOfferFields->getLastName());
                $applicantEntity->setFirstName($applyJobOfferFields->getFirstName());

                // Docs to provide manager
                $docsFiles = $applyJobOfferFields->getDocsToProvide();
                $docsToProvide = $applicantEntity->getDocsToProvide() ?? [];

                foreach ($docsFiles as $doc) {
                    // manage each file
                    $destination = $this->getParameter('user/employability/docs');
                    $fileName = uniqid(). '.' .$doc->guessExtension();
                    $doc->move($destination, $fileName);

                    $docsToProvide[] = $destination. '/' .$fileName;
                }

                $applicantEntity->setDocsToProvide($docsToProvide);

                $this->entityManager->persist($applicantEntity);
                $this->entityManager->flush();

                $this->addFlash('candidacy_send_successfully', 'Votre candidature a été envoyé avec succès');

                return $this->redirectToRoute('home');
            }

            return $this->render('user/employability/organizationManager/applyForAJobOffer.html.twig', [
                'job_offer' => $jobOffer,
                'apply_job_offer_form' => $applyJobOfferForm->createView(),
            ]);
        }
    }