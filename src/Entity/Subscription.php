<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
class Subscription
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["getSubscription"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getSubscription"])]
    #[Assert\NotBlank(message: "Le contact est obligatoire")]
    private ?Contact $contact = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["getSubscription"])]
    #[Assert\NotBlank(message: "Le produit est obligatoire")]
    private ?Product $product = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getSubscription"])]
    #[Assert\NotBlank(message: "la date de début est obligatoire")]
    private ?\DateTimeInterface $begineDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["getSubscription"])]
    #[Assert\NotBlank(message: "la date fin est obligatoire")]
    #[Assert\Expression(
        "this.getEndDate() > this.getBegineDate()",
        message: "The end date must be greater than the begin date",
    )]
    private ?\DateTimeInterface $endDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function getBegineDate(): ?\DateTimeInterface
    {
        return $this->begineDate;
    }

    public function setBegineDate(\DateTimeInterface $begineDate): static
    {
        $this->begineDate = $begineDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }
}
