<?php

namespace Tests\Integration\Models;

use App\Models\Reply;
use App\Models\Thread;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_find_by_slug()
    {
        factory(Thread::class)->create(['slug' => 'foo']);

        $this->assertInstanceOf(Thread::class, Thread::findBySlug('foo'));
    }

    /** @test */
    public function it_can_give_an_excerpt_of_its_body()
    {
        $thread = factory(Thread::class)->make(['body' => 'This is a pretty long text.']);

        $this->assertEquals('This is...', $thread->excerpt(7));
    }

    /** @test */
    public function its_conversation_is_old_when_the_oldest_reply_was_six_months_ago()
    {
        $thread = factory(Thread::class)->create();
        $thread->repliesRelation()->save(factory(Reply::class)->make(['created_at' => now()->subMonths(7)]));

        $this->assertTrue($thread->isConversationOld());

        $thread = factory(Thread::class)->create();
        $thread->repliesRelation()->save(factory(Reply::class)->make());

        $this->assertFalse($thread->isConversationOld());
    }

    /** @test */
    public function its_conversation_is_old_when_there_are_no_replies_but_the_creation_date_was_six_months_ago()
    {
        $thread = factory(Thread::class)->create(['created_at' => now()->subMonths(7)]);

        $this->assertTrue($thread->isConversationOld());

        $thread = factory(Thread::class)->create();

        $this->assertFalse($thread->isConversationOld());
    }

    /** @test */
    public function we_can_mark_and_unmark_a_reply_as_the_solution()
    {
        $thread = factory(Thread::class)->create();
        $reply = factory(Reply::class)->create(['replyable_id' => $thread->id()]);

        $this->assertFalse($thread->isSolutionReply($reply));

        $thread->markSolution($reply);

        $this->assertTrue($thread->isSolutionReply($reply));

        $thread->unmarkSolution();

        $this->assertFalse($thread->isSolutionReply($reply));
    }

    /** @test */
    public function it_can_retrieve_the_latest_threads_in_a_correct_order()
    {
        $threadUpdatedYesterday = $this->createThreadFromYesterday();
        $threadFromToday = $this->createThreadFromToday();
        $threadFromTwoDaysAgo = $this->createThreadFromTwoDaysAgo();

        $threads = Thread::feed();

        $this->assertTrue($threadFromToday->matches($threads->first()), 'First thread is incorrect');
        $this->assertTrue($threadUpdatedYesterday->matches($threads->slice(1)->first()), 'Second thread is incorrect');
        $this->assertTrue($threadFromTwoDaysAgo->matches($threads->last()), 'Last thread is incorrect');
    }

    /** @test */
    public function it_generates_a_slug_when_valid_url_characters_provided()
    {
        $thread = factory(Thread::class)->make(['slug' => 'Help with eloquent']);

        $this->assertEquals('help-with-eloquent', $thread->slug());
    }

    /** @test */
    public function it_generates_a_unique_slug_when_valid_url_characters_provided()
    {
        $threadOne = factory(Thread::class)->create(['slug' => 'Help with eloquent']);
        $threadTwo = factory(Thread::class)->create(['slug' => 'Help with eloquent']);

        $this->assertEquals('help-with-eloquent-1', $threadTwo->slug());
    }

    /** @test */
    public function it_generates_a_slug_when_invalid_url_characters_provided()
    {
        $thread = factory(Thread::class)->make(['slug' => '한글 테스트']);

        // When providing a slug with invalid url characters, a random 5 character string is returned.
        $this->assertRegExp('/\w{5}/', $thread->slug());
    }

    private function createThreadFromToday(): Thread
    {
        $today = Carbon::now();

        return factory(Thread::class)->create(['created_at' => $today]);
    }

    private function createThreadFromYesterday(): Thread
    {
        $yesterday = Carbon::yesterday();

        return factory(Thread::class)->create(['created_at' => $yesterday]);
    }

    private function createThreadFromTwoDaysAgo(): Thread
    {
        $twoDaysAgo = Carbon::now()->subDay(2);

        return factory(Thread::class)->create(['created_at' => $twoDaysAgo]);
    }
}
