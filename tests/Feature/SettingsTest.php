<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;

class SettingsTest extends BrowserKitTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function requires_login()
    {
        $this->visit('/settings')
            ->seePageIs('/login');
    }

    /** @test */
    public function users_can_update_their_profile()
    {
        $this->login();

        $this->visit('/settings')
            ->submitForm('Save', [
                'name' => 'Freek Murze',
                'email' => 'freek@example.com',
                'username' => 'freekmurze',
                'bio' => 'My bio',
            ])
            ->seePageIs('/settings')
            ->see('Freek Murze')
            ->see('freekmurze')
            ->see('Settings successfully saved!')
            ->see('My bio');
    }

    /** @test */
    public function users_cannot_choose_duplicate_usernames_or_email_addresses()
    {
        $this->createUser(['email' => 'freek@example.com', 'username' => 'freekmurze']);

        $this->login();

        $this->visit('/settings')
            ->submitForm('Save', [
                'name' => 'Freek Murze',
                'email' => 'freek@example.com',
                'username' => 'freekmurze',
            ])
            ->seePageIs('/settings')
            ->see('Something went wrong. Please review the fields below.')
            ->see('The email has already been taken.')
            ->see('The username has already been taken.');
    }

    /** @test */
    public function users_can_delete_their_account()
    {
        $this->login(['name' => 'Freek Murze']);

        $this->delete('/settings')
            ->assertRedirectedTo('/');

        $this->notSeeInDatabase('users', ['name' => 'Freek Murze']);
    }

    /** @test */
    public function users_cannot_delete_their_account()
    {
        $this->loginAsAdmin();

        $this->visit('/settings')
            ->dontSee('Delete Account');
    }

    /** @test */
    public function users_can_update_their_password()
    {
        $this->login();

        $this->visit('/settings/password')
            ->submitForm('Save', [
                'current_password' => 'password',
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->seePageIs('/settings/password')
            ->see('Password successfully changed!');

        $this->assertPasswordWasHashedAndSaved();
    }

    /** @test */
    public function users_can_set_their_password_when_they_have_none_set_yet()
    {
        $user = factory(User::class)->states('passwordless')->create();

        $this->loginAs($user);

        $this->visit('/settings/password')
            ->submitForm('Save', [
                'password' => 'newpassword',
                'password_confirmation' => 'newpassword',
            ])
            ->seePageIs('/settings/password')
            ->see('Password successfully changed!');

        $this->assertPasswordWasHashedAndSaved();
    }

    private function assertPasswordWasHashedAndSaved(): void
    {
        $this->assertTrue($this->app['hash']->check('newpassword', Auth::user()->getAuthPassword()));
    }
}
