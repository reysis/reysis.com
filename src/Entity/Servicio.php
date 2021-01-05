<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\DTO\MediaObject\MediaObjectInput;
use App\Repository\ServicioRepository;
use ApiPlatform\Core\Annotation\ApiProperty;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     iri="http://schema.org/Service",
 *     collectionOperations={
 *          "get" = {"accessControl" = "is_granted('IS_AUTHENTICATED_ANOUNYMOUSLY')"},
 *          "post" = {
 *                  "security_post_denormalize"="is_granted('POST', object)",
 *                  "security_post_denormalize_message"="Solo un Administrador puede crear Tipos de Servicios",
<<<<<<< HEAD
 *                  "attributes"={}
=======
>>>>>>> 86e250389ab11f18b2c4bc89a904ec9d2aa02f5b
 *          }
 *     },
 *     itemOperations={
 *          "get" = {"accessControl" = "is_granted('ROLE_ADMIN')"},
 *          "put" = {"accessControl" = "is_granted('ROLE_ADMIN')"},
 *          "delete" ={"accessControl" = "is_granted('ROLE_ADMIN')"}
 *      },
 *     attributes={
 *          "pagination_items_per_page" = 12
 *     },
 * )
 * @ORM\Entity(repositoryClass=ServicioRepository::class)
 * @Vich\Uploadable
 */
class Servicio
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
<<<<<<< HEAD
     * @Groups({"servicio:read"})
=======
     * @Groups({"servicio:read", "admin:write"})
>>>>>>> 86e250389ab11f18b2c4bc89a904ec9d2aa02f5b
     * @Assert\NotBlank
     */
    private $nombre;

    /**
     * @var string una descripción completa del servicio
     * 
     * @ORM\Column(type="text")
     * @ApiProperty(iri="http://schema.org/description")
<<<<<<< HEAD
     * @Groups({"servicio:item:get"})
=======
     * @Groups({"servicio:item:get", "admin:write"})
>>>>>>> 86e250389ab11f18b2c4bc89a904ec9d2aa02f5b
     * @Assert\NotBlank
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=OrdenServicio::class, mappedBy="servicio", orphanRemoval=true)
     * @Groups({"admin:read", "admin:write"})
     */
    private $servicioPrestado;

    /**
     * Ultia fecha en la que se actualizó la imagen
     *
     * @ORM\Column(type="datetime")
     */
    private $updatedAt;

    /**
<<<<<<< HEAD
     * @var MediaObject
     * @ORM\OneToOne(targetEntity=MediaObject::class, inversedBy="servicio", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"servicio:read"})
     * @Assert\NotBlank
=======
     * @ApiProperty(
     *     readableLink=true,
     *     writableLink=true
     * )
     *
     * @var MediaObject
     * @Groups({"admin:write", "servicio:read"})
     * @ORM\OneToOne(targetEntity=MediaObject::class, inversedBy="servicio", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
>>>>>>> 86e250389ab11f18b2c4bc89a904ec9d2aa02f5b
     */
    private $serviceImage;

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection|OrdenServicio[]
     */
    public function getServicioPrestado(): Collection
    {
        return $this->servicioPrestado;
    }

    public function addServicioPrestado(OrdenServicio $servicio): self
    {
        if (!$this->servicioPrestado->contains($servicio)) {
            $this->servicioPrestado[] = $servicio;
            $servicio->setTipoServicio($this);
        }

        return $this;
    }

    public function removeServicioPrestado(OrdenServicio $servicio): self
    {
        if ($this->servicioPrestado->contains($servicio)) {
            $this->servicioPrestado->removeElement($servicio);
            // set the owning side to null (unless already changed)
            if ($servicio->getTipoServicio() === $this) {
                $servicio->setTipoServicio(null);
            }
        }

        return $this;
    }

    /**
     * @Groups({"servicio:read"})
     */
    public function getShortDescription(): ?string
    {
        if(strlen($this->descripcion) < 70){
            return $this->descripcion;
        }
        return substr($this->descripcion, 0, 70).'...';
    }

<<<<<<< HEAD
    /**
     * @return MediaObject
     */
    public function getServiceImage(): MediaObject
    {
        return $this->serviceImage;
    }

    public function setServiceImage(MediaObject $serviceImage): self
    {
=======
    public function getServiceImage(): ?MediaObject
    {
        return $this->serviceImage;
    }

    public function setServiceImage(MediaObject $serviceImage): self
    {
>>>>>>> 86e250389ab11f18b2c4bc89a904ec9d2aa02f5b
        $this->serviceImage = $serviceImage;

        return $this;
    }
}
