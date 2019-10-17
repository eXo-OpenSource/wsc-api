<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\data\trophy\TrophyList;
use wbb\data\thread\ThreadList;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class ThreadApi extends BaseApi {

    /**
     * @api
     * @permission('trophy.canFetchTrophyData')
     */
    public function index($userID = null, $boardID = null, $limit = null, $offset = null) {
        $threadList = new ThreadList();
        
        if (!empty($boardID)) {
            $threadList->getConditionBuilder()->add("thread.boardID = ?", [$boardID]);
        }

        if (!empty($userID)) {
            $threadList->getConditionBuilder()->add("thread.userID = ?", [$userID]);
        }

        $threadList->sqlLimit = $limit;
        $threadList->sqlOffset = $offset;
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

        return $data;
    }
    
    /**
     * @api
     * @permission('trophy.canFetchTrophyData')
     */
    public function get($trophyID) {
        if (empty($trophyID)) {
            throw new ApiException('trophyID is required', 400);
        }

		$trophyList = new TrophyList();
		$trophyList->getConditionBuilder()->add('trophyID = ?', [$trophyID]);
        $trophyList->readObjects();

        if (sizeof($trophyList) !== 1) {
            throw new ApiException('trophyID is invalid', 412);
        }

        $data = [];

        foreach ($trophyList as $trophy) {
            $category = $trophy->getCategory();
            $data = [
                'trophyID' => $trophy->trophyID,
                'title' => $trophy->title,
                'description' => $trophy->description,
                'categoryID' => $trophy->categoryID,
                'category' => [
                    'categoryID' => $category->categoryID,
                    'title' => $category->title,
                    'description' => $category->description,
                    'isDisabled' => $category->isDisabled
                ],
                'type' => $trophy->type,
                'iconFile' => $trophy->iconFile,
                'iconName' => $trophy->iconName,
                'iconColor' => $trophy->iconColor,
                'badgeColor' => $trophy->badgeColor,
                'isDisabled' => $trophy->isDisabled,
                'awardAutomatically' => $trophy->awardAutomatically
            ];
        }

        return $data;
    }
}
