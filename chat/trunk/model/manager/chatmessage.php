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
                'pk_idChatMessage' => array(
                    'type' => 'primary',
                    'properties' => array(
                        'idChatMessage',
                    ),
                ),
            ),
            'properties' => array(
                'idChatMessage' => array(
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
                    'db' => 'date_insert',
                    'pdo_extra' => 'NOT NULL',
                    'type' => 'date',
                ),
                'dateEdit' => array(
                    'title' => 'Date de édition',
                    'form' => array(
                        'type' => 'date',
                    ),
                    'db' => 'date_edit',
                    'pdo_extra' => 'NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
                    'type' => 'timestamp',
                )
            )
        ),
    );

    /**
     * @return string
     */
    public function getMainProperty()
    {
        return 'idChatMessage';
    }

    /**
     * @param int[] $ids
     * @return Bundle\Chat\Model\Entity\chatMessage[]
     */
    protected function initEntities(array $ids)
    {
        $where = implode(', ', array_fill(0, count($ids), '?'));
        $dbh = $this->getApp()->getDatabase()->getHandler('readFront');
        $sql = 'SELECT :idChatMessage, :idSender, :content, :dateInsert, :dateEdit
				FROM @
			WHERE :idChatMessage IN (' . $where . ')';
        $query = new Kernel\Db\Query($sql, $ids, $this);

        $result = $dbh->sendQuery($query);
        $entities = array();
        while (list($idChatMessage, $idSender, $content, $dateInsert, $dateEdit) = $result->fetchRow()) {
            /** @var Bundle\Chat\Model\Entity\chatMessage $entity */
            $entity = $this->generateEntity($idChatMessage);

            if (!$entity) {
                continue;
            }

            $entity->setId($idChatMessage);
            $entity->setIdSender($idSender);
            $entity->setContent($content);
            $entity->setDateInsert($dateInsert);
            $entity->setDateEdit($dateEdit);
            $entities[$idChatMessage] = $entity;
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
        if (empty($parameters['idSender'])) {
            $invalid[] = 'idSender';
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

        $idSender = $parameters['idSender'];
        $content = $parameters['content'];

        $handler = $this->getApp()->getDatabase()->getHandler('writeFront');

        $sql = 'INSERT INTO @ (:idSender, :content, :dateInsert) VALUES (?, ?, ?)';

        $query = new Kernel\Db\Query($sql, array($idSender, $content, date('Y-m-d H:i:s')), $this);
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
        $sql = 'SELECT :idChatMessage
				FROM @
			ORDER BY :idChatMessage DESC
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
        $sql = 'SELECT :idChatMessage
				FROM @
				WHERE :idChatMessage > ?
			ORDER BY :idChatMessage DESC';
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