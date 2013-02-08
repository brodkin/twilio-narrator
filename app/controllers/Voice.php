<?php

class Voice extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index($position)
    {
        $sid = Input::get('AccountSid');
        $incoming = Input::get('To');

        $response = new Services_Twilio_Twiml();
        $response->say('Hello, your sid is '.$sid);
        $response->play('https://api.twilio.com/cowbell.mp3', array("loop" => 5));
        print $response;
    }

}
