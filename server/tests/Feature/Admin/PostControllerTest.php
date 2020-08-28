<?php

namespace Tests\Feature\Admin;

use App\Http\Controllers\Admin\PostController;
use App\Post;
use App\Tag;
use Facades\Tests\Setup\PostBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Str;

class PostControllerTest extends TestCase
{
    protected string $url = 'posts/';

    public function testUserCanCreatePost()
    {
        $this->withoutExceptionHandling();
        Storage::fake('custom');

        $img = UploadedFile::fake()->image('a.jpeg');
        $post = PostBuilder::raw([
            'img' => $img
        ]);
        $tags = factory(Tag::class, 3)->create()->pluck('slug');

        $this->postJson($this->url, $post + compact('tags'))
            ->assertJsonMissingValidationErrors()
            ->assertCreated()
            ->assertJsonPath('title', $post['title'])
            ->assertJsonPath('tags.0.slug', $tags[0]);

        $this->assertDatabaseHas('posts', [
            'title' => $post['title'],
            'img' => $img->hashName(),
        ]);

        Storage::disk('custom')->assertExists(PostController::UploadDIr . $img->hashName());
    }

    public function testUserCannotUpdateWithInvalidData()
    {

        $post = factory(Post::class)->create();

        $this->patchJson($this->url . $post->slug)
            ->assertStatus(422);
    }

    public function testUserCanUpdatePost()
    {
        // $this->withoutExceptionHandling();
        Storage::fake('custom');

        $title = $this->faker->text(50);

        $img = UploadedFile::fake()->image('b.png');
        $post = PostBuilder::create([
            'img' => Str::replaceFirst(
                PostController::UploadDIr . '/',
                '',
                Storage::disk('custom')->put(PostController::UploadDIr, $img)
            )
        ]);



        $tags = factory(Tag::class, 3)->create();
        $post->tags()->sync($tags);
        $post->loadMissing('tags');
        $tags = $tags->pluck('slug');

        $newImg = UploadedFile::fake()->image('w.jpg');
        $post->img = $newImg;
        $post->title = $title;

        unset($post->tags);

        // check that old image is stored
        Storage::disk('custom')->assertExists(
            PostController::UploadDIr . $img->hashName()
        );

        $this->patchJson(
            $this->url . $post->slug,
            $post->toArray() + compact('tags')
        )->assertJsonMissingValidationErrors()
            ->assertOk()
            ->assertJsonPath('tags.0.slug', $tags[0])
            ->assertJsonPath('img', $newImg->hashName());

        $this->assertDatabaseHas('posts', [
            'title' => $title,
            'img' => $newImg->hashName(),
            'slug' => $post->slug,
        ]);

        Storage::disk('custom')->assertExists(
            PostController::UploadDIr . $newImg->hashName()
        );
        Storage::disk('custom')->assertMissing(
            PostController::UploadDIr . $img->hashName()
        );
    }

    public function testUserCanDeletePost()
    {
        Storage::fake('custom');
        // $this->withoutExceptionHandling();
        $img = UploadedFile::fake()->image('b.png');
        $post = PostBuilder::create([
            'img' => Str::replaceFirst(
                PostController::UploadDIr . '/',
                '',
                Storage::disk('custom')->put(PostController::UploadDIr, $img)
            )
        ]);



        Storage::disk('custom')->assertExists(
            PostController::UploadDIr . $img->hashName()
        );

        $this->deleteJson($this->url . $post->slug)
            ->assertNoContent();

        $this->assertDatabaseMissing('posts', $post->only('slug'));

        Storage::disk('custom')->assertMissing(
            PostController::UploadDIr . $img->hashName()
        );
    }
}
