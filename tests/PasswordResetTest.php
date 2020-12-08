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
            ->see('Pole adres e-mail nie jest poprawnym adresem e-mail.');
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
            function ($notification) use ($user, &$resetToken) {
                $mailData = $notification->toMail($user)->toArray();

                $this->assertContains('Zresetuj swoje hasło', $mailData['subject']);
                $this->assertContains('Otrzymujesz tego e-maila, ponieważ dostaliśmy prośbę zrestartowania hasła dla Twojego konta.', $mailData['introLines']);
                $this->assertEquals('Zresetuj hasło', $mailData['actionText']);
                $this->assertEquals(route('password.reset', $notification->token), $mailData['actionUrl']);
                $this->assertContains('Jeśli to nie Ty prosiłeś o restartowanie hasła, nie musisz podejmować żadnych działań.', $mailData['outroLines'][0]);

                $resetToken = $notification->token;

                return true;
            }
        );

        $this->visit(route('password.reset', $resetToken))
            ->seePageIs('password/reset/'.$resetToken)
            ->see('Zresetuj hasło')
            ->see('Adres e-mail')
            ->see('Hasło')
            ->see('Powtórz hasło')
            ->type($user->email, 'email')
            ->type('new_password', 'password')
            ->type('new_password', 'password_confirmation')
            ->press('Zresetuj hasło');

        $this->seePageIs('room')
            ->dontSee('Zaloguj')
            ->dontSee('Zresetuj hasło')
            ->see('Pokoje');
    }

    public function testFactoryLoggedUserCannotResetPassword()
    {
        $this->fakeUser = factory(\App\Models\User::class)->create();

        if ($this->actingAs($this->fakeUser) == null) {
            $this->markTestSkipped('FakeUser not working...');
        }

        $this->actingAs($this->fakeUser)
            ->visit('/')
            ->see($this->fakeUser->name)
            ->see('Pokoje')
            ->visit('password/reset')
            ->seePageIs('room')
            ->see($this->fakeUser->name)
            ->see('Pokoje');
    }
}
