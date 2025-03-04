<?php

   namespace App\Form\Fields\Users\Employability\OrganizationManager;

   use Symfony\Component\Validator\Constraints as Assert;

   class ApplyForAJobOfferFields
   {
       #[Assert\NotBlank]
       private ?string $lastName = null;

       #[Assert\NotBlank]
       private ?string $firstName = null;

       #[Assert\NotBlank]
       private ?string $email = null;

       #[Assert\NotBlank]
       #[Assert\Regex(
           pattern: '/^((07|05|01)\d{8})$/',
           message: 'Entrer un numéro de téléphone ivoirien valide'
       )]
       private ?string $phone = null;

       #[Assert\NotBlank]
       /**
        * #[Assert\File(
        * maxSize: '5M',
        * mimeTypes: ['application/pdf'],
        * maxSizeMessage: 'Les fichiers doivent être inférieur à 5 Mo',
        * mimeTypesMessage: 'Seul les fichiers PDF sont autorisés.'
        * )]
        */

       private ?array $docsToProvide = null;



       //setters

       /**
        * @param string|null $lastName
        */
       public function setLastName(?string $lastName): void
       {
           $this->lastName = $lastName;
       }

       public function setFirstName(?string $firstName): void
       {
           $this->firstName = $firstName;
       }


       public function setEmail(?string $email): void
       {
           $this->email = $email;
       }


       public function setPhone(?string $phone): void
       {
           $this->phone = $phone;
       }


       public function setDocsToProvide(?array $docsToProvide): void
       {
           $this->docsToProvide = $docsToProvide;
       }




       //getters
       public function getLastName(): ?string
       {
           return $this->lastName;
       }

       public function getFirstName(): ?string
       {
           return $this->firstName;
       }

       public function getEmail(): ?string
       {
           return $this->email;
       }

       public function getPhone(): ?string
       {
           return $this->phone;
       }

       public function getDocsToProvide(): ?array
       {
           return $this->docsToProvide;
       }
   }
