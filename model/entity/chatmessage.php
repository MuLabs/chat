<?php
namespace Mu\Bundle\Chat\Model\Entity;

use Mu\Kernel;
use Mu\App;
use Mu\Bundle;

class ChatMessage extends Kernel\Model\Entity
{
    protected $idSender;
    protected $content;
    protected $dateInsert;
    protected $dateEdit;

    #region Getters
    /**
     * @return string
     */
    public function getDateEdit()
    {
        return $this->dateEdit;
    }

    /**
     * @return string
     */
    public function getDateInsert()
    {
        return $this->dateInsert;
    }

    /**
     * @return int
     */
    public function getIdSender()
    {
        return $this->idSender;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    #endregion

    #region Setters
    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        $this->setProperty('content', $this->content);
    }

    /**
     * @internal
     * @param int $idSender
     */
    public function setIdSender($idSender)
    {
        $this->idSender = (int)$idSender;
        $this->setProperty('idSender', $this->idSender);
    }

    /**
     * @param Kernel\Model\Entity $sender
     */
    public function setSender(Kernel\Model\Entity $sender)
    {
        $this->setIdSender($sender->getId());
    }

    /**
     * @param string $dateInsert
     */
    public function setDateInsert($dateInsert)
    {
        $this->dateInsert = $dateInsert;
        $this->setProperty('dateInsert', $this->dateInsert);
    }

    /**
     * @param string $dateEdit
     */
    public function setDateEdit($dateEdit)
    {
        $this->dateEdit = $dateEdit;
        $this->setProperty('dateEdit', $this->dateEdit);
    }

    #endregion

    #region Getters specific
    /**
     * @return Bundle\Users\Model\Entity\User
     */
    public function getSender()
    {
        return $this->getApp()->getUserManager()->get($this->getIdSender());
    }

    /**
     * @return int
     */
    public function getTimeFromNow()
    {
        $now = new \DateTime();
        $insert = new \DateTime($this->getDateInsert());

        return abs($now->getTimestamp() - $insert->getTimestamp());
    }
    #endregion

    #region Setters specific

    #endregion
    /**
     * @return array|string
     */
    public function jsonSerialize()
    {
        return array(
            'idChatMessage' => $this->getId(),
            'idSender' => $this->getIdSender(),
            'content' => $this->getContent(),
            'dateInsert' => $this->getDateInsert(),
            'dateEdit' => $this->getDateEdit()
        );
    }
}