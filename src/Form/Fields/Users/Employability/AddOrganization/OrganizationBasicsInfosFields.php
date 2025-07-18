<?php

    namespace App\Form\Fields\Users\Employability\AddOrganization;

    use App\Entity\Organization;
    use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
    use Symfony\Component\Validator\Constraints as Assert;

    #[UniqueEntity('organizationName', message: 'Une organisation utilise déjà ce nom', entityClass: Organization::class)]
    class OrganizationBasicsInfosFields
    {
        #[Assert\NotBlank]
        private ?string $town = null;

        #[Assert\NotBlank]
        private ?string $organizationName = null;

        #[Assert\NotBlank(message: 'Ajouter une préférence pour l\'entreprise')]
        private ?array $organizationPreferences = null;

        #[Assert\NotBlank]
        private ?array $sectorOfActivity = null;

        #[Assert\Regex(
            pattern: '/^[A-Z]{2}-\d{9}-[A-Z0-9]$/',
            message: 'Le numéro d\'enregistrement doit respecter le format XX-XXXXXXXXX-Z'
        )]
        private ?string $organizationRegistrationNumber = null;


        //setters
        public function setTown(?string $town): void
        {
            $this->town = $town;
        }

        public function setOrganizationName(?string $organizationName): static
        {
            $this->organizationName = $organizationName;

            return $this;
        }

        public function setOrganizationPreferences(?array $organizationPreferences): void
        {
            $this->organizationPreferences = $organizationPreferences;
        }

        public function setSectorOfActivity(?array $sectorOfActivity): void
        {
            $this->sectorOfActivity = $sectorOfActivity;
        }

        public function setOrganizationRegistrationNumber(string $organizationRegistrationNumber): static
        {
            $this->organizationRegistrationNumber = $organizationRegistrationNumber;

            return $this;
        }



        //getters
        public function getTown(): ?string
        {
            return $this->town;
        }

        public function getOrganizationName(): ?string
        {
            return $this->organizationName;
        }

        public function getOrganizationPreferences(): ?array
        {
            return $this->organizationPreferences;
        }

        public function getSectorOfActivity(): ?array
        {
            return $this->sectorOfActivity;
        }

        public function getOrganizationRegistrationNumber(): ?string
        {
            return $this->organizationRegistrationNumber;
        }
    }
