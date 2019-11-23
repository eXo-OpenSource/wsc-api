<?php
namespace wcf\api;

use wcf\util\StringUtil;
use wcf\system\exception\ApiException;
use wcf\system\api\ApiResponse;
use wbb\data\post\PostList;

/**
 * @author 	Robert Bitschnau
 * @package	at.megathorx.wsc-api
 */
class PostApi extends BaseApi {

    /**
     * @api
     * @param integer $threadID
     * @param integer $postID
     * @param integer $userID
     * @param boolean $hasPoll
     * @param integer $limit
     * @param integer $offset
     * @permission('post.canFetchPostData')
     */
    public function get($threadID = null, $postID = null, $userID = null, $hasPoll = null, $limit = 10, $offset = 0) {
        if (empty($threadID) && empty($postID)) {
            throw new ApiException('threadID or postID is required', 400);
        }

        $postList = new PostList();

        if (!empty($threadID)) {
            $postList->getConditionBuilder()->add('threadID = ?', [$threadID]);
        }

        if (!empty($postID)) {
            $postList->getConditionBuilder()->add('postID = ?', [$postID]);
        }

        if (!empty($userID)) {
            $postList->getConditionBuilder()->add('userID = ?', [$userID]);
        }

        if (!empty($hasPoll)) {
            $postList->getConditionBuilder()->add('pollID IS NOT NULL');
        }

        $postList->sqlLimit = $limit ? $limit : 10;
        $postList->sqlOffset = $offset ? $offset : 0;
        $postList->readObjects();

        $count = $postList->countObjects();


        if (sizeof($postList) === 0) {
            throw new ApiException('threadID or postID is invalid', 412);
        }

        $data = [];

        if (!empty($postID)) {
            $post = $postList->current();
            return [
                'postID' => $post->postID,
                'threadID' => $post->threadID,
                'userID' => $post->userID,
                'username' => $post->username,
                'subject' => $post->subject,
                'message' => $post->message,
                'time' => $post->time,
                'isDeleted' => $post->isDeleted,
                'isDisabled' => $post->isDisabled,
                'isClosed' => $post->isClosed,
                'editorID' => $post->editorID,
                'editor' => $post->editor,
                'lastEditTime' => $post->lastEditTime,
                'editCount' => $post->editCount,
                'editReason' => $post->editReason,
                'lastVersionTime' => $post->lastVersionTime,
                'attachments' => $post->attachments,
                'pollID' => $post->pollID,
                'enableHtml' => $post->enableHtml,
                'ipAddress' => $post->ipAddress,
                'cumulativeLikes' => $post->cumulativeLikes,
                'deleteTime' => $post->deleteTime,
                'enableTime' => $post->enableTime,
                'hasEmbeddedObjects' => $post->hasEmbeddedObjects
            ];
        } else {
            foreach ($postList as $post) {
                array_push($data, [
                    'postID' => $post->postID,
                    'threadID' => $post->threadID,
                    'userID' => $post->userID,
                    'username' => $post->username,
                    'subject' => $post->subject,
                    'message' => $post->message,
                    'time' => $post->time,
                    'isDeleted' => $post->isDeleted,
                    'isDisabled' => $post->isDisabled,
                    'isClosed' => $post->isClosed,
                    'editorID' => $post->editorID,
                    'editor' => $post->editor,
                    'lastEditTime' => $post->lastEditTime,
                    'editCount' => $post->editCount,
                    'editReason' => $post->editReason,
                    'lastVersionTime' => $post->lastVersionTime,
                    'attachments' => $post->attachments,
                    'pollID' => $post->pollID,
                    'enableHtml' => $post->enableHtml,
                    'ipAddress' => $post->ipAddress,
                    'cumulativeLikes' => $post->cumulativeLikes,
                    'deleteTime' => $post->deleteTime,
                    'enableTime' => $post->enableTime,
                    'hasEmbeddedObjects' => $post->hasEmbeddedObjects
                ]);
            }
        }

        return new ApiResponse($data, 200, $count);
    }
}
