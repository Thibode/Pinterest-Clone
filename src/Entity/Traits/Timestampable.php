<?php


namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait Timestampable
{
     
    #[ORM\Column(name: 'created_at', type: 'datetime')]
    private $createdAt;

    
    #[ORM\Column(name: 'update_at', type: 'datetime')]
    private $updateAt;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTimestamps()
    {
        if ($this->getCreatedAt() === null){

            $this->setCreatedAt(new \DateTimeImmutable);
        }

        $this->setUpdateAt(new \DateTimeImmutable);
    }
}