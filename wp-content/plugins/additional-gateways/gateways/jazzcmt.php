<?php

namespace SMSGateway;


class JazzCMT {
    // https://connect.jazzcmt.com/sendsms_url.html?Username=03082471906&Password=xxxxxx&From=MASK&To=0300xxxxxxx&Message=MESSAGE

    public static function sendSMS($gateway_fields, $mobile, $message, $test_call) {
        $username = $gateway_fields['username'];
        $password = $gateway_fields['password'];
        $sender = $gateway_fields['sender'];

        return self::process_sms($username, $password, $sender, $mobile, $message, $test_call);
    }

    public static function process_sms($username, $password, $sender, $mobile, $message, $test_call) {
        $curl = curl_init();
        $params = array(
            'Username' => $username,
            'Password' => $password,
            'From' => $sender,
            'To' => $mobile,
            'Message' => $message,
        );
        $encoded_query = http_build_query($params);
        curl_setopt($curl, CURLOPT_URL, 'https://connect.jazzcmt.com/sendsms_url.html?' . $encoded_query);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_errno($curl);
        curl_close($curl);

        if($test_call) return $result;

        if ($curl_error !== 0) {
            return false;
        }

        $is_success = 200 <= $code && $code < 300;

        return $is_success;
    }

}
