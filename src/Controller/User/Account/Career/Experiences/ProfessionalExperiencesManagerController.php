<?php

    namespace App\Controller\User\Account\Career\Experiences;

    use App\Entity\Career;
    use App\Form\Fields\Users\Account\Career\Experiences\ProfessionalExperiencesManagerFields;
    use App\Form\Types\Users\Account\Career\Experiences\ProfessionalExperiencesManagerType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class ProfessionalExperiencesManagerController extends AbstractController
    {
        private RequestStack $requestStack;
        private EntityManagerInterface $entityManager;


        public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
        {
            $this->requestStack = $requestStack;
            $this->entityManager = $entityManager;
        }


        #[Route(path: '/account/professional-experiences/professional-experience', name: 'account_professional_experience_manager')]
        #[IsGranted('ROLE_USER')]
        public function professionalExperiencesManager(): Response
        {
            $user = $this->getUser();

            $experienceFields = new ProfessionalExperiencesManagerFields();
            $experienceEntity = $user->getCareer() ?? new Career();

            $experienceType = $this->createForm(ProfessionalExperiencesManagerType::class, $experienceFields);

            $experienceType->handleRequest($this->requestStack->getCurrentRequest());

            if($experienceType->isSubmitted() && $experienceType->isValid()) {
                //connect entities
                $experienceEntity->setUser($user);
                $user->setCareer($experienceEntity);

                $experienceEntity->setJobTitle($experienceFields->getJobTitle());
                $experienceEntity->setJobField($experienceFields->getJobField());
                $experienceEntity->setCountry($experienceFields->getCountry());
                $experienceEntity->setEnterpriseName($experienceFields->getEnterpriseName());
                $experienceEntity->setStartDate($experienceFields->getStartDate());
                $experienceEntity->setEndDate($experienceFields->getEndDate());
                $experienceEntity->setJobDescription($experienceFields->getJobDescription());

                $this->entityManager->persist($experienceEntity);
                $this->entityManager->flush();

                $this->addFlash('experiences_saved', 'Informations sauvegardée');

                return $this->redirectToRoute('account_professional_experience');
            }

            return $this->render('user/account/career/experiences/professionalExperiencesManager.html.twig', [
                'experience_form' => $experienceType->createView(),
            ]);
        }
    }