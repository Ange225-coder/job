<?php

    namespace App\Controller\User\UserPanel;

    use App\Entity\User;
    use App\Form\Fields\Users\UserPanel\MainObjectivesFields;
    use App\Form\Types\Users\UserPanel\MainObjectivesType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class MainObjectivesController extends AbstractController
    {
        private RequestStack $requestStack;
        private EntityManagerInterface $entityManager;


        public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
        {
            $this->requestStack = $requestStack;
            $this->entityManager = $entityManager;
        }


        #[Route(path: '/welcome', name: 'welcome')]
        #[IsGranted('ROLE_USER')]
        public function mainObjectives(): Response
        {
            $user = $this->getUser();

            if(!$user instanceof User) {
                throw $this->createAccessDeniedException('Utilisateur invalide');
            }

            $objectifFields = new MainObjectivesFields();

            $objectifForm = $this->createForm(MainObjectivesType::class, $objectifFields);

            $request = $this->requestStack->getCurrentRequest();
            $objectifForm->handleRequest($request);

            if($objectifForm->isSubmitted() && $objectifForm->isValid()) {
                //get current user
                $user = $this->entityManager->getRepository(User::class)->findOneBy([
                    'email' => $this->getUser()->getUserIdentifier(),
                ]);

                // if user is saved in database, add main objectives
                // for this user
                if($user) {
                    $user->setMainObjectives($objectifFields->getMainObjectives());
                    $user->setFieldsOfInterest($objectifFields->getFields());
                }

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                return $this->redirectToRoute('user_dashboard');
            }

            return $this->render('user/userPanel/mainObjectives.html.twig', [
                'mainObjectivesForm' => $objectifForm->createView(),
            ]);
        }
    }