<?php
namespace PixelYourSite;



defined('ABSPATH') or die('Direct access not allowed');


class FacebookAsyncTask extends \WP_Async_Task {
    protected $action = 'pys_send_server_event';

    protected function prepare_data($data) {
        try {
            if (!empty($data)) {
                return array('data' => base64_encode(serialize($data)));
            }
        } catch (\Exception $ex) {
            error_log($ex);
        }

        return array();
    }

    protected function run_action() {
        try {
            $data = unserialize(base64_decode($_POST['data']));

            $events = is_array($data[0]) ? $data[0] : $data ;
            if (empty($events)) {
                return;
            }
            foreach ($events as $event) {
                FacebookServer()->sendEvent($event["pixelIds"],[$event["event"]]);
            }

        }
        catch (\Exception $ex) {
            error_log($ex);
        }
    }
}