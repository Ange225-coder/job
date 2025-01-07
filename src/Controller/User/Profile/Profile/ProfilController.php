<?php

    namespace App\Controller\User\Profile\Profile;

    use Symfony\Component\Routing\Annotation\Route;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Security\Http\Attribute\IsGranted;

    class ProfilController extends AbstractController
    {
        #[Route(path: '/user/profile', name: 'user_profile')]
        #[IsGranted('ROLE_USER')]
        public function profile(): Response
        {
            return $this->render('user/profile/profile/profil.html.twig');
        }
    }