<?php

namespace Tests\Unit\Mails;

use App\Mails\UserMail;
use Faker\Factory;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserMailTest extends TestCase
{
    /** @test */
    public function send_mail()
    {
        $faker = Factory::create();

        $email = $faker->email;
        $subject = $faker->sentence;
        $message = $faker->text();

        $mail = new UserMail($email, $subject, $message);

        Mail::send($mail);

        Mail::assertSent(UserMail::class, function (UserMail $mail) use ($email, $subject, $message) {
            $mail->build();

            $this->assertContains($subject, $mail->subject);
            $this->assertContains($message, $mail->message);

            return $mail->hasTo($email);
        });
    }
}
