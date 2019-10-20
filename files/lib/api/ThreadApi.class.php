<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wbb\data\thread\ThreadList;
use wcf\system\api\ApiResponse;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class ThreadApi extends BaseApi {

    /**
     * @api
     * @permission('thread.canFetchThreadData')
     */
    public function index($userID = null, $boardID = null, $isSticky = null, $isDisabled = null, $isClosed = null, $isDeleted = null, $isDone = null,
                            $limit = null, $offset = null) {

        $threadList = new ThreadList();

        if (!empty($boardID)) {
            $threadList->getConditionBuilder()->add("thread.boardID = ?", [$boardID]);
        }

        if (!empty($userID)) {
            $threadList->getConditionBuilder()->add("thread.userID = ?", [$userID]);
        }

        if (!empty($isSticky)) {
            $threadList->getConditionBuilder()->add("thread.isSticky = ?", [$isSticky]);
        }

        if (!empty($isDisabled)) {
            $threadList->getConditionBuilder()->add("thread.isDisabled = ?", [$isDisabled]);
        }

        if (!empty($isClosed)) {
            $threadList->getConditionBuilder()->add("thread.isClosed = ?", [$isClosed]);
        }

        if (!empty($isDeleted)) {
            $threadList->getConditionBuilder()->add("thread.isDeleted = ?", [$isDeleted]);
        }

        if (!empty($isDone)) {
            $threadList->getConditionBuilder()->add("thread.isDone = ?", [$isDone]);
        }

        $threadList->sqlLimit = $limit ? $limit : 10;
        $threadList->sqlOffset = $offset ? $offset : 0;
        $threadList->readObjects();

        $data = [];

        $count = $threadList->countObjects();

        foreach ($threadList as $thread) {
            $firstPost = $thread->getFirstPost();
            array_push($data, [
                'threadID' => $thread->threadID,
                'boardID' => $thread->boardID,
                'languageID' => $thread->languageID,
                'topic' => $thread->topic,
                'firstPostID' => $thread->firstPostID,
                'firstPost' => [
                    'postID' => $firstPost->postID,
                    'threadID' => $firstPost->threadID,
                    'userID' => $firstPost->userID,
                    'username' => $firstPost->username,
                    'subject' => $firstPost->subject,
                    'message' => $firstPost->message,
                    'time' => $firstPost->time,
                    'isDeleted' => $firstPost->isDeleted,
                    'isDisabled' => $firstPost->isDisabled,
                    'isClosed' => $firstPost->isClosed,
                    'editorID' => $firstPost->editorID,
                    'editor' => $firstPost->editor,
                    'lastEditTime' => $firstPost->lastEditTime,
                    'editCount' => $firstPost->editCount,
                    'editReason' => $firstPost->editReason,
                    'lastVersionTime' => $firstPost->lastVersionTime,
                    'attachments' => $firstPost->attachments,
                    'pollID' => $firstPost->pollID,
                    'enableHtml' => $firstPost->enableHtml,
                    'ipAddress' => $firstPost->ipAddress,
                    'cumulativeLikes' => $firstPost->cumulativeLikes,
                    'deleteTime' => $firstPost->deleteTime,
                    'enableTime' => $firstPost->enableTime,
                    'hasEmbeddedObjects' => $firstPost->hasEmbeddedObjects
                ],
                'time' => $thread->time,
                'userID' => $thread->userID,
                'username' => $thread->username,
                'lastPostID' => $thread->lastPostID,
                'lastPostTime' => $thread->lastPostTime,
                'lastPosterID' => $thread->lastPosterID,
                'lastPoster' => $thread->lastPoster,
                'replies' => $thread->replies,
                'views' => $thread->views,
                'attachments' => $thread->attachments,
                'polls' => $thread->polls,
                'isAnnouncement' => $thread->isAnnouncement,
                'isSticky' => $thread->isSticky,
                'isDisabled' => $thread->isDisabled,
                'isClosed' => $thread->isClosed,
                'isDeleted' => $thread->isDeleted,
                'movedThreadID' => $thread->movedThreadID,
                'movedTime' => $thread->movedTime,
                'isDone' => $thread->isDone,
                'cumulativeLikes' => $thread->cumulativeLikes,
                'hasLabels' => $thread->hasLabels,
                'deleteTime' => $thread->deleteTime
            ]);
        }

        return new ApiResponse($data, 200, $count);
    }

    /**
     * @api
     * @permission('thread.canFetchThreadData')
     */
    public function get($threadID) {
        if (empty($threadID)) {
            throw new ApiException('threadID is required', 400);
        }

        $threadList = new ThreadList();
		$threadList->getConditionBuilder()->add('threadID = ?', [$threadID]);
        $threadList->readObjects();

        if (sizeof($threadList) !== 1) {
            throw new ApiException('threadID is invalid', 412);
        }

        $data = [];

        foreach ($threadList as $thread) {
            $firstPost = $thread->getFirstPost();
            array_push($data, [
                'threadID' => $thread->threadID,
                'boardID' => $thread->boardID,
                'languageID' => $thread->languageID,
                'topic' => $thread->topic,
                'firstPostID' => $thread->firstPostID,
                'firstPost' => [
                    'postID' => $firstPost->postID,
                    'threadID' => $firstPost->threadID,
                    'userID' => $firstPost->userID,
                    'username' => $firstPost->username,
                    'subject' => $firstPost->subject,
                    'message' => $firstPost->message,
                    'time' => $firstPost->time,
                    'isDeleted' => $firstPost->isDeleted,
                    'isDisabled' => $firstPost->isDisabled,
                    'isClosed' => $firstPost->isClosed,
                    'editorID' => $firstPost->editorID,
                    'editor' => $firstPost->editor,
                    'lastEditTime' => $firstPost->lastEditTime,
                    'editCount' => $firstPost->editCount,
                    'editReason' => $firstPost->editReason,
                    'lastVersionTime' => $firstPost->lastVersionTime,
                    'attachments' => $firstPost->attachments,
                    'pollID' => $firstPost->pollID,
                    'enableHtml' => $firstPost->enableHtml,
                    'ipAddress' => $firstPost->ipAddress,
                    'cumulativeLikes' => $firstPost->cumulativeLikes,
                    'deleteTime' => $firstPost->deleteTime,
                    'enableTime' => $firstPost->enableTime,
                    'hasEmbeddedObjects' => $firstPost->hasEmbeddedObjects
                ],
                'time' => $thread->time,
                'userID' => $thread->userID,
                'username' => $thread->username,
                'lastPostID' => $thread->lastPostID,
                'lastPostTime' => $thread->lastPostTime,
                'lastPosterID' => $thread->lastPosterID,
                'lastPoster' => $thread->lastPoster,
                'replies' => $thread->replies,
                'views' => $thread->views,
                'attachments' => $thread->attachments,
                'polls' => $thread->polls,
                'isAnnouncement' => $thread->isAnnouncement,
                'isSticky' => $thread->isSticky,
                'isDisabled' => $thread->isDisabled,
                'isClosed' => $thread->isClosed,
                'isDeleted' => $thread->isDeleted,
                'movedThreadID' => $thread->movedThreadID,
                'movedTime' => $thread->movedTime,
                'isDone' => $thread->isDone,
                'cumulativeLikes' => $thread->cumulativeLikes,
                'hasLabels' => $thread->hasLabels,
                'deleteTime' => $thread->deleteTime
            ]);
        }

        return $data;
    }

    /**
     * @api
     * @permission('thread.canCreateThread')
     */
    public function create($boardID, $title, $message, $userID = null, $username = null, $enableTime = null, $tags = null, $disabled = false, $closed = false, $languageID = null, $type = 0, $announcementBoards = null) {
        // 'thread.canCreateThread'

        return 'No good work'; // TODO: Needs fix

		$data = [
			'boardID' => $boardID,
			'languageID' => $languageID,
			'topic' => $title,
			'time' => TIME_NOW,
			'userID' => $userID ? $userID : null,
			'username' => $username ? $username : 'System',
			'hasLabels' => !empty($labelIDs) ? 1 : 0
        ];

		if ($closed) $data['isClosed'] = 1;
		if ($disabled) $data['isDisabled'] = 1;

		$threadData = [
			'data' => $data,
			'board' => $boardID,
			'attachmentHandler' => $this->attachmentHandler,
			'htmlInputProcessor' => $this->htmlInputProcessor,
			'postData' => ['enableTime' => $this->enableTimeObj ? $this->enableTimeObj->getTimestamp() : 0],
			'tags' => [],
			'subscribeThread' => $this->subscribeThread,
			'optionHandler' => $this->optionHandler
		];

		// handle thread type
		switch ($this->type) {
			case Thread::TYPE_STICKY:
				$threadData['data']['isSticky'] = 1;
			break;

			case Thread::TYPE_ANNOUNCEMENT:
				$threadData['data']['isAnnouncement'] = 1;
				$threadData['announcementBoardIDs'] = $this->boardIDs;
			break;
		}

		if (MODULE_TAGGING && WBB_THREAD_ENABLE_TAGS) {
			$threadData['tags'] = $tags;
            // if (isset($_POST['tags']) && is_array($_POST['tags'])) $this->tags = ArrayUtil::trim($_POST['tags']);
        }

		$thread = new ThreadAction([], 'create', $threadData);
		$resultValues = $thread->executeAction();

		// save labels
		if (!empty($this->labelIDs)) {
			ThreadLabelObjectHandler::getInstance()->setLabels($this->labelIDs, $resultValues['returnValues']->threadID);
        }

        return $data;
    }
}
