<?php

declare(strict_types=1);

namespace Tests;

use App\CommentClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

/**
 * Class CommentClientTest
 * @package Tests
 * @covers \App\CommentClient
 */
class CommentClientTest extends TestCase
{
    protected CommentClient $commentClient;
    protected MockHandler $mockHandler;


    protected function setUp(): void
    {
        $this->mockHandler = new MockHandler();

        $httpClient = new Client([
            'handler' => $this->mockHandler,
        ]);

        $this->commentClient = new CommentClient($httpClient);
    }


    public function testCommentsAreRetrieved(): void
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/comments.json')));

        $comments = $this->commentClient->getComments();

        self::assertIsArray($comments);
        self::assertCount(3, $comments);
    }

    public function testCommentsAreEmptyWhenResponseError(): void
    {
        $this->mockHandler->append(new Response(404));

        $comments = $this->commentClient->getComments();

        self::assertEmpty($comments);
    }

    public function testCommentsAreEmptyWhenException(): void
    {
        $this->mockHandler->append(new RequestException('Error Communicating with Server', new Request('GET', 'test')));

        $comments = $this->commentClient->getComments();

        self::assertEmpty($comments);
    }

    public function testAddCommentSuccess(): void
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/add-comment.json')));

        $comment = $this->commentClient->addComment('added-comment', 'added-comment-text');

        self::assertSame('added-comment', $comment['name']);
        self::assertSame('added-comment-text', $comment['text']);
    }

    public function testAddCommentResponseErrorHandled(): void
    {
        $this->mockHandler->append(new Response(500));

        $comment = $this->commentClient->addComment('added-comment', 'added-comment-text');

        self::assertEmpty($comment);
    }

    public function testAddCommentExceptionHandled(): void
    {
        $this->mockHandler->append(new RequestException('Error Communicating with Server', new Request('POST', 'test')));

        $comment = $this->commentClient->addComment('added-comment', 'added-comment-text');

        self::assertEmpty($comment);
    }

    public function testEditCommentSuccess(): void
    {
        $this->mockHandler->append(new Response(200, [], file_get_contents(__DIR__ . '/fixtures/edit-comment.json')));

        $comment = $this->commentClient->editComment(1,'edited-comment', 'edited-comment-text');

        self::assertSame('edited-comment', $comment['name']);
        self::assertSame('edited-comment-text', $comment['text']);
    }

    public function testEditCommentResponseErrorHandled(): void
    {
        $this->mockHandler->append(new Response(500));

        $comment = $this->commentClient->editComment(1,'edited-comment', 'edited-comment-text');

        self::assertEmpty($comment);

    }

    public function testEditCommentExceptionHandled(): void
    {
        $this->mockHandler->append(new RequestException('Error Communicating with Server', new Request('PUT', 'test')));

        $comment = $this->commentClient->editComment(1,'edited-comment', 'edited-comment-text');

        self::assertEmpty($comment);
    }
}