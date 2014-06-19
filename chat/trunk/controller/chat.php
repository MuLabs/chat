<?php
namespace Mu\Bundle\Chat\Controller;

use Mu\Kernel;
use Mu\Bundle;
use Mu\App;

class Chat extends Bundle\Users\Controller\Connected
{
    public function fetch()
    {
        $action = $this->request('action', '');

        switch ($action) {
            case 'last':
                return $this->actionReload();
                break;
            case 'add':
                return $this->actionAdd();
                break;
            default:
                $this->error('404');
                break;
        }
    }

    public function actionReload()
    {
        $lastId = $this->request('lastId', 0);

        if (!$lastId) {
            $this->error('500');
        }

        $view = $this->getView();
        $messages = $this->getApp()->getChatMessageManager()->getMessagesLimitById($lastId);

        $view->setVar('messages', array_reverse($messages));
        return $view->fetch('backoffice/common/chatMessageList');
    }

    public function actionAdd()
    {
        $content = $this->request('content', '');
        $viewer = $this->getViewer();

        if (empty($content)) {
            $this->error('500');
        }

        $this->getApp()->getChatMessageManager()->create(
            array(
                'content' => $content,
                'idSender' => $viewer->getId(),
            )
        );

        return '';
    }
}