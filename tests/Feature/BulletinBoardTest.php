<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Thread;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class BulletinBoardTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_lists_threads_with_most_recently_active_first(): void
    {
        $olderThread = Thread::factory()->has(Post::factory()->count(2))->create(['title' => '古いスレッド']);
        $newerThread = Thread::factory()->create(['title' => '新しいスレッド']);

        Date::setTestNow(now()->addMinute());
        Post::factory()->for($olderThread)->create();

        $this->get(route('threads.index'))
            ->assertOk()
            ->assertSeeInOrder(['古いスレッド', '3件', '新しいスレッド']);
    }

    public function test_index_shows_message_when_there_are_no_threads(): void
    {
        $this->get(route('threads.index'))
            ->assertOk()
            ->assertSee('まだスレッドがありません');
    }

    public function test_thread_is_created_with_its_first_post(): void
    {
        $response = $this->post(route('threads.store'), [
            'title' => 'はじめてのスレッド',
            'name' => '太郎',
            'body' => 'よろしくお願いします',
        ]);

        $thread = Thread::sole();
        $response->assertRedirect(route('threads.show', $thread));
        $this->assertSame('はじめてのスレッド', $thread->title);
        $this->assertDatabaseHas('posts', [
            'thread_id' => $thread->id,
            'name' => '太郎',
            'body' => 'よろしくお願いします',
        ]);
    }

    public function test_thread_creation_requires_title_and_body(): void
    {
        $this->post(route('threads.store'), [])
            ->assertSessionHasErrors([
                'title' => 'タイトルを入力してください。',
                'body' => '本文を入力してください。',
            ]);

        $this->assertDatabaseCount('threads', 0);
        $this->assertDatabaseCount('posts', 0);
    }

    #[TestWith(['title', 101, 'タイトルは100文字以内で入力してください。'])]
    #[TestWith(['name', 51, '名前は50文字以内で入力してください。'])]
    #[TestWith(['body', 2001, '本文は2000文字以内で入力してください。'])]
    public function test_thread_creation_rejects_too_long_input(string $field, int $length, string $message): void
    {
        $payload = ['title' => 'タイトル', 'body' => '本文', $field => str_repeat('あ', $length)];

        $this->post(route('threads.store'), $payload)
            ->assertSessionHasErrors([$field => $message]);

        $this->assertDatabaseCount('threads', 0);
    }

    public function test_show_lists_posts_in_order_with_anonymous_fallback_name(): void
    {
        $thread = Thread::factory()->create(['title' => '雑談スレ']);
        Post::factory()->for($thread)->create(['name' => '花子', 'body' => '最初の書き込み']);
        Post::factory()->for($thread)->create(['name' => null, 'body' => '二番目の書き込み']);

        $this->get(route('threads.show', $thread))
            ->assertOk()
            ->assertSee('雑談スレ')
            ->assertSeeInOrder(['花子', '最初の書き込み', Post::ANONYMOUS_NAME, '二番目の書き込み']);
    }

    public function test_show_escapes_user_input(): void
    {
        $thread = Thread::factory()->create(['title' => '<b>title</b>']);
        Post::factory()->for($thread)->create(['body' => '<script>alert(1)</script>']);

        $this->get(route('threads.show', $thread))
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<b>title</b>', false);
    }

    public function test_show_returns_not_found_for_missing_thread(): void
    {
        $this->get(route('threads.show', 999))->assertNotFound();
    }

    public function test_reply_is_added_and_bumps_thread(): void
    {
        $thread = Thread::factory()->create();
        $originalUpdatedAt = $thread->updated_at;
        Date::setTestNow(now()->addMinute());

        $response = $this->post(route('posts.store', $thread), [
            'name' => '',
            'body' => '返信です',
        ]);

        $post = Post::sole();
        $response->assertRedirect(route('threads.show', $thread).'#post-'.$post->id);
        $this->assertSame($thread->id, $post->thread_id);
        $this->assertNull($post->name);
        $this->assertSame('返信です', $post->body);
        $this->assertTrue($thread->fresh()->updated_at->gt($originalUpdatedAt));
    }

    public function test_reply_requires_body(): void
    {
        $thread = Thread::factory()->create();

        $this->post(route('posts.store', $thread), ['body' => ''])
            ->assertSessionHasErrors(['body' => '本文を入力してください。']);

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_reply_to_missing_thread_returns_not_found(): void
    {
        $this->post(route('posts.store', 999), ['body' => '返信'])->assertNotFound();
    }

    public function test_posting_is_rate_limited(): void
    {
        $thread = Thread::factory()->create();

        for ($i = 0; $i < 10; $i++) {
            $this->post(route('posts.store', $thread), ['body' => '連投'])->assertRedirect();
        }

        $this->post(route('posts.store', $thread), ['body' => '連投'])->assertTooManyRequests();
        $this->assertDatabaseCount('posts', 10);
    }
}
