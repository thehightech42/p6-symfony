<?php

namespace App\Entity;

use App\Repository\VisuelFigureRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VisuelFigureRepository::class)
 */
class VisuelFigure
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Figure::class, inversedBy="visuelFigures")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $figure;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFigure(): ?Figure
    {
        return $this->figure;
    }

    public function setFigure(?Figure $figure): self
    {
        $this->figure = $figure;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function typeAndMove(string $type, $file, $pathDirectory = null)
    {
        // On ajoute le type
        $this->setType($type);

        if($type === 'picture'){
            // On essaie de déplacer le fichier.
            try{
                $newPathAndNameFile = 'uploads/img/picture-' . md5( uniqid() ) . '.' .$file->guessExtension();
                $file->move($pathDirectory .'/uploads/img', $newPathAndNameFile);
                $this->setUrl($newPathAndNameFile);
                return $this;
            }catch (FileException $e) {
                return false;
            }
        }else if($type === 'video'){
            
            $id = explode("=", $file);
            $newLinkVideo = "https://www.youtube.com/embed/".end($id);
            $this->setUrl($newLinkVideo);
            return $this;
        }
        

    }
}
