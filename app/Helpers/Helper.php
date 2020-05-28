<?php


namespace App\Helpers;




use App\bigbluebutton\src\Parameters\CreateMeetingParameters;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Parameters\JoinMeetingParameters;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class Helper
{

     public static function paginate($items, $perPage = null, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
    public static function get_local_time(){

        $ip = file_get_contents("http://ipecho.net/plain");

        $url = 'http://ip-api.com/json/'.$ip;

        $tz = file_get_contents($url);

        $tz = json_decode($tz,true)['timezone'];

        return $tz;

    }

    public static function createMeeting($params)
    {
        $bbb = new BigBlueButton();
        $createMeetingParams = new CreateMeetingParameters($params['meetingUrl'] , $params['meetingName']);
        $createMeetingParams->setAttendeePassword($params['attendeePassword']);
        $createMeetingParams->setModeratorPassword($params['moderatorPassword']);
        $createMeetingParams->setLogoutUrl($params['logoutUrl']);
        if (isset($params['muteAllUser']))
        {
            $createMeetingParams->setMuteOnStart($params['muteAllUser']);
            $createMeetingParams->setLockSettingsDisableMic($params['muteAllUser']);
        }
        if (isset($params['moderator_approval']))
        {
            $params['moderator_approval'] ? $createMeetingParams->setModerateJoin() :$createMeetingParams->setOpenJoin();
        }
        if (isset($params['setRecord']))
        {
            $createMeetingParams->setRecord($params['setRecord']);
            $createMeetingParams->setAllowStartStopRecording($params['setRecord']);
        }
        if (isset($params['welcome_message']))
        {
            $createMeetingParams->setWelcomeMessage($params['welcome_message']);
        }

        $response = $bbb->createMeeting($createMeetingParams);
        return $response;
    }

    public static function joinMeeting($params)
    {
        $bbb = new BigBlueButton();
        $joinMeetingParams = new JoinMeetingParameters($params['meetingId'], $params['username'], $params['password']);
        $joinMeetingParams->setRedirect(true);
        $apiUrl = $bbb->getJoinMeetingURL($joinMeetingParams);
        return $apiUrl;
    }

}