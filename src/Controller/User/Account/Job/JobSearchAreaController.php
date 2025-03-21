<?php

    namespace App\Controller\User\Account\Job;

    use App\Entity\JobAndAlternation;
    use App\Entity\User;
    use App\Form\Fields\Users\Account\Job\JobSearchAreaFields;
    use App\Form\Types\Users\Account\Job\JobSearchAreaType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class JobSearchAreaController extends AbstractController
    {
        public function __construct(
            private readonly RequestStack $requestStack,
            private readonly EntityManagerInterface $entityManager
        ){}



        #[Route(path: '/account/job-preference/job-search-area', name: 'account_job_preference_search_area')]
        #[IsGranted('ROLE_USER')]
        public function jobSearchArea(): Response
        {
            $user = $this->getUser();

            if(!$user instanceof User) {
                throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette page');
            }

            $jobAreaEntity = $user->getJobAndAlternation() ?? new JobAndAlternation();

            $jobAreaFields = new JobSearchAreaFields();
            if($user->getJobAndAlternation() !== null) {
                $jobAreaFields->setJobArea($user->getJobAndAlternation()->getEmploymentArea());
            }
            else {
                $jobAreaFields->setJobArea('');
            }


            $jobAreaForm = $this->createForm(JobSearchAreaType::class, $jobAreaFields);
            $jobAreaForm->handleRequest($this->requestStack->getCurrentRequest());

            if($jobAreaForm->isSubmitted() && $jobAreaForm->isValid()) {
                // connect entities
                $user->setJobAndAlternation($jobAreaEntity);
                $jobAreaEntity->setUser($user);

                $jobAreaEntity->setEmploymentArea($jobAreaFields->getJobArea());

                $this->entityManager->persist($jobAreaEntity);
                $this->entityManager->flush();

                // Make redirect to user profil if it from to user profile
                if($this->requestStack->getCurrentRequest()->query->get('redirect') === 'user_profile_view_as_recruiter') {
                    $this->addFlash('job_search_area_saved', 'Information sauvegardée');
                    return $this->redirectToRoute('user_profile_view_as_recruiter');
                }

                $this->addFlash('jobArea_saved', 'Information sauvegardée');

                return $this->redirectToRoute('account_job_preference');
            }

            return $this->render('user/account/job/jobSearchArea.html.twig', [
                'jobAreaForm' => $jobAreaForm->createView(),
            ]);
        }
    }