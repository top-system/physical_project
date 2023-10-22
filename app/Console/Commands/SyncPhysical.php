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
    protected $signature = 'sync:physical';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->getEvents();
        return Command::SUCCESS;
    }

    public function getEvents()
    {
        $eventDb = DB::connection('mongodb')->collection('events');
        $api = ApiFootballService::getInstance();
        $response = $api->getEvents([]);
        $translate = Translate::getInstance();
        foreach ($response as &$resp) {
            $resp['country_name'] = $translate->to($resp['country_name']);
            $resp['league_name'] = $translate->to($resp['league_name']);
            $resp['match_hometeam_name'] = $translate->to($resp['match_hometeam_name']);
            $resp['match_awayteam_name'] = $translate->to($resp['match_awayteam_name']);
            $resp['match_referee'] = $translate->to($resp['match_referee']);
            $resp['stage_name'] = $translate->to($resp['stage_name']);
            $resp['match_stadium'] = $translate->to($resp['match_stadium']);
            $resp['match_round'] = $translate->to($resp['match_round']);
            foreach ($resp['goalscorer'] as &$goalscorer) {
                $goalscorer['away_scorer'] = $translate->to($goalscorer['away_scorer']);
                $goalscorer['away_assist'] = $translate->to($goalscorer['away_assist']);
                $goalscorer['home_scorer'] = $translate->to($goalscorer['home_scorer']);
                $goalscorer['home_assist'] = $translate->to($goalscorer['home_assist']);
                $goalscorer['score_info_time'] = $translate->to($goalscorer['score_info_time']);
            }
            foreach ($resp['cards'] as &$cards) {
                $cards['home_fault'] = $translate->to($cards['home_fault']);
                $cards['card'] = $translate->to($cards['card']);
                $cards['away_fault'] = $translate->to($cards['away_fault']);
                $cards['score_info_time'] = $translate->to($cards['score_info_time']);
            }
            foreach ($resp['substitutions']['home'] as &$subsHome) {
                $subsHome['substitution'] = $translate->to($subsHome['substitution']);
            }
            foreach ($resp['substitutions']['away'] as &$subsAway) {
                $subsAway['substitution'] = $translate->to($subsAway['substitution']);
            }
            foreach ($resp['statistics_1half'] as &$statis1half) {
                $statis1half['type'] = $translate->to($statis1half['type']);
            }
            foreach ($resp['statistics'] as &$statistics) {
                $statistics['type'] = $translate->to($statistics['type']);
            }
            foreach ($resp['lineup']['home']['starting_lineups'] as &$starting_lineups) {
                $starting_lineups['lineup_player'] = $translate->to($starting_lineups['lineup_player']);
            }
            foreach ($resp['lineup']['home']['substitutes'] as &$substitutes) {
                $substitutes['lineup_player'] = $translate->to($substitutes['lineup_player']);
            }
            foreach ($resp['lineup']['home']['coach'] as &$coach) {
                $coach['lineup_player'] = $translate->to($coach['lineup_player']);
            }
            unset($coach);
            foreach ($resp['lineup']['away']['starting_lineups'] as &$starting_lineups) {
                $starting_lineups['lineup_player'] = $translate->to($starting_lineups['lineup_player']);
            }
            unset($starting_lineups);
            foreach ($resp['lineup']['away']['substitutes'] as &$substitutes) {
                $substitutes['lineup_player'] = $translate->to($substitutes['lineup_player']);
            }
            unset($coach);
            foreach ($resp['lineup']['away']['coach'] as &$coach) {
                $coach['lineup_player'] = $translate->to($coach['lineup_player']);
            }
            $ret = $eventDb->where('match_id',$resp['match_id'])->first();
            if ($ret){
                $eventDb->where('match_id',$resp['match_id'])->update($resp);
            }else{
                $eventDb->insert($resp);
            }
        }
        return true;
    }
}
