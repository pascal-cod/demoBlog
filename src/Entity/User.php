<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 * fields = {"email"},
 * message="Un compte est déja existant a cette adresse Email !!"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * message="Un compte est deja exsitant a cette adresse Email !!"
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="8", minMessage="Votre mot de passe dois faire minimum 8 caracteres")
     * @Assert\EqualTo(propertyPath="confirm_password", message="Les mots de passe ne corespondent pas")
     */
    private $password;

    /**
     * @Assert\EqualTo(propertyPath="password", message="Les mots de passe ne corespondent pas")
     */
    public $confirm_password;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /*  
        Pour savoir encoder le mot de passe, il faut que notre classe (entité) User implémenté de la classe UserInterface
        Il faut absolument déclarer les méthodes getRoles(), getSalt(), eraseCredentials(), getUserName(), getPassword()
    */

    // Cette méthode est uniquement destinée a nettoyer les mots de passes en texte brut éventuellement stockés
    public function eraseCredentials()
    {
        
    }

    // renvoie la chaine de caracteres non encodé que l utilisateur a saisi, qui a été utilisé a l'origin pour coder le mot de passe
    public function getSalt()
    {
        
    }

    // Cette méthode renvoi un tableau de chaine de caracteres ou sont stockés les roles accordés a l'utilisateur
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
}
