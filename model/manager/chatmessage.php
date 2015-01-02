<?php
namespace Mu\Bundle\Chat\Model\Manager;

use Mu\Kernel;
use Mu\App;
use Mu\Bundle;

class ChatMessage extends Kernel\Model\Manager
{
    protected $properties = array(
        'chatMessage' => array(
            'infos' => array(
                'db' => 'chatMessage'
            ),
            'keys' => array(
                'pk_id' => array(
                    'type' => 'primary',
                    'properties' => array(
                        'id',
                    ),
                ),
            ),
            'properties' => array(
                'id' => array(
                    'title' => 'ID CM',
                    'form' => array(
                        'type' => 'hidden',
                    ),
                    'db' => 'idChatMessage',
                    'pdo_extra' => 'UNSIGNED NOT NULL AUTO_INCREMENT',
                    'type' => 'int',
                ),
                'idSender' => array(
                    'title' => 'ID sender',
                    'form' => array(
                        'type' => 'hidden',
                    ),
                    'db' => 'idSender',
                    'pdo_extra' => 'UNSIGNED NOT NULL',
                    'type' => 'smallint',
                ),
                'content' => array(
                    'title' => 'Content CM',
                    'form' => array(
                        'type' => 'textarea',
                    ),
                    'db' => 'content',
                    'pdo_extra' => 'NOT NULL',
                    'type' => 'text'
                ),
                'dateInsert' => array(
                    'title' => 'Date de instertion',
                    'form' => array(
                        'type' => 'date',
                    ),
                    'db' => 'dateInsert',
                    'pdo_extra' => 'NOT NULL',
                    'type' => 'date',
                ),
                'dateEdit' => array(
                    'title' => 'Date de édition',
                    'form' => array(
                        'type' => 'date',
                    ),
                    'db' => 'dateEdit',
                    'pdo_extra' => 'NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                    'type' => 'timestamp',
                )
            )
        ),
    );

    /**
     * @param int[] $ids
     * @return Bundle\Chat\Model\Entity\chatMessage[]
     */
    protected function initEntities(array $ids)
    {
        $where = implode(', ', array_fill(0, count($ids), '?'));
        $dbh = $this->getApp()->getDatabase()->getHandler('readFront');
        $sql = 'SELECT :id, :idSender, :content, :dateInsert, :dateEdit
				FROM @
			WHERE :id IN (' . $where . ')';
        $query = new Kernel\Db\Query($sql, $ids, $this);

        $result = $dbh->sendQuery($query);
        $entities = array();
        while (list($id, $idSender, $content, $dateInsert, $dateEdit) = $result->fetchRow()) {
            /** @var Bundle\Chat\Model\Entity\chatMessage $entity */
            $entity = $this->generateEntity($id);

            if (!$entity) {
                continue;
            }

            $entity->setId($id);
            $entity->setIdSender($idSender);
            $entity->setContent($content);
            $entity->setDateInsert($dateInsert);
            $entity->setDateEdit($dateEdit);
            $entities[$id] = $entity;
        }

        return $entities;
    }

    /**
     * @param array $parameters
     * @return Bundle\Chat\Model\Entity\chatMessage
     * @throws \Mu\Kernel\Model\Exception
     */
    public function create(array $parameters = array())
    {
        $invalid = array();
        if (empty($parameters['sender']) && !($parameters['sender'] instanceof Kernel\Model\Entity)) {
            $invalid[] = 'sender';
        }
        if (empty($parameters['content'])) {
            $invalid[] = 'content';
        }

        if (count($invalid)) {
            throw new Kernel\Model\Exception(implode(
                ', ',
                $invalid
            ), Kernel\Model\Exception::INVALID_CREATE_PARAMETERS);
        }

        /** @var Kernel\Model\Entity $sender */
        $sender  = $parameters['sender'];
        $content = $parameters['content'];

        $handler = $this->getApp()->getDatabase()->getHandler('writeFront');

        $sql = 'INSERT INTO @ (:idSender, :content, :dateInsert) VALUES (?, ?, ?)';

        $query = new Kernel\Db\Query($sql, array(
            $sender->getId(),
            $content,
            date('Y-m-d H:i:s')
        ), $this);
        $handler->sendQuery($query);
        $idMessage = $handler->getInsertId();
        $message = $this->get($idMessage);

        $message->logAction(
            Kernel\Backoffice\ActionLogger::ACTION_CREATE,
            array(),
            $parameters
        );
        $this->discard();

        return $message;
    }

    /**
     * @param int $limit
     * @return Bundle\Chat\Model\Entity\chatMessage[]
     */
    public function getMessagesLimitByNumber($limit)
    {
        $handler = $this->getApp()->getDatabase()->getHandler('readFront');
        $sql = 'SELECT :id
				FROM @
			ORDER BY :id DESC
			LIMIT ?';
        $query = new Kernel\Db\Query($sql, array($limit), $this);
        $result = $handler->sendQuery($query);

        return $this->multiGet($result->fetchAllValue());
    }

    /**
     * @param int $idLast
     * @return Bundle\Chat\Model\Entity\chatMessage[]
     */
    public function getMessagesLimitById($idLast)
    {
        $handler = $this->getApp()->getDatabase()->getHandler('readFront');
        $sql = 'SELECT :id
				FROM @
				WHERE :id > ?
			ORDER BY :id DESC';
        $query = new Kernel\Db\Query($sql, array($idLast), $this);
        $result = $handler->sendQuery($query);

        return $this->multiGet($result->fetchAllValue());
    }

    /**
     * @param mixed $id
     * @return Bundle\Chat\Model\Entity\chatMessage
     */
    public function get($id)
    {
        return parent::get($id);
    }

    /**
     * @param string $stdOut
     * @return bool
     */
    public function createDefaultDataSet($stdOut = '\print')
    {

    }
}