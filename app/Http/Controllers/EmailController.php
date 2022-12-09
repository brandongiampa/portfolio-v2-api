<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailController extends Controller
{
    public function store(Request $request) {

        $error_msgs = array();

        if (!isset($request['name'])) array_push($error_msgs, 'User did not include his/her name.');
        if (!isset($request['from_email'])) array_push($error_msgs, 'User did not include his/her email address.');
        if (!isset($request['body'])) array_push($error_msgs, 'User did not include his/her name.');

        if (count($error_msgs) > 0) {
            return response(['messages' => $error_msgs], 400);
        }

        try {
            $to = 'me@brandongiampa.com';
            $subject = 'Message from ' . $request['name'] . '!';
            $message = '';
            $message .= 'Name: ' . $request['name'] . '\n';
            $message .= 'Email Address: ' . $request['from_email'] . '\n\n';
            $message .= $request['body'];
            mail($to,$subject,$message);
        }
        catch(Exception $e) {
            return response(['messages' => [$e->getMessage()]], 500);
        }
    }
}
