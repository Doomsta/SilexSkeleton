<?php



namespace App\Model\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Group
 * @package App\Model\Entity
 * @Entity
 * @HasLifecycleCallbacks
 */
class Group extends BaseEntity
{
    /**
     * @Column(type="integer")
     * @Id
     * @GeneratedValue
     */
    protected $id;
    /**
     * @ManyToMany(targetEntity="User", mappedBy="groups")
     **/
    private $users;

    /**
     * @ManyToMany(targetEntity="Role")
     */
    private $roles;

    /**
     * @Column(name="name", type="string", length=20, unique=true)
     */
    private $name;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
} 