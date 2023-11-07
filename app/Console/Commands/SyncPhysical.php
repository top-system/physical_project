<?php

namespace App\Console\Commands;

use App\Models\FootBallEvent;
use Illuminate\Console\Command;
use App\Services\ApiFootballService;
use App\Services\Translate;
use Illuminate\Support\Facades\DB;

class SyncPhysical extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:physical {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    private $eventDb;
    private $apiFootball;
    private $translate;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $action = $this->argument('action');
        $this->eventDb = DB::connection('mongodb')->collection('events');
        $this->apiFootball = ApiFootballService::getInstance();
        $this->translate = Translate::getInstance();

        if ($action == "init"){
            $this->getEvents(date("Y-m-d",strtotime("+1 day")),date("Y-m-d",strtotime("+1 day")));
            $this->getEvents(date("Y-m-d",strtotime("+2 day")),date("Y-m-d",strtotime("+2 day")));
            $this->getEvents(date("Y-m-d",strtotime("+3 day")),date("Y-m-d",strtotime("+3 day")));
            $this->getEvents(date("Y-m-d",strtotime("+4 day")),date("Y-m-d",strtotime("+4 day")));
            $this->getEvents(date("Y-m-d",strtotime("+5 day")),date("Y-m-d",strtotime("+5 day")));
        }else if ($action == "sync"){
            $this->getEvents(date("Y-m-d",strtotime("+5 day")),date("Y-m-d",strtotime("+5 day")));
        }else if ($action == "initCache"){
            (new Translate())->initCache();
        }
        else{
            $this->getEvents(date("Y-m-d"),date("Y-m-d"));
        }
        return Command::SUCCESS;
    }

    public function getEvents($from,$to)
    {

        $response = $this->apiFootball->getEvents(["from" => $from,"to" => $to]);
        foreach ($response as &$resp) {
            $resp['country_name'] = $this->translate->to($resp['country_name']);
            $resp['league_name'] = $this->translate->to($resp['league_name']);
            $resp['match_hometeam_name'] = $this->translate->to($resp['match_hometeam_name']);
            $resp['match_awayteam_name'] = $this->translate->to($resp['match_awayteam_name']);
            $resp['match_referee'] = $this->translate->to($resp['match_referee']);
            $resp['match_stadium'] = $this->translate->to($resp['match_stadium']);
            $resp['match_round'] = $this->translate->to($resp['match_round']);
            foreach ($resp['goalscorer'] as &$goalscorer) {
                $goalscorer['away_scorer'] = $this->translate->to($goalscorer['away_scorer']);
                $goalscorer['away_assist'] = $this->translate->to($goalscorer['away_assist']);
                $goalscorer['home_scorer'] = $this->translate->to($goalscorer['home_scorer']);
                $goalscorer['home_assist'] = $this->translate->to($goalscorer['home_assist']);
                $goalscorer['score_info_time'] = $this->translate->to($goalscorer['score_info_time']);
            }
            foreach ($resp['cards'] as &$cards) {
                $cards['home_fault'] = $this->translate->to($cards['home_fault']);
                $cards['card'] = $this->translate->to($cards['card']);
                $cards['away_fault'] = $this->translate->to($cards['away_fault']);
                $cards['score_info_time'] = $this->translate->to($cards['score_info_time']);
            }
            foreach ($resp['substitutions']['home'] as &$subsHome) {
                $subsHome['substitution'] = $this->translate->to($subsHome['substitution']);
            }
            foreach ($resp['substitutions']['away'] as &$subsAway) {
                $subsAway['substitution'] = $this->translate->to($subsAway['substitution']);
            }
            foreach ($resp['statistics_1half'] as &$statis1half) {
                $statis1half['type'] = $this->translate->to($statis1half['type']);
            }
            foreach ($resp['statistics'] as &$statistics) {
                $statistics['type'] = $this->translate->to($statistics['type']);
            }
            foreach ($resp['lineup']['home']['starting_lineups'] as &$starting_lineups) {
                $starting_lineups['lineup_player'] = $this->translate->to($starting_lineups['lineup_player']);
            }
            foreach ($resp['lineup']['home']['substitutes'] as &$substitutes) {
                $substitutes['lineup_player'] = $this->translate->to($substitutes['lineup_player']);
            }
            foreach ($resp['lineup']['home']['coach'] as &$coach) {
                $coach['lineup_player'] = $this->translate->to($coach['lineup_player']);
            }
            unset($coach);
            foreach ($resp['lineup']['away']['starting_lineups'] as &$starting_lineups) {
                $starting_lineups['lineup_player'] = $this->translate->to($starting_lineups['lineup_player']);
            }
            unset($starting_lineups);
            foreach ($resp['lineup']['away']['substitutes'] as &$substitutes) {
                $substitutes['lineup_player'] = $this->translate->to($substitutes['lineup_player']);
            }
            unset($coach);
            foreach ($resp['lineup']['away']['coach'] as &$coach) {
                $coach['lineup_player'] = $this->translate->to($coach['lineup_player']);
            }
            $ret = $this->eventDb->where('match_id',$resp['match_id'])->first();
            if ($ret){
                $this->eventDb->where('match_id',$resp['match_id'])->update($resp);
            }else{
                $this->eventDb->insert($resp);
            }
            unset($resp);
        }
        return true;
    }
}
