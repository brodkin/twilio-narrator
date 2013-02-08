<?php

class Voice extends BaseController
{
    /**
     * Return Main Menu TwiML
     *
     * @return void
     */
    public function start()
    {
        $sid = Input::get('AccountSid');
        $incoming = Input::get('To');

        $response = new Services_Twilio_Twiml();
        $response->say('This is the main menu for SID '.$sid);
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        print $response;
    }

    /**
     * Return Content TwiML
     *
     * @return void
     */
    public function content($position)
    {
        $sid = Input::get('AccountSid');
        $incoming = Input::get('To');

        $response = new Services_Twilio_Twiml();
        $response->say('This is content.');
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        print $response;
    }

}
