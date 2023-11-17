<?php


namespace Modules\Notification\Services;


use Carbon\Carbon;
use Modules\ALL\Models\TgChannelText;

class DeadlineCheckerService
{
    public Carbon $date;

    public function getHasCome()
    {
        $searched = '%deadline%';
        $deadlines = TgChannelText::where('message', 'LIKE', $searched)->get()->toArray();

        foreach ($deadlines as $deadline) {
            $str = $deadline['message'];
            preg_match_all('/\d{4}-\d{2}-\d{2}/', $str, $matches);
            $now = $this->date->format('Y-m-d');
            try {
                [[$m]] = $matches;
            } catch (\Exception $e) {
                $m = '';
            }
            if (strtotime($m) > strtotime($now)) {
                $comes[] = $m;
            }
        }
    }

    public function __construct()
    {
        $this->date = new Carbon('now');
    }
}
