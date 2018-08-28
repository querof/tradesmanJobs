<?php

namespace MyHammer\JobsBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="job", indexes={@ORM\Index(name="fk_service", columns={"service_id"}), @ORM\Index(name="fk_city", columns={"city_id"}), @ORM\Index(name="fk_user", columns={"user_id"})})
 * @ORM\Entity
 */
class Job
{
    /**
     * @var string
     * @Assert\Length(
     *      min = 5,
     *      max = 50,
     *      minMessage = "The min Length for a job description must be at least {{ limit }} characters long",
     *      maxMessage = "The max Length for a job description must be  {{ limit }} characters long"
     * )
     * @ORM\Column(name="title", type="string", length=50, nullable=false)
     */
    private $title;

    /**
     * @var string
     * @Assert\Length(
     *      min = 25,
     *      max = 255,
     *      minMessage = "The min Length for a job description must be at least {{ limit }} characters long",
     *      maxMessage = "The max Length for a job description must be  {{ limit }}  characters long"
     * )
     * @ORM\Column(name="description", type="string", length=255, nullable=false)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \MyHammer\JobsBundle\Entity\City
     *
     * @ORM\ManyToOne(targetEntity="MyHammer\JobsBundle\Entity\City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;

    /**
     * @var \MyHammer\JobsBundle\Entity\Service
     *
     * @ORM\ManyToOne(targetEntity="MyHammer\JobsBundle\Entity\Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    private $service;

    /**
     * @var \MyHammer\JobsBundle\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="MyHammer\JobsBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;



    /**
     * Set title
     *
     * @param string $title
     *
     * @return Job
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Job
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Job
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set city
     *
     * @param \MyHammer\JobsBundle\Entity\City $city
     *
     * @return Job
     */
    public function setCity(\MyHammer\JobsBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \MyHammer\JobsBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set service
     *
     * @param \MyHammer\JobsBundle\Entity\Service $service
     *
     * @return Job
     */
    public function setService(\MyHammer\JobsBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \MyHammer\JobsBundle\Entity\Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set user
     *
     * @param \MyHammer\JobsBundle\Entity\User $user
     *
     * @return Job
     */
    public function setUser(\MyHammer\JobsBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MyHammer\JobsBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
