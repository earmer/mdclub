<?php

declare(strict_types=1);

namespace App\Controller;

use App\Abstracts\ControllerAbstracts;
use App\Helper\ArrayHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * 评论
 *
 * Class CommentController
 * @package App\Controller
 */
class CommentController extends ControllerAbstracts
{
    /**
     * 获取指定用户发表的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $user_id
     * @return Response
     */
    public function getListByUserId(Request $request, Response $response, int $user_id): Response
    {
        $list = $this->commentService->getList(['user_id' => $user_id], true);

        return $this->success($response, $list);
    }

    /**
     * 获取当前用户发表的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getMyList(Request $request, Response $response): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $list = $this->commentService->getList(['user_id' => $userId], true);

        return $this->success($response, $list);
    }

    /**
     * 获取所有评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getList(Request $request, Response $response): Response
    {
        $list = $this->commentService->getList([], true);

        return $this->success($response, $list);
    }

    /**
     * 批量删除评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function deleteMultiple(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $commentIds = ArrayHelper::parseQuery($request, 'comment_id', 100);
        $this->commentService->deleteMultiple($commentIds);

        return $this->success($response);
    }

    /**
     * 获取评论详情
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function getOne(Request $request, Response $response, int $comment_id): Response
    {
        $comment = $this->commentService->getOrFail($comment_id, true);

        return $this->success($response, $comment);
    }

    /**
     * 更新评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function updateOne(Request $request, Response $response, int $comment_id): Response
    {
        $content = $request->getParsedBodyParam('content');

        $this->commentService->update($comment_id, $content);
        $commentInfo = $this->commentService->get($comment_id, true);

        return $this->success($response, $commentInfo);
    }

    /**
     * 删除评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function deleteOne(Request $request, Response $response, int $comment_id): Response
    {
        $this->commentService->delete($comment_id);

        return $this->success($response);
    }

    /**
     * 获取投票者
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function getVoters(Request $request, Response $response, int $comment_id): Response
    {
        $type = $request->getQueryParam('type');
        $voters = $this->commentService->getVoters($comment_id, $type, true);

        return $this->success($response, $voters);
    }

    /**
     * 添加投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function addVote(Request $request, Response $response, int $comment_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $type = $request->getParsedBodyParam('type');

        $this->commentService->addVote($userId, $comment_id, $type);
        $voteCount = $this->commentService->getVoteCount($comment_id);

        return $this->success($response, ['vote_count' => $voteCount]);
    }

    /**
     * 删除投票
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function deleteVote(Request $request, Response $response, int $comment_id): Response
    {
        $userId = $this->roleService->userIdOrFail();
        $this->commentService->deleteVote($userId, $comment_id);
        $voteCount = $this->commentService->getVoteCount($comment_id);

        return $this->success($response, ['vote_count' => $voteCount]);
    }

    /**
     * 获取回收站中的评论列表
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function getDeletedList(Request $request, Response $response): Response
    {
        $this->roleService->managerIdOrFail();

        $list = $this->commentService->getList(['is_deleted' => true], true);

        return $this->success($response, $list);
    }

    /**
     * 批量恢复评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function restoreMultiple(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 批量删除回收站中的评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @return Response
     */
    public function destroyMultiple(Request $request, Response $response): Response
    {
        return $response;
    }

    /**
     * 恢复指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function restoreOne(Request $request, Response $response, int $comment_id): Response
    {
        return $response;
    }

    /**
     * 删除指定评论
     *
     * @param  Request  $request
     * @param  Response $response
     * @param  int      $comment_id
     * @return Response
     */
    public function destroyOne(Request $request, Response $response, int $comment_id): Response
    {
        return $response;
    }
}
