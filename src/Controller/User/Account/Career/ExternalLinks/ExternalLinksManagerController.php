<?php

    namespace App\Controller\User\Account\Career\ExternalLinks;

    use App\Entity\Career;
    use App\Form\Fields\Users\Account\Career\ExternalLinks\ExternalLinksManagerFields;
    use App\Form\Types\Users\Account\Career\ExternalLinks\ExternalLinksManagerType;
    use Doctrine\ORM\EntityManagerInterface;
    use Symfony\Component\HttpFoundation\RequestStack;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class ExternalLinksManagerController extends AbstractController
    {
        private EntityManagerInterface $entityManager;
        private RequestStack $requestStack;


        public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
        {
            $this->requestStack = $requestStack;
            $this->entityManager = $entityManager;
        }


        #[Route(path: '/account/external-link/link', name: 'account_external_links_link')]
        #[IsGranted('ROLE_USER')]
        public function externalLinksManager(): Response
        {
            $user = $this->getUser();

            $externalLinkFields = new ExternalLinksManagerFields();
            $careerEntity = $user->getCareer() ?? new Career();

            $externalLinkType = $this->createForm(ExternalLinksManagerType::class, $externalLinkFields);

            $externalLinkType->handleRequest($this->requestStack->getCurrentRequest());

            if($externalLinkType->isSubmitted() && $externalLinkType->isValid()) {
                // connect entities
                $careerEntity->setUser($user);
                $user->setCareer($careerEntity);

                $careerEntity->setExternalLink($externalLinkFields->getLinkedInLink() ?? $externalLinkFields->getGithubLink() ?? $externalLinkFields->getUrl());

                $this->entityManager->persist($careerEntity);
                $this->entityManager->flush();

                $this->addFlash('link_added_successfully', 'Informations sauvegardée');

                return $this->redirectToRoute('account_external_link');
            }

            return $this->render('user/account/career/externalLinks/externalLinksManager.html.twig', [
                'external_link_form' => $externalLinkType->createView(),
            ]);
        }
    }