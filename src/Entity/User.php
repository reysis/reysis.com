<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumn;

/**
 * Una persona natural o una empresa
 * 
 * @see http://schema.org/Person Documentation on Schema.org
 * 
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ApiResource(
 *      iri="http://schema.org/Person",
 *      collectionOperations={
 *          "get" = {"accessControl" = "is_granted('ROLE_ADMIN')"},
 *          "post" = {
 *              "accessControl" = "is_granted('IS_AUTHENTICATED_ANOUNYMOUSLY')",
 *              "validation_groups"={"Default", "create"}
 *          }
 *      },
 *      itemOperations={
 *          "get" = {"accessControl" = "is_granted('ROLE_USER') and object == user"},
 *          "put" = {"accessControl" = "is_granted('ROLE_USER') and object == user"},
 *          "delete" ={"accessControl" = "is_granted('ROLE_ADMIN')"}
 *      },
 * )
 * @ApiFilter(PropertyFilter::class)
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "username":"partial",
 *          "tipoUsuario":"exact"
 *      }
 * )
 * @UniqueEntity("username")
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
     * @ORM\Column(type="string", length=180, unique=true)
     * @ORM\OneToOne(targetEntity=Persona::class, cascade={"persist", "remove"})
     * @Groups({"user:read", "user:write", "turno:write"})
     * @Assert\NotBlank(groups={"create"})
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     * @Groups({"admin:read","admin:write"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * Una variable temporal para almacenar la password y poder encriptarla en el proceso de normalización
     *
     * @var string The plain password
     * @Groups({"user:write", "turno:write"})
     * @Assert\NotBlank(groups={"create"})
     * @SerializedName("password")
     */
    private $plainPassword;

    /**
     * @ApiProperty(iri="http://schema.org/email")
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Email()
     * @Groups({"user:read", "user:write", "turno:write"})
     */
    private $email;

    /**
     * Retorna verdadero si este es el usuario autenticado actualmente
     *
     * @Groups({"user:read"})
    */
    private $isMe = false;

    /**
     * @ORM\OneToMany(targetEntity=CuentaBancaria::class, mappedBy="user", orphanRemoval=true)
     */
    protected $cuentaBancaria;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateRegistered;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastEdited;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoggued;

    /**
     * @ORM\ManyToOne(targetEntity=Address::class, inversedBy="users", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"user:write", "owner:read","turno:write"})
     */
    private $address;

    /**
     * @ORM\ManyToMany(targetEntity=PhoneNumber::class)
     * @JoinTable(name="users_phonenumbers",
     *      joinColumns={@JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@JoinColumn(name="phonenumber_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $phoneNumbers;

    /**
     * @ORM\OneToOne(targetEntity=Empresa::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $empresa;

    /**
     * @ORM\OneToOne(targetEntity=Persona::class, mappedBy="user", cascade={"persist", "remove"})
     */
    private $persona;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nationality;

    /**
     * @ORM\OneToOne(targetEntity=Contrato::class, inversedBy="user", cascade={"persist", "remove"})
     */
    private $contrato;

    /**
     * @ORM\OneToMany(targetEntity=OrdenServicio::class, mappedBy="user")
     */
    private $serviceOrder;

    /**
     * @ORM\OneToMany(targetEntity=Turno::class, mappedBy="user", orphanRemoval=true)
     */
    private $turnos;

    public function __construct()
    {
        $this->cuentaBancaria = new ArrayCollection();
        $this->phoneNumbers = new ArrayCollection();
        $this->serviceOrder = new ArrayCollection();
        $this->turnos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;

        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    /**
     * Get the plain password
     *
     * @return  string
     */ 
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * Set the plain password
     *
     * @param  string  $plainPassword  The plain password
     *
     * @return  self
     */ 
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getIsMe(): bool
    {
        return $this->isMe;
    }

    public function setIsMe(bool $isMe): void
    {
        $this->isMe = $isMe;
    }

    /**
     * @return Collection|CuentaBancaria[]
     */
    public function getCuentaBancaria(): Collection
    {
        return $this->cuentaBancaria;
    }

    public function addCuentaBancarium(CuentaBancaria $cuentaBancarium): self
    {
        if (!$this->cuentaBancaria->contains($cuentaBancarium)) {
            $this->cuentaBancaria[] = $cuentaBancarium;
            $cuentaBancarium->setUser($this);
        }

        return $this;
    }

    public function removeCuentaBancarium(CuentaBancaria $cuentaBancarium): self
    {
        if ($this->cuentaBancaria->contains($cuentaBancarium)) {
            $this->cuentaBancaria->removeElement($cuentaBancarium);
            // set the owning side to null (unless already changed)
            if ($cuentaBancarium->getUser() === $this) {
                $cuentaBancarium->setUser(null);
            }
        }

        return $this;
    }

    public function getDateRegistered(): ?\DateTimeInterface
    {
        return $this->dateRegistered;
    }

    public function setDateRegistered(\DateTimeInterface $dateRegistered): self
    {
        $this->dateRegistered = $dateRegistered;

        return $this;
    }

    public function getLastEdited(): ?\DateTimeInterface
    {
        return $this->lastEdited;
    }

    public function setLastEdited(\DateTimeInterface $lastEdited): self
    {
        $this->lastEdited = $lastEdited;

        return $this;
    }

    public function getLastLoggued(): ?\DateTimeInterface
    {
        return $this->lastLoggued;
    }

    public function setLastLoggued(\DateTimeInterface $lastLoggued): self
    {
        $this->lastLoggued = $lastLoggued;

        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return Collection|PhoneNumber[]
     */
    public function getPhoneNumbers(): Collection
    {
        return $this->phoneNumbers;
    }

    public function addPhoneNumber(PhoneNumber $phoneNumber): self
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers[] = $phoneNumber;
        }

        return $this;
    }

    public function removePhoneNumber(PhoneNumber $phoneNumber): self
    {
        if ($this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->removeElement($phoneNumber);
        }

        return $this;
    }

    public function getEmpresa(): ?Empresa
    {
        return $this->empresa;
    }

    public function setEmpresa(?Empresa $empresa): self
    {
        $this->empresa = $empresa;

        return $this;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): self
    {
        $this->persona = $persona;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    public function getContrato(): ?Contrato
    {
        return $this->contrato;
    }

    public function setContrato(?Contrato $contrato): self
    {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * @return Collection|OrdenServicio[]
     */
    public function getServiceOrder(): Collection
    {
        return $this->serviceOrder;
    }

    public function addServiceOrder(OrdenServicio $serviceOrder): self
    {
        if (!$this->serviceOrder->contains($serviceOrder)) {
            $this->serviceOrder[] = $serviceOrder;
            $serviceOrder->setUser($this);
        }

        return $this;
    }

    public function removeServiceOrder(OrdenServicio $serviceOrder): self
    {
        if ($this->serviceOrder->contains($serviceOrder)) {
            $this->serviceOrder->removeElement($serviceOrder);
            // set the owning side to null (unless already changed)
            if ($serviceOrder->getUser() === $this) {
                $serviceOrder->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Turno[]
     */
    public function getTurnos(): Collection
    {
        return $this->turnos;
    }

    public function addTurno(Turno $turno): self
    {
        if (!$this->turnos->contains($turno)) {
            $this->turnos[] = $turno;
            $turno->setUser($this);
        }

        return $this;
    }

    public function removeTurno(Turno $turno): self
    {
        if ($this->turnos->contains($turno)) {
            $this->turnos->removeElement($turno);
            // set the owning side to null (unless already changed)
            if ($turno->getUser() === $this) {
                $turno->setUser(null);
            }
        }

        return $this;
    }
}
