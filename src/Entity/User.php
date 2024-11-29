<?php

    namespace App\Entity;

    use App\Repository\UserRepository;
    use Doctrine\ORM\Mapping as ORM;
    use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
    use Symfony\Component\Security\Core\User\UserInterface;

    #[ORM\Entity(repositoryClass: UserRepository::class)]
    #[ORM\Table(name: '`user`')]
    class User implements UserInterface, PasswordAuthenticatedUserInterface
    {
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column]
        private ?int $id = null;

        #[ORM\Column(length: 180, unique: true)]
        private ?string $email = null;

        #[ORM\Column(length: 180)]
        private ?string $firstName = null;

        #[ORM\Column(length: 180)]
        private ?string $lastName = null;

        #[ORM\Column]
        private ?\DateTimeImmutable $dateOfBirth = null;

        #[ORM\Column]
        private array $roles = [];

        #[ORM\Column]
        private ?string $password = null;

        #[ORM\Column(nullable: true)]
        private ?string $gender = null;

        #[ORM\Column(nullable: true)]
        private ?string $phone = null;

        #[ORM\Column(nullable: true)]
        private ?string $address = null;

        #[ORM\Column(nullable: true)]
        private ?array $mainObjectives = null;

        #[ORM\Column(nullable: true)]
        private ?string $currentProfessionalSituation = null;



        //setters
        public function setEmail(string $email): static
        {
            $this->email = $email;

            return $this;
        }

        public function setFirstName(?string $firstName): void
        {
            $this->firstName = $firstName;
        }

        public function setLastName(?string $lastName): void
        {
            $this->lastName = $lastName;
        }

        public function setDateOfBirth(?\DateTimeImmutable $dateOfBirth): void
        {
            $this->dateOfBirth = $dateOfBirth;
        }

        public function setRoles(array $roles): static
        {
            $this->roles = $roles;

            return $this;
        }

        public function setPassword(string $password): static
        {
            $this->password = $password;

            return $this;
        }

        public function setGender(?string $gender): void
        {
            $this->gender = $gender;
        }

        public function setAddress(?string $address): void
        {
            $this->address = $address;
        }

        public function setCurrentProfessionalSituation(?string $currentProfessionalSituation): void
        {
            $this->currentProfessionalSituation = $currentProfessionalSituation;
        }

        public function setMainObjectives(?array $mainObjectives): void
        {
            $this->mainObjectives = $mainObjectives;
        }

        public function setPhone(?string $phone): void
        {
            $this->phone = $phone;
        }



        //getters
        public function getId(): ?int
        {
            return $this->id;
        }

        public function getEmail(): ?string
        {
            return $this->email;
        }

        public function getFirstName(): ?string
        {
            return $this->firstName;
        }

        public function getLastName(): ?string
        {
            return $this->lastName;
        }

        public function getDateOfBirth(): ?\DateTimeImmutable
        {
            return $this->dateOfBirth;
        }

        public function getUserIdentifier(): string
        {
            return (string) $this->email;
        }

        public function getRoles(): array
        {
            $roles = $this->roles;
            // guarantee every user at least has ROLE_USER
            $roles[] = 'ROLE_USER';

            return array_unique($roles);
        }

        public function getPassword(): ?string
        {
            return $this->password;
        }

        public function getAddress(): ?string
        {
            return $this->address;
        }

        public function getCurrentProfessionalSituation(): ?string
        {
            return $this->currentProfessionalSituation;
        }

        public function getGender(): ?string
        {
            return $this->gender;
        }

        public function getMainObjectives(): ?array
        {
            return $this->mainObjectives;
        }

        public function getPhone(): ?string
        {
            return $this->phone;
        }

        public function eraseCredentials(): void
        {
            // If you store any temporary, sensitive data on the user, clear it here
            // $this->plainPassword = null;
        }
    }
