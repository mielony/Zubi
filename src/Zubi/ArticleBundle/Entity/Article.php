<?php

namespace Zubi\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zubi\UserBundle\Entity\User;
use Zubi\UserBundle\Entity\Osoba;
use Zubi\FaqBundle\Entity\Status_widocznosci;

use Zubi\ArticleBundle\Controller\DefaultController;

/**
 * Zubi\ArticleBundle\Entity\Article
 */
class Article
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $title
     */
    private $title;

    /**
     * @var text $content
     */
    private $content;


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
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Set content
     *
     * @param text $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get content
     *
     * @return text 
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @var integer $groupId
     */
    private $groupId;

    /**
     * @var integer $statusId
     */
    private $statusId;

    /**
     * @var integer $userId
     */
    private $userId;

    /**
     * @var integer $authorId
     */
    private $authorId;


    /**
     * Set groupId
     *
     * @param integer $groupId
     */
    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }
    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set statusId
     *
     * @param integer $statusId
     */
    public function setStatusId($statusId)
    {
        $this->statusId = $statusId;
    }

    /**
     * Get statusId
     *
     * @return integer 
     */
    public function getStatusId()
    {
        return $this->statusId;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set authorId
     *
     * @param integer $authorId
     */
    public function setAuthorId($authorId)
    {
        $this->authorId = $authorId;
    }

    /**
     * Get authorId
     *
     * @return integer 
     */
    public function getAuthorId()
    {
        return $this->authorId;
    }
    /**
     * @var Zubi\ArticleBundle\Entity\Status_widocznosci
     */
    private $status_widocznosci;


    /**
     * Set status_widocznosci
     *
     * @param Zubi\ArticleBundle\Entity\Status_widocznosci $statusWidocznosci
     */
    public function setStatusWidocznosci(\Zubi\FaqBundle\Entity\Status_widocznosci $statusWidocznosci)
    {
        $this->status_widocznosci = $statusWidocznosci;
        $this->statusId = $this->status_widocznosci->getId();
    }

    /**
     * Get status_widocznosci
     *
     * @return Zubi\FaqBundle\Entity\Status_widocznosci 
     */
    public function getStatusWidocznosci()
    {
        return $this->status_widocznosci;        
    }
    /**
     * @var Zubi\ArticleBundle\Entity\Osoba
     */
    private $author;

    /**
     * Set autor
     *
     * @param Zubi\UserBundle\Entity\Osoba $author
     */
    public function setAuthor(\Zubi\UserBundle\Entity\Osoba $author)
    {
        $this->author = $author;
        $this->authorId = $this->author->getId();
    }

    /**
     * Get author
     *
     * @return Zubi\UserBundle\Entity\Osoba
     */
    public function getAuthor()
    {
        return $this->author;
    }
    /**
     * @var Zubi\ArticleBundle\Entity\User
     */
    private $creator;


    /**
     * Set creator
     *
     * @param Zubi\ArticleBundle\Entity\User $creator
     */
    public function setCreator(\Zubi\ArticleBundle\Entity\User $creator)
    {
        $this->creator = $creator;
    }

    /**
     * Get creator
     *
     * @return Zubi\ArticleBundle\Entity\User 
     */
    public function getCreator()
    {
        return $this->creator;
    }
    
    private $articleGroup;



    public function setArticleGroup(\Zubi\ArticleBundle\Entity\ArticleGroup $articleGroup)
    {
        $this->articleGroup = $articleGroup;        
        $this->groupId = $this->articleGroup->getId();       
    }


    public function getArticleGroup()
    {
        return $this->articleGroup;
    }
    
    
}