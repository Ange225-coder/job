<?php

    namespace App\DataFixtures;

    use App\Entity\Formation;
    use App\Entity\User;
    use App\Enum\User\Account\Career\Formation\DiplomaSpeciality;
    use Doctrine\Bundle\FixturesBundle\Fixture;
    use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
    use Doctrine\Persistence\ObjectManager;

    class FormationsFixtures extends Fixture implements FixtureGroupInterface
    {
        public function load(ObjectManager $manager): void
        {
            //get all users form User table
            $users = $manager->getRepository(User::class)->findAll();

            foreach ($users as $user) {
                $formation = new Formation();

                $formation->setUser($user);
                $formation->setDiplomaLevel('BAC+3');
                $formation->setDiplomaName('MASTER');
                $formation->setDiplomaSpeciality(DiplomaSpeciality::COMPUTER_SCIENCE);
                $formation->setUniversityName('Université de HARVARD');
                $formation->setDiplomaTown('Abidjan');
                //$formation->setDiplomaMonth(new \DateTimeImmutable('2000-03-01'));
                $formation->setDiplomaYear('2003');

                $manager->persist($formation);
            }

            $manager->flush();
        }


        public static function getGroups(): array
        {
            return ['formation_fixtures'];
        }
    }