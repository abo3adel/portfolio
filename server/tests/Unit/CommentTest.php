<?php

namespace Tests\Unit;

use App\Comment;
use Facades\Tests\Setup\PostBuilder;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testItBelongsToPost()
    {
        $post = PostBuilder::create();

        $comment = $post->comments()->save(
            factory(Comment::class)->make()
        );

        $this->assertSame($post->id, $comment->post->id);
    }

    public function testItHasUpdaedAttr()
    {
        $c = factory(Comment::class)->create();

        $this->assertIsString($c->updated);
        $this->assertSame(
            $c->updated_at->format('d M Y'),
            $c->updated
        );

        $this->assertArrayHasKey(
            'updated',
            $c->toArray()
        );
    }
}
