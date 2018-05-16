<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;

class PasswordResetTest extends BrowserKitTestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        Session::start();
    }

    public function testEmptyPasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Pole adres e-mail jest wymagane.');
    }

    public function testSimpleFailPasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('badEmail', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje')
            ->see('Format adres e-mail jest nieprawidłowy.');
    }

    public function testUserNotInDatabaseFailPasswordReset()
    {
        $this->visit('password/reset')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type('valid-email@test.com', 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->see('Nie znaleziono użytkownika z takim adresem e-mail')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje');

        $this->dontSeeInDatabase('users', [
            'email' => 'valid-email@test.com',
        ]);
    }

    public function testUserInDatabaseCorrectPasswordReset()
    {
        Notification::fake();
        $user = factory(\App\Models\User::class)->create();

        $this->seeInDatabase('users', [
            'email' => $user->email,
        ]);

        $this->visit('password/reset')
            ->see('Zaloguj')
            ->see('Zresetuj hasło')
            ->dontSee('Pokoje')
            ->type($user->email, 'email')
            ->press('Wyślij link na email')
            ->seePageIs('password/reset')
            ->see('Przypomnienie hasła zostało wysłane!')
            ->dontSee('Wyloguj')
            ->dontSee('Pokoje');

        Notification::assertSentTo(
            $user,
            \App\Notifications\ResetPasswordNotification::class,
            function ($notification) use ($user) {
                $mailData = $notification->toMail($user)->toArray();

                $this->assertContains('Zresetuj swoje hasło', $mailData['subject']);
                $this->assertContains('Otrzymujesz tego e-maila, ponieważ dostaliśmy prośbę zrestartowania hasła dla Twojego konta.', $mailData['introLines']);
                $this->assertEquals('Zresetuj hasło', $mailData['actionText']);
                $this->assertEquals(route('password.reset', $notification->token), $mailData['actionUrl']);
                $this->assertContains('Jeśli to nie Ty prosiłeś o restartowanie hasła, nie musisz podejmować żadnych działań.', $mailData['outroLines'][0]);

                return true;
            }
        );
    }

    public function testFactoryLoggedUserCannotResetPassword()
    {
        $this->fakeUser = factory(\App\Models\User::class)->create();

        if ($this->actingAs($this->fakeUser) == null) {
            $this->markTestSkipped('FakeUser not working...');
        }

        $this->actingAs($this->fakeUser)
            ->visit('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->see('Pokoje')
            ->visit('password/reset')
            ->seePageIs('home')
            ->see($this->fakeUser->name)
            ->see('You are logged in!')
            ->see('Pokoje');
    }
}
