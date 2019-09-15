<?php



use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @ORM\Table(name="post", indexes={@ORM\Index(name="docusuario", columns={"docusuario"})})
 * @ORM\Entity
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idpost", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idpost;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=50, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="cuerpo", type="string", length=500, nullable=false)
     */
    private $cuerpo;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="docusuario", referencedColumnName="documento")
     * })
     */
    private $docusuario;


    /**
     * Get idpost
     *
     * @return integer 
     */
    public function getIdpost()
    {
        return $this->idpost;
    }

    /**
     * Set titulo
     *
     * @param string $titulo
     * @return Post
     */
    public function setTitulo($titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo
     *
     * @return string 
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set cuerpo
     *
     * @param string $cuerpo
     * @return Post
     */
    public function setCuerpo($cuerpo)
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    /**
     * Get cuerpo
     *
     * @return string 
     */
    public function getCuerpo()
    {
        return $this->cuerpo;
    }

    /**
     * Set docusuario
     *
     * @param \Usuario $docusuario
     * @return Post
     */
    public function setDocusuario(\Usuario $docusuario = null)
    {
        $this->docusuario = $docusuario;

        return $this;
    }

    /**
     * Get docusuario
     *
     * @return \Usuario 
     */
    public function getDocusuario()
    {
        return $this->docusuario;
    }
}
