<?php
use LanguageDetection\Language;
require __DIR__ . '/vendor/autoload.php';

$ig = new \InstagramAPI\Instagram();

$climate = new \League\CLImate\CLImate();
$climate->green()->bold(
    "
                                          _                ___
  /\  /\_   _ _ __   ___ _ ____   _____ | |_ ___ _ __    / _ \_ __ ___
 / /_/ / | | | '_ \ / _ \ '__\ \ / / _ \| __/ _ \ '__|  / /_)/ '__/ _ \
/ __  /| |_| | |_) |  __/ |   \ V / (_) | ||  __/ |    / ___/| | | (_) |
\/ /_/  \__, | .__/ \___|_|    \_/ \___/ \__\___|_|    \/    |_|  \___/
        |___/|_|                                                      "
);
$climate->out('');
$climate->green()->bold('Hypervoter Pro Terminal');
$climate->green()->bold('v3.5.9.1');
$climate->out('');
$climate->green('© Developed by HyperVoter (https://hypervoter.com)');
$climate->out('');
$option = getopt('g::');
if (isset($option['g'])) {

    generator($ig, $climate);
} elseif (isset($argv[1])) {
	$conf_name = $argv[1];
	
	$datajson = null;
    run($ig, $climate, $conf_name, $datajson);
} else {
	 run($ig, $climate);
}
/**
 * Json config generator
 */

function generator($ig, $climate)
{
    $climate->backgroundBlueWhite('Hypervote Config Generator is Starting... ');
    $climate->out('');
    sleep(2);
    $climate->out('Please provide a valid license key from your Dashboard on Hypervoter.com (https://hypervoter.com).');
    sleep(1);
    $climate->out('Example: j5tkjkl4f7e595e9008bb77acc599453');
    $license_key = getVarFromUser('License key');
    if (empty($license_key)) {
        do {
            $license_key = getVarFromUser('License key');
        } while (empty($license_key));
    }
    $license_key = str_replace(' ', '', $license_key);
	
	
    $license_status = activate_license($license_key, $ig, $climate);
    if ('valid' === $license_status) {
        $climate->out('You license active and valid. Processing...');
    } else {
        $climate->out('You license key not valid.');
    }
    sleep(1);
    $climate->out('Please provide login data of your Instagram Account.');
    $login = getVarFromUser('Login');
    if (empty($login)) {
        do {
            $login = getVarFromUser('Login');
        } while (empty($login));
    }
    sleep(1);
    $password = getVarFromUser('Password');
    if (empty($password)) {
        do {
            $password = getVarFromUser('Password');
        } while (empty($password));
    }
    $first_loop = true;
    do {
        if ($first_loop) {
            $climate->out("(Optional) Set proxy, if needed. It's better to use a proxy from the same country where you running this script.");
            $climate->out('Proxy should match following pattern:');
            $climate->out('http://ip:port or http://username:password@ip:port');
            $climate->out("Don't use in pattern https://.");
            $climate->out("Type 3 to skip and don't use proxy.");
            $first_loop = false;
        } else {
            $climate->out('Proxy - [NOT VALID]');
            $climate->out('Please check the proxy syntax and try again.');
        }
        $proxy = getVarFromUser('Proxy');
        if (empty($proxy)) {
            do {
                $proxy = getVarFromUser('Proxy');
            } while (empty($proxy));
        }
        if ('3' === $proxy) {
            $proxy = '3';
            break;
        }
    } while (!isValidProxy($proxy, $climate));
    $climate->out('Please choose the Hypervote estimated speed.');
    $climate->out('Type integer value without spaces from 1 to 1 500 000 stories/day or 0 for maximum possible speed.');
    $climate->out('We recommend you set 400000 stories/day. This speed works well for a long time without exceeding the limits.');
    $climate->out('When you are using the maximum speed you may exceed the Hypervote limits per day if this account actively used by a user in the Instagram app at the same time.');
    $climate->out('If you are using another type of automation, we recommend to you reducing Hypervote speed and find your own golden ratio.');
    $speed = (int) getVarFromUser('Speed');
    if ($speed > 1500000) {
        do {
            $climate->out('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
            $climate->out('Type 0 for maximum speed.');
            $speed = (int) getVarFromUser('Delay');
        } while ($speed > 1500000);
    }
    if (0 == $speed) {
        $climate->out('Maximum speed enabled.');
    } else {
        $climate->out('Speed set to ' . $speed . ' stories/day.');
    }
    $climate->out('Experimental features:');
    $climate->out('Voting only fresh stories, which posted no more than X hours ago.');
    $climate->out('X - is integer value from 1 to 23.');
    $climate->out('Type 0 to skip this option.');
    $climate->out('This option will reduce speed, but can increase results of Hypervote.');
    $fresh_stories_range = 0;
    if ($fresh_stories_range > 23) {
        do {
            $climate->out('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
            $climate->out('Type 0 for skip this option.');
            $fresh_stories_range = 0;
        } while ($fresh_stories_range > 23);
    }
    $defined_targs = getVarFromUser('Please define your targets. Use only usernames without "@" sign');
    $q_answers = (int) getVarFromUser('Is Question Answers active? (0/1)');
    $q_vote = (int) getVarFromUser('Is Poll Vote active? (0/1)');
	$q_countdown = (int) getVarFromUser('Is Countdown Vote active? (0/1)');
	$q_post_like = (int) getVarFromUser('Is Post Like active? (0/1)');
	$q_post_like_speed = (int) getVarFromUser('How many Like Speed Per day? (0-3000)');
	$q_post_timeline_like = (int) getVarFromUser('Is Timeline Post  Like active? (0/1)');
	$q_post_comment = (int) getVarFromUser('Is Post Comment active? (0/1)');
	$q_post_comment_per_people = (int) getVarFromUser('How many comment per user? (Default is 1 you can type 0/10)');
	 $comment_text = getVarFromUser('Please provide comment text or emoji. (comma seperated. For Ex: hello,good post!,oh dear)');
	$q_post_comment_speed = (int) getVarFromUser('How many Comment Speed Per day? (0-3000)');
	$q_post_comment_like = (int) getVarFromUser('Is Post Comment Like active? (0/1)');
	$q_post_comment_like_speed = (int) getVarFromUser('How many Comment Like Speed Per day? (0-10000)');
	$q_post_like_per_people = (int) getVarFromUser('How Many Post Like Per People?? (0-10)');
	$q_follow = (int) getVarFromUser('Is Follow active? (0/1)');
	$q_follow_speed = (int) getVarFromUser('How many follow Speed Per day? (0-3000)');
	$q_unfollow = (int) getVarFromUser('Is unFollow active? (0/1)');
	$q_unfollow_speed = (int) getVarFromUser('How many Unfollow Speed Per day? (0-3000)');
	$q_unfollow_interval = (int) getVarFromUser('How many day after unfollow followed accounts? (Second type : 86000 = 1 Day.)');
	$q_telegram = (int) getVarFromUser('Do you want to activate telegram notification? (0/1)');
	$q_auto_confirm = (int) getVarFromUser('Do you want to auto confirm targets? (0/1)');
	$q_telegram_stats = (int) getVarFromUser('Do you want to activate telegram Stats notification? (0/1)');
	$q_telegram_error = (int) getVarFromUser('Do you want to activate telegram Error notification? (0/1)');
	$q_telegram_username =  getVarFromUser('Can you provide your telegram username?');
	
	 $q_delay_telegram = (int) getVarFromUser('How many second delay for Telegram Reports? (you can put delay seconds for report messages.Default : 86400 (1 day) delay for reports.)');
	
	
	$q_telegram_chat_id = getVarFromUser('Can you provide your telegram chat id? (Check telegram @userinfobot for find your id');
	$q_language_enable = (int) getVarFromUser('Is Language Detection and Multi Language support for Question & Answer active? (0/1)');
	
	$q_filter = (int) getVarFromUser('Choice Filter: 0 is Liker - 1 is Follower, 2 is all filter at same time. (0/2)');
	$q_verified = (int) getVarFromUser('Your account is verified?(BlueBadge) 0 -> No 1 -> Yes');
	$q_masslookingv2 = (int) getVarFromUser('Masslookingv2 is enable? 0 is no 1 is Yes.');
	$q_masslookingv2_speed = (int) getVarFromUser('Masslookingv2 speed? (1000, 2000, 3000, 4000, 5000, 10000, 15000, 20000 and 25000. Other speed is have ban risk for standard acc.)');
    $q_slide = (int) getVarFromUser('Is Slide Points active? (0/1)');
	$q_business = (int) getVarFromUser('Do you want to ignore business profile? (0/1)');
	$q_follow = (int) getVarFromUser('Do you want to ignore Already your follower people stories? (0/1)');
	$q_following = (int) getVarFromUser('Do you want to ignore Already your following people stories? (0/1)');
	$q_sameuser = (int) getVarFromUser('Do you want to ignore same people multiple stories? (0/1)');
	$q_requested = (int) getVarFromUser('Do you want to ignore your follow requested people stories? (0/1)');
    $q_quiz = (int) getVarFromUser('Is Quiz Answers active? (0/1)');
	 $q_mention = (int) getVarFromUser('Is Bio Mention active? (0/1)');
    $q_stories = (int) getVarFromUser('Is Story Masslooking Active? (0/1)');
    $climate->out('Please use this option with caution.Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram.');
    if (0 !== $q_answers) {
        $q_answers_a = getVarFromUser('Please provide your answers (comma seperated. For Ex: hello,hi there,oh dear)');
    }
	if (0 !== $q_language_enable) {
        $q_answers_ar = getVarFromUser('Please provide your answers for Arabic (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_en = getVarFromUser('Please provide your answers for English (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_fr = getVarFromUser('Please provide your answers for French (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_de = getVarFromUser('Please provide your answers for German (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_tr = getVarFromUser('Please provide your answers for Turkish (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_id = getVarFromUser('Please provide your answers for Indonesian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_in = getVarFromUser('Please provide your answers for Hindi (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_ru = getVarFromUser('Please provide your answers for Russian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_jp = getVarFromUser('Please provide your answers for Japan (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_cn = getVarFromUser('Please provide your answers For Chineese (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_it = getVarFromUser('Please provide your answers for Italian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_es = getVarFromUser('Please provide your answers for Spainish (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_pt = getVarFromUser('Please provide your answers for Portuguese (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_ir = getVarFromUser('Please provide your answers For Farsi or Persian (comma seperated. For Ex: hello,hi there,oh dear)');
		
    }
	 if (!empty($q_answers_ar)) {
        $qs_ar = explode(',', $q_answers_ar);
    } else {
        $qs_ar = array();
    }
	 if (!empty($q_answers_en)) {
        $qs_en = explode(',', $q_answers_en);
    } else {
        $qs_en = array();
    }
	if (!empty($q_answers_fr)) {
        $qs_fr = explode(',', $q_answers_fr);
    } else {
        $qs_fr = array();
    }
	if (!empty($q_answers_de)) {
        $qs_de = explode(',', $q_answers_de);
    } else {
        $qs_de = array();
    }
	if (!empty($q_answers_tr)) {
        $qs_tr = explode(',', $q_answers_tr);
    } else {
        $qs_tr = array();
    }
	if (!empty($q_answers_id)) {
        $qs_id = explode(',', $q_answers_id);
    } else {
        $qs_id = array();
    }
	if (!empty($q_answers_in)) {
        $qs_in = explode(',', $q_answers_in);
    } else {
        $qs_in = array();
    }
	if (!empty($q_answers_ru)) {
        $qs_ru = explode(',', $q_answers_ru);
    } else {
        $qs_ru =  array();
    }
	if (!empty($comment_text)) {
        $ct_ex = explode(',', $comment_text);
    } else {
         $ct_ex  =  array();
    }
	if (!empty($q_answers_jp)) {
        $qs_jp = explode(',', $q_answers_jp);
    } else {
        $qs_jp  =  array();
    }
	if (!empty($q_answers_cn)) {
        $qs_cn = explode(',', $q_answers_cn);
    } else {
        $qs_cn  =  array();
    }
	if (!empty($q_answers_it)) {
        $qs_it = explode(',', $q_answers_it);
    } else {
        $qs_it  =  array();
    }
	if (!empty($q_answers_es)) {
        $qs_es = explode(',', $q_answers_es);
    } else {
        $qs_es = array();
    }
	if (!empty($q_answers_pt)) {
        $qs_pt = explode(',', $q_answers_pt);
    } else {
        $qs_pt =  array();
    }
	if (!empty($q_answers_ir)) {
        $qs_ir= explode(',', $q_answers_ir);
    } else {
        $qs_ir = array();
    }
    if (0 !== $q_slide) {
        $q_slide_points_min = (int) getVarFromUser('Please Provide Min. Slide Points (0/100)');
        $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
        do {
            $climate->errorBold('Max value can not set lower than min value. Max value must set ' . ($q_slide_points_min + 1) . ' or bigger!');
            $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
        } while ($q_slide_points_min > $q_slide_points_max);
    } else {
        $q_slide_points_min = 0;
        $q_slide_points_max = 100;
    }
    if (!empty($q_answers_a)) {
        $qs = explode(',', $q_answers_a);
    } else {
        $qs = array();
    }
    $data = array(
        'license_key' => $license_key,
        'username' => $login,
        'password' => $password,
        'proxy' => $proxy,
        'speed_value' => $speed,
		'telegram_username' => $q_telegram_username ? $q_telegram_username : null,
		'masslookingv2' => (0 === $q_masslookingv2) ? false : true,
		'masslookingv2_speed' => $q_masslookingv2_speed,
		'is_verified' => (0 === $q_verified) ? false : true,
		'is_auto_confirm' =>(0 === $q_auto_confirm) ? false : true,
		'is_telegram_error_active' => (0 === $q_telegram_stats) ? false : true,
		'is_telegram_stats_active' => (0 === $q_telegram_error) ? false : true,
		'telegram_delay' => $q_delay_telegram,
		'business' => (0 === $q_business) ? false : true,
		'is_multi_language_active' => (0 === $q_language_enable) ? false : true,
		 'is_telegram_active' => (0 === $q_telegram) ? false : true,
		 'is_telegram_chat_id' => $q_telegram_chat_id ? $q_telegram_chat_id : null,
        'targets' => $defined_targs,
		'filter' => $q_filter,
		'comment_text' => $ct_ex,
        'fresh_stories_range' => 5,
        'is_poll_vote_active' => (0 === $q_vote) ? false : true,
		'post_like' => (0 === $q_post_like) ? false : true,
		'timeline_post_like' => (0 === $q_post_like) ? false : true,
		'post_comment' => (0 === $q_post_comment) ? false : true,
		'post_comment_like' => (0 === $q_post_comment_like) ? false : true,
		'post_like_per_people' => $q_post_like_per_people,
		'post_comment_per_people' => $q_post_comment_per_people,
		'follow' => (0 === $q_follow) ? false : true,
		'unfollow' => (0 === $q_unfollow) ? false : true,
		'unfollow_interval' => (0 === $q_unfollow_interval) ? false : true,
		'comment_speed' => $q_post_comment_speed,
		'like_speed' => $q_post_like_speed,
		'follow_speed' => $q_follow_speed,
		'unfollow_speed' => $q_unfollow_speed,
		'comment_like_speed' => $q_post_comment_like_speed,
		 'is_mention_active' => (0 === $q_mention) ? false : true,
		'is_countdown_active' => (0 === $q_countdown) ? false : true,
		  'is_follower_ignore_active' => (0 === $q_follow) ? false : true,
		  'is_following_ignore_active' => (0 === $q_following) ? false : true,
		  'is_same_user_ignore_active' => (0 === $q_sameuser) ? false : true,
		  'is_requested_ignore_active' => (0 === $q_requested) ? false : true,
        'is_slider_points_active' => (0 === $q_slide) ? false : true,
        'is_questions_answers_active' => (0 === $q_answers) ? false : true,
        'is_quiz_answers_active' => (0 === $q_quiz) ? false : true,
        'is_mass_story_vivew_active' => (0 === $q_stories) ? false : true,
        'questions_answers' => $qs,
		'questions_answers_ar' => $qs_ar,
		'questions_answers_en' => $qs_en,
		'questions_answers_de' => $qs_de,
		'questions_answers_fr' => $qs_fr,
		'questions_answers_tr' => $qs_tr,
		'questions_answers_id' => $qs_id,
		'questions_answers_in' => $qs_in,
		'questions_answers_ru' => $qs_ru,
		'questions_answers_jp' => $qs_jp,
		'questions_answers_cn' => $qs_cn,
		'questions_answers_it' => $qs_it,
		'questions_answers_es' => $qs_es,
		'questions_answers_pt' => $qs_pt,
		'questions_answers_ir' => $qs_ir,
        'slider_points_range' => array(
            ($q_slide_points_min) ? $q_slide_points_min : 0,
            ($q_slide_points_max) ? $q_slide_points_max : 100,
        ),
    );
	
    $choicebaby = $climate->confirm('All values are set. Do you want to save this configuration?');
    if ($choicebaby->confirmed()) {
        $filename = getVarFromUser('Please set a name for file');
        if (file_exists(__DIR__ .'/config/config-' . $filename . '.json')) {
            $climate->errorBold('File ' . $filename . ' already exists. Set a different name');
            $filename = getVarFromUser('Please set a name for file');
        }
        $fp = fopen(__DIR__ .'/config/config-' . $filename . '.json', 'w');
        fwrite($fp, json_encode($data));
        fclose($fp);
        $climate->infoBold('Config file ' . $filename . ' successfully saved. Hyperloop starting in 3 seconds...');
        sleep(2);
        run($ig, $climate, $filename, null);
    } else {
        $choice2 = $climate->confirm(' All your changes not saved. Are you sure? ');
        if ($choice2->confirmed()) {
            $climate->info(' Allright. Hyperloop sequence starting with these info in 3 seconds... ');
            sleep(2);
            run($ig, $climate, null, json_encode($data));
        } else {
            $filename = getVarFromUser('Please set a name for file');
            if (file_exists(__DIR__ .'/config/config-' . $filename . '.json')) {
                $climate->errorBold('File ' . $filename . ' already exists. Set a different name');
                $filename = getVarFromUser('Please set a name for file');
            }
            $fp = fopen(__DIR__ .'/config/config-' . $filename . '.json', 'w');
            fwrite($fp, json_encode($data));
            fclose($fp);
            $climate->infoBold('Config file ' . $filename . ' successfully saved. Hyperloop starting in 3 seconds...');
            sleep(3);
            run($ig, $climate, $filename, null);
        }
    }
} // Generator Ends
/**
 * Json config generator
 */
/**
 * Let's start the show
 */

function run($ig, $climate, $conf_name = null, $datajson = null, $confirmation = null)
{
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', '-1');
    try {
        if (null == $datajson) {
            if (null !== $conf_name) {
                $climate->out('Config file name provided by generator. Processing...');
                $config_name = $conf_name;
            } else {
                $climate->out('Please provide an username for config file...');
                $config_name = getVarFromUser('Username');
            }
            if (empty($config_name)) {
                do {
                    $config_name = getVarFromUser('Username');
                } while (empty($config_name));
            }
            $climate->infoBold('Checking for config...');
            sleep(1);
            if (file_exists(__DIR__ .'/config/config-' . $config_name . '.json')) {
                $climate->infoBold('Config file found. Processing...');
                sleep(1);
                $mafile = fopen(__DIR__ .'/config/config-' . $config_name . '.json', 'r');
                $file = fread($mafile, filesize(__DIR__ .'/config/config-' . $config_name . '.json'));
                $data = json_decode($file);
                fclose($mafile);
                $climate->infoBold('Checking & Validating License key...');
                sleep(1);
                if ('' !== $data->license_key) {
					$telegram_username = $data->telegram_username;
					$login = $data->username;
					
                    $license_status = activate_license($data->license_key, $ig, $climate, $telegram_username, $login);
                } else {
                    $climate->out('Please provide a valid license key from your Dashboard on Hypervoter.com (https://hypervoter.com).');
                    $climate->out('Example: j5tkjkl4f7e595e9008bb77acc599453');
                    $license_key = getVarFromUser('License key');
                    if (empty($license_key)) {
                        do {
                            $license_key = getVarFromUser('License key');
                        } while (empty($license_key));
                    }
                    $license_key = str_replace(' ', '', $license_key);
					$telegram_username = $data->telegram_username;
					$login = $data->username;
                    $license_status = activate_license($license_key, $ig, $climate, $telegram_username, $login);
                    if ('valid' === $license_status) {
                        $climate->out('You license active and valid.');
                    } else {
                        $climate->out('You license key not valid.');
                        run($ig, $climate);
                    }
                    sleep(1);
                }
                if ('valid' === $license_status) {
                    $climate->backgroundBlueWhiteBold('  You license key is active and valid. Processing...  ');
                    sleep(1);
                    if ('' !== $data->username) {
                        $climate->infoBold('Username Found');
                        $login = $data->username;
                    } else {
                        $climate->backgroundRedWhiteBold('Username can not empty');
                        sleep(1);
                        exit();
                    }
                    if ('' !== $data->password) {
                        $climate->infoBold('Password Found');
                        $password = $data->password;
                    } else {
                        $climate->backgroundRedWhiteBold('Password can not empty');
                        sleep(1);
                        exit();
                    }
                    if ('3' === $data->proxy) {
                        $climate->infoBold('Proxy Skipping Enabled. Processing ...');
                        $proxy = '3';
                        sleep(1);
                    } else {
                        $climate->infoBold('Proxy Option found. Validating...');
                        sleep(1);
                        $validate_proxy = isValidProxy($data->proxy, $climate);
                        $climate->infoBold('Proxy Status : ' . $validate_proxy);
                        if (200 == $validate_proxy) {
                            $climate->info('Proxy Connected. Processing ...');
                            $proxy = $data->proxy;
                            $ig->setProxy($data->proxy);
                        } else {
                            $proxy = '3';
                            $climate->info('Proxy can not conntected. Skipping...');
                        }
                    }
                    if ($data->speed_value) {
                        $climate->infoBold('Speed Value Found. Processing ... ');
                        $speed = (int) $data->speed_value;
                        if ($speed > 1500000) {
                            do {
                                $climate->errorBold('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
                                usleep(500000);
                                $climate->errorBold('For Maxiumum speed please use "0"... Please set speed now (that will not change your config file)');
                                $speed = (int) getVarFromUser('Speed');
                            } while ($speed > 1500000);
                        }
                        if (0 === $speed) {
                            $climate->infoBold('  Maximum speed enabled.  ');
                            $delay = 15;
                        } else {
                            $climate->infoBold('Speed set to ' . $speed . ' stories/day.');
                            $delay = round(60 * 60 * 24 * 200 / $speed);
                        }
                    }
                    if ($data->fresh_stories_range > 0) {
                        $fresh_stories_range = 0;
                        $climate->infoBold('Experimental Feature (Fresh Stories Range) value found. Validating ...');
                        sleep(1);
                        if ($fresh_stories_range > 23) {
                            do {
                                $climage->errorBold('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
                                $climage->errorBold('Type 0 for skip this option.');
                                $fresh_stories_range = 0;
                            } while ($fresh_stories_range > 23);
                        }
                        $climate->infoBold('Fresh Stories Range set to ' . $fresh_stories_range);
                        sleep(1);
                    } else {
                        $fresh_stories_range = 0;
                        $climate->infoBold('Experimental Feature (Fresh Stories Range) Skipping.');
                        usleep(500000);
                    }
                    $defined_targets = $data->targets;
                    if ($data->is_mass_story_vivew_active) {
                        $climate->backgroundRedWhiteBold('Attention! Mass story View Feature is active. Please use this option with caution. Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram. ');
                    }
                } else {
                    $climate->backgroundRedWhiteBold('  You license key is not valid. Please obtain valid licence key from your Dashboard on Hypervoter.com (https://hypervoter.com)  ');
                    sleep(1);
                    exit();
                }
            } else {
                $climate->backgroundRedWhiteBold(' Config file not found. Manual input starting... ');
                sleep(5);
                $defined_targets = null;
                $climate->out('Please provide a valid license key from your Dashboard on Hypervoter.com (https://hypervoter.com).');
                $climate->out('Example: j5tkjkl4f7e595e9008bb77acc599453');
                $license_key = getVarFromUser('License key');
                if (empty($license_key)) {
                    do {
                        $license_key = getVarFromUser('License key');
                    } while (empty($license_key));
                }
                $license_key = str_replace(' ', '', $license_key);
				
                $license_status = activate_license($license_key, $ig, $climate);
                if ('valid' === $license_status) {
                    $climate->out('You license active and valid.');
                } else {
                    $climate->out('You license key not valid.');
                    run($ig, $climate);
                }
                $climate->out('Please provide login data of your Instagram Account.');
                $login = getVarFromUser('Login');
                if (empty($login)) {
                    do {
                        $login = getVarFromUser('Login');
                    } while (empty($login));
                }
                $password = getVarFromUser('Password');
                if (empty($password)) {
                    do {
                        $password = getVarFromUser('Password');
                    } while (empty($password));
                }
                $first_loop = true;
                do {
                    if ($first_loop) {
                        $climate->out("(Optional) Set proxy, if needed. It's better to use a proxy from the same country where you running this script.");
                        $climate->out('Proxy should match following pattern:');
                        $climate->out('http://ip:port or http://username:password@ip:port');
                        $climate->out("Don't use in pattern https://.");
                        $climate->out("Type 3 to skip and don't use proxy.");
                        $first_loop = false;
                    } else {
                        $climate->out('Proxy - [NOT VALID]');
                        $climate->out('Please check the proxy syntax and try again.');
                    }
                    $proxy = getVarFromUser('Proxy');
                    if (empty($proxy)) {
                        do {
                            $proxy = getVarFromUser('Proxy');
                        } while (empty($proxy));
                    }
                    if ('3' === $proxy) {
                        // Skip proxy setup
                        $proxy = '3';
                        break;
                    }
                } while (!isValidProxy($proxy, $climate));
                $proxy_check = isValidProxy($proxy, $climate);
                if ('3' === $proxy) {
                    $proxy = '3';
                    $climate->info('Proxy Setup Skipped');
                } elseif (500 === $proxy_check) {
                    $proxy = '3';
                    $climate->info('Proxy is not valid. Skipping');
                } else {
                    $climate->info('Proxy - [OK]');
                    $ig->setProxy($proxy);
                }
                $climate->out('Please choose the Hypervote estimated speed.');
                $climate->out('Type integer value without spaces from 1 to 1 500 000 stories/day or 0 for maximum possible speed.');
                $climate->out('We recommend you set 400000 stories/day. This speed works well for a long time without exceeding the limits.');
                $climate->out('When you are using the maximum speed you may exceed the Hypervote limits per day if this account actively used by a user in the Instagram app at the same time.');
                $climate->out('If you are using another type of automation, we recommend to you reducing Hypervote speed and find your own golden ratio.');
                $speed = (int) getVarFromUser('Speed');
                if ($speed > 1500000) {
                    do {
                        $climate->out('Speed value is incorrect. Type integer value from 1 to 1 500 000 stories/day.');
                        $climate->out('Type 0 for maximum speed.');
                        $speed = (int) getVarFromUser('Delay');
                    } while ($speed > 1500000);
                }
                if (0 == $speed) {
                    $climate->out('Maximum speed enabled.');
                    $delay = 46;
                } else {
                    $climate->out('Speed set to ' . $speed . ' stories/day.');
                    $delay = round(60 * 60 * 24 * 200 / $speed);
                }
                $climate->out('Experimental features:');
                $climate->out('Voting only fresh stories, which posted no more than X hours ago.');
                $climate->out('X - is integer value from 1 to 23.');
                $climate->out('Type 0 to skip this option.');
                $climate->out('This option will reduce speed, but can increase results of Hypervote.');
                $fresh_stories_range = 0;
                if ($fresh_stories_range > 23) {
                    do {
                        $climate->out('Fresh stories range value is incorrect. Type integer value from 1 to 23.');
                        $climate->out('Type 0 for skip this option.');
                        $fresh_stories_range = 0;
                    } while ($fresh_stories_range > 23);
                }
				$q_verified = (int) getVarFromUser('Your account is verified?(BlueBadge) 0 -> No 1 -> Yes');
	$q_masslookingv2 = (int) getVarFromUser('Masslookingv2 is enable? 0 is no 1 is Yes.');
	$q_masslookingv2_speed = (int) getVarFromUser('Masslookingv2 speed? (5000, 10000, 15000, 20000 and 25000. Other speed is have ban risk for standard acc.)');
	$q_post_like = (int) getVarFromUser('Is Post Like active? (0/1)');
	$q_post_like_speed = (int) getVarFromUser('How many Like Speed Per day? (0-3000)');
	$q_post_timeline_like = (int) getVarFromUser('Is Timeline Post  Like active? (0/1)');
	$q_post_comment = (int) getVarFromUser('Is Post Comment active? (0/1)');
	$q_post_comment_per_people = (int) getVarFromUser('How many comment per user? (Default is 1 you can type 0/10)');
	  $comment_text = getVarFromUser('Please provide comment text or emoji. (comma seperated. For Ex: hello,good post!,oh dear)');
	$q_post_comment_speed = (int) getVarFromUser('How many Comment Speed Per day? (0-3000)');
	$q_post_comment_like = (int) getVarFromUser('Is Post Comment Like active? (0/1)');
	$q_post_comment_like_speed = (int) getVarFromUser('How many Comment Like Speed Per day? (0-10000)');
	$q_post_like_per_people = (int) getVarFromUser('How Many Post Like Per People?? (0-10)');
	$q_follow = (int) getVarFromUser('Is Follow active? (0/1)');
	$q_follow_speed = (int) getVarFromUser('How many follow Speed Per day? (0-3000)');
	$q_unfollow = (int) getVarFromUser('Is unFollow active? (0/1)');
	$q_unfollow_speed = (int) getVarFromUser('How many Unfollow Speed Per day? (0-3000)');
	$q_unfollow_interval = (int) getVarFromUser('How many day after unfollow followed accounts? (Second type : 86000 = 1 Day.)');
                $q_answers = (int) getVarFromUser('Is Question Answers active? (0/1)');
				$q_language_enable = (int) getVarFromUser('Is Language Detection and Multi Language support for Question & Answer active? (0/1)');
                $q_vote = (int) getVarFromUser('Is Poll Vote active? (0/1)');
				$q_countdown = (int) getVarFromUser('Is Countdown Vote active? (0/1)');
				 $q_delay_telegram = (int) getVarFromUser('How many second delay for Telegram Reports? (you can put delay seconds for report messages.Default : 86400 (1 day) delay for reports.)');
				$q_follow = (int) getVarFromUser('Do you want to ignore Already your follower people stories? (0/1)');
	$q_following = (int) getVarFromUser('Do you want to ignore Already your following people stories? (0/1)');
	$q_telegram = (int) getVarFromUser('Do you want to activate telegram notification? (0/1)');
	$q_telegram_stats = (int) getVarFromUser('Do you want to activate telegram Stats notification? (0/1)');
	$q_telegram_error = (int) getVarFromUser('Do you want to activate telegram Error notification? (0/1)');
	 if (0 !== $q_telegram) {
	$q_telegram_username =  getVarFromUser('Can you provide your telegram username?');
	 }
	
	  $q_telegram_chat_id = getVarFromUser('Can you provide your telegram chat id? (Check telegram @userinfobot for find your id');
	$q_requested = (int) getVarFromUser('Do you want to ignore Follow requested people stories? (0/1)');
	$q_sameuser = (int) getVarFromUser('Do you want to ignore same people multiple stories? (0/1)');
	$q_requested = (int) getVarFromUser('Do you want to ignore your follow requested people stories? (0/1)');
                $q_slide = (int) getVarFromUser('Is Slide Points active? (0/1)');
				  $q_business = (int) getVarFromUser('Do you want ignore business profile? (0/1)');
				$q_filter = (int) getVarFromUser('Choice Filter: 0 is Peoples Post Liker - 1 is Peoples Follower, 2 is all filter at same time. (0-1-2)');
                $q_quiz = (int) getVarFromUser('Is Quiz Answers active? (0/1)');
				   $q_auto_confirm = (int) getVarFromUser('Do you want to auto confirm targets? (0/1)');
				 $q_mention = (int) getVarFromUser('Is Bio Mention active? (0/1)');
                $q_stories = (int) getVarFromUser('Is Story Masslooking Active? (0/1)');
                $climate->out('Please use this option with caution. Our algorithm is optimized for maximum efficiency and human behaviour. As developers, we are not responsible if your account blocked by Instagram.');
                if (0 !== $q_answers) {
                    $q_answers_a = getVarFromUser('Please provide your answers (in comma seperated)');
                }
				if (0 !== $q_language_enable) {
        $q_answers_ar = getVarFromUser('Please provide your answers for Arabic (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_en = getVarFromUser('Please provide your answers for English (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_fr = getVarFromUser('Please provide your answers for French (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_de = getVarFromUser('Please provide your answers for German (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_tr = getVarFromUser('Please provide your answers for Turkish (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_id = getVarFromUser('Please provide your answers for Indonesian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_in = getVarFromUser('Please provide your answers for Hindi (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_ru = getVarFromUser('Please provide your answers for Russian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_jp = getVarFromUser('Please provide your answers for Japan (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_cn = getVarFromUser('Please provide your answers For Chineese (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_it = getVarFromUser('Please provide your answers for Italian (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_es = getVarFromUser('Please provide your answers for Spainish (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_pt = getVarFromUser('Please provide your answers for Portuguese (comma seperated. For Ex: hello,hi there,oh dear)');
		$q_answers_ir = getVarFromUser('Please provide your answers For Farsi or Persian (comma seperated. For Ex: hello,hi there,oh dear)');
		
    }
                if (0 !== $q_slide) {
                    $q_slide_points_min = (int) getVarFromUser('Please Provide Min. Slide Points (0/100)');
                    $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
                    do {
                        $climate->errorBold('Max value can not set lower than min value. Max value must set ' . ($q_slide_points_min) . ' or bigger!');
                        $q_slide_points_max = (int) getVarFromUser('Please Provide Max. Slide Points (0/100)');
                    } while ($q_slide_points_min > $q_slide_points_max);
                } else {
                    $q_slide_points_min = 0;
                    $q_slide_points_max = 100;
                }
				  if (!empty($q_answers_ar)) {
        $qs_ar = explode(',', $q_answers_ar);
    } else {
        $qs_ar = array();
    }
	if (!empty($comment_text)) {
        $ct_ex = explode(',', $comment_text);
    } else {
         $ct_ex  =  array();
    }
	 if (!empty($q_answers_en)) {
        $qs_en = explode(',', $q_answers_en);
    } else {
        $qs_en = array();
    }
	if (!empty($q_answers_fr)) {
        $qs_fr = explode(',', $q_answers_fr);
    } else {
        $qs_fr = array();
    }
	if (!empty($q_answers_de)) {
        $qs_de = explode(',', $q_answers_de);
    } else {
        $qs_de = array();
    }
	if (!empty($q_answers_tr)) {
        $qs_tr = explode(',', $q_answers_tr);
    } else {
        $qs_tr = array();
    }
	if (!empty($q_answers_id)) {
        $qs_id = explode(',', $q_answers_id);
    } else {
        $qs_id = array();
    }
	if (!empty($q_answers_in)) {
        $qs_in = explode(',', $q_answers_in);
    } else {
        $qs_in = array();
    }
	if (!empty($q_answers_ru)) {
        $qs_ru = explode(',', $q_answers_ru);
    } else {
        $qs_ru =  array();
    }
	if (!empty($q_answers_jp)) {
        $qs_jp = explode(',', $q_answers_jp);
    } else {
        $qs_jp  =  array();
    }
	if (!empty($q_answers_cn)) {
        $qs_cn = explode(',', $q_answers_cn);
    } else {
        $qs_cn  =  array();
    }
	if (!empty($q_answers_it)) {
        $qs_it = explode(',', $q_answers_it);
    } else {
        $qs_it  =  array();
    }
	if (!empty($q_answers_es)) {
        $qs_es = explode(',', $q_answers_es);
    } else {
        $qs_es = array();
    }
	if (!empty($q_answers_pt)) {
        $qs_pt = explode(',', $q_answers_pt);
    } else {
        $qs_pt =  array();
    }
	if (!empty($q_answers_ir)) {
        $qs_ir= explode(',', $q_answers_ir);
    } else {
        $qs_ir = array();
    }
                if (!empty($q_answers_a)) {
                    $qs = explode(',', $q_answers_a);
                } else {
                    $qs = array();
                }
                $datas = json_encode(
                    array(
                        'is_poll_vote_active' => (0 === $q_vote) ? false : true,
						'filter' => $q_filter,
                        'is_slider_points_active' => (0 === $q_slide) ? false : true,
						'masslookingv2' => (0 === $q_masslookingv2) ? false : true,
						'post_like' => (0 === $q_post_like) ? false : true,
		'timeline_post_like' => (0 === $q_post_like) ? false : true,
		'post_comment' => (0 === $q_post_comment) ? false : true,
		'post_comment_per_people' => $q_post_comment_per_people,
		   'comment_text' => $ct_ex,
		'post_comment_like' => (0 === $q_post_comment_like) ? false : true,
		'post_like_per_people' => $q_post_like_per_people,
		'follow' => (0 === $q_follow) ? false : true,
		'unfollow' => (0 === $q_unfollow) ? false : true,
		'unfollow_interval' => (0 === $q_unfollow_interval) ? false : true,
		'comment_speed' => $q_post_comment_speed,
		'like_speed' => $q_post_like_speed,
		'follow_speed' => $q_follow_speed,
		'unfollow_speed' => $q_unfollow_speed,
		'comment_like_speed' => $q_post_comment_like_speed,
		'masslookingv2_speed' => $q_masslookingv2_speed,
		'is_verified' => (0 === $q_verified) ? false : true,
		'is_auto_confirm' =>(0 === $q_auto_confirm) ? false : true,
                        'is_questions_answers_active' => (0 === $q_answers) ? false : true,
                        'is_quiz_answers_active' => (0 === $q_quiz) ? false : true,
						 'is_mention_active' => (0 === $q_mention) ? false : true,
					'telegram_delay' => $q_delay_telegram,
						 'is_telegram_chat_id' => $q_telegram_chat_id ? $q_telegram_chat_id : null,
		'is_multi_language_active' => (0 === $q_language_enable) ? false : true,
		'telegram_username' => $q_telegram_username ? $q_telegram_username : null,
		 'is_telegram_active' => (0 === $q_telegram) ? false : true,
		  'is_telegram_error_active' => (0 === $q_telegram_error) ? false : true,
		   'is_telegram_stats_active' => (0 === $q_telegram_error) ? false : true,
		  'is_follower_ignore_active' => (0 === $q_follow) ? false : true,
		    'business' => (0 === $q_business) ? false : true,
		  'is_following_ignore_active' => (0 === $q_following) ? false : true,
		  'is_same_user_ignore_active' => (0 === $q_sameuser) ? false : true,
		  'is_requested_ignore_active' => (0 === $q_requested) ? false : true,
						 'is_countdown_active' => (0 === $q_countdown) ? false : true,
                        'is_mass_story_vivew_active' => (0 === $q_stories) ? false : true,
                        'questions_answers' => $qs,
						'questions_answers_ar' => $qs_ar,
		'questions_answers_en' => $qs_en,
		'questions_answers_de' => $qs_de,
		'questions_answers_fr' => $qs_fr,
		'questions_answers_tr' => $qs_tr,
		'questions_answers_id' => $qs_id,
		'questions_answers_in' => $qs_in,
		'questions_answers_ru' => $qs_ru,
		'questions_answers_jp' => $qs_jp,
		'questions_answers_cn' => $qs_cn,
		'questions_answers_it' => $qs_it,
		'questions_answers_es' => $qs_es,
		'questions_answers_pt' => $qs_pt,
		'questions_answers_ir' => $qs_ir,
                        'slider_points_range' => array(
                            ($q_slide_points_min) ? $q_slide_points_min : 0,
                            ($q_slide_points_max) ? $q_slide_points_max : 100,
                        ),
                    )
                );
                $data = json_decode($datas);
            }
        } else {
            $data = json_decode($datajson);
            $defined_targets = $data->targets;
            $login = $data->username;
            $password = $data->password;
            $proxy = $data->proxy;
			
        }
        $login_process = validate_login_process($ig, $login, $password, $climate, $proxy, false);
        $is_connected = $login_process;
        if ($is_connected) {
            $climate->infoBold('Logged as @' . $login . ' successfully.');
        }
		$auto_confirm = $data->is_auto_confirm;
		$delay=13;
        $data_targ = define_targets($ig, $login, $defined_targets, $climate, $auto_confirm);
		
        hypervote_v1($data, $data_targ, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
    } catch (\Exception $e) {
        $climate->errorBold($e->getMessage());
        sleep(1);
        $climate->errorBold('Please run script command again.');
        exit;
    }
}

function validate_login_process($ig, $login, $password, $climate, $proxy, $slient)
{
    $is_connected = false;
    $is_connected_count = 0;
    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
    do {
        if (10 == $is_connected_count) {
            if ($e->getResponse()) {
                $climate->errorBold($e->getMessage());
            }
            throw new Exception($fail_message);
        }
        try {
            if (0 == $is_connected_count) {
                if ($slient) {
                } else {
                    $climate->infoBold('Emulation of an Instagram app initiated...');
                }
            }
            $login_resp = $ig->login($login, $password);
            if (null !== $login_resp && $login_resp->isTwoFactorRequired()) {
                // Default verification method is phone
                $twofa_method = '1';
                // Detect is Authentification app verification is available
                $is_totp = json_decode(json_encode($login_resp), true);
                if ('1' == $is_totp['two_factor_info']['totp_two_factor_on']) {
                    $climate->infoBold('Two-factor authentication required, please enter the code from you Authentication app');
                    $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                    $twofa_method = '3';
                } else {
                    $climate->bold(
                        'Two-factor authentication required, please enter the code sent to your number ending in %s',
                        $login_resp->getTwoFactorInfo()->getObfuscatedPhoneNumber()
                    );
                    $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                }
                $twofa_code = getVarFromUser('Two-factor code');
                if (empty($twofa_code)) {
                    do {
                        $twofa_code = getVarFromUser('Two-factor code');
                    } while (empty($twofa_code));
                }
                $is_connected = false;
                $is_connected_count = 0;
                do {
                    if (10 == $is_connected_count) {
                        if ($e->getResponse()) {
                            $climate->errorBold($e->getMessage());
                        }
                        throw new Exception($fail_message);
                    }
                    if (0 == $is_connected_count) {
                        $climate->infoBold('Two-factor authentication in progress...');
                    }
                    try {
                        $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                        $is_connected = true;
                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                        sleep(7);
                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                        sleep(7);
                    } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                        $is_code_correct = false;
                        $is_connected = true;
                        do {
                            $climate->errorBold('Code is incorrect. Please check the syntax and try again.');
                            $twofa_code = getVarFromUser('Two-factor code');
                            if (empty($twofa_code)) {
                                do {
                                    $twofa_code = getVarFromUser('Security code');
                                } while (empty($twofa_code));
                            }
                            $is_connected = false;
                            $is_connected_count = 0;
                            do {
                                try {
                                    if (10 == $is_connected_count) {
                                        if ($e->getResponse()) {
                                            $climte->out($e->getMessage());
                                        }
                                        throw new Exception($fail_message);
                                    }
                                    if (0 == $is_connected_count) {
                                        $climate->infoBold('Verification in progress...');
                                    }
                                    $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                                    $is_code_correct = true;
                                    $is_connected = true;
                                } catch (\InstagramAPI\Exception\NetworkException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                                    $is_code_correct = false;
                                    $is_connected = true;
                                } catch (\Exception $e) {
                                    throw $e;
                                }
                                $is_connected_count += 1;
                            } while (!$is_connected);
                        } while (!$is_code_correct);
                    } catch (\Exception $e) {
                        throw $e;
                    }
                    $is_connected_count += 1;
                } while (!$is_connected);
            }
            $is_connected = true;
        } catch (\InstagramAPI\Exception\NetworkException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
            throw new Exception('Please go to Instagram website or mobile app and pass checkpoint!');
        } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
            if (!($ig instanceof InstagramAPI\Instagram)) {
                throw new Exception('Oops! Something went wrong. Please try again later! (invalid_instagram_client)');
            }
            if (!($e instanceof InstagramAPI\Exception\ChallengeRequiredException)) {
                throw new Exception('Oops! Something went wrong. Please try again later! (unexpected_exception)');
            }
            if (!$e->hasResponse() || !$e->getResponse()->isChallenge()) {
                throw new Exception('Oops! Something went wrong. Please try again later! (unexpected_exception_response)');
            }
            $challenge = $e->getResponse()->getChallenge();
            if (is_array($challenge)) {
                $api_path = $challenge['api_path'];
            } else {
                $api_path = $challenge->getApiPath();
            }
            $climate->info('Instagram want to send you a security code to verify your identity.');
            $climate->info('How do you want receive this code?');
            $climate->infoBold('1 - [Email]');
            $climate->infoBold('2 - [SMS]');
            $climate->infoBold('3 - [Exit]');
            $choice = getVarFromUser('Choice');
            if (empty($choice)) {
                do {
                    $choice = getVarFromUser('Choice');
                } while (empty($choice));
            }
            if ('1' == $choice || '2' == $choice || '3' == $choice) {
                // All fine
            } else {
                $is_choice_ok = false;
                do {
                    $climate->errorBold('Choice is incorrect. Type 1, 2 or 3.');
                    $choice = getVarFromUser('Choice');
                    if (empty($choice)) {
                        do {
                            $choice = getVarFromUser('Choice');
                        } while (empty($choice));
                    }
                    if ('1' == $confirm || '2' == $confirm || '3' == $confirm) {
                        $is_choice_ok = true;
                    }
                } while (!$is_choice_ok);
            }
            $challange_choice = 0;
            if ('3' == $choice) {
                run($ig, $climate);
            } elseif ('1' == $choice) {
                // Email
                $challange_choice = 1;
            } else {
                // SMS
                $challange_choice = 0;
            }
            $is_connected = false;
            $is_connected_count = 0;
            do {
                if (10 == $is_connected_count) {
                    if ($e->getResponse()) {
                        $climate->errorBold($e->getMessage());
                    }
                    throw new Exception($fail_message);
                }
                try {
                    $challenge_resp = $ig->sendChallangeCode($api_path, $challange_choice);
                    // Failed to send challenge code via email. Try with SMS.
                    if ('ok' != $challenge_resp->status) {
                        $challange_choice = 0;
                        sleep(7);
                        $challenge_resp = $ig->sendChallangeCode($api_path, $challange_choice);
                    }
                    $is_connected = true;
                } catch (\InstagramAPI\Exception\NetworkException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                    sleep(7);
                } catch (\Exception $e) {
                    throw $e;
                }
                $is_connected_count += 1;
            } while (!$is_connected);
            if ('ok' != $challenge_resp->status) {
                if (isset($challenge_resp->message)) {
                    if ('This field is required.' == $challenge_resp->message) {
                        $climate->info("We received the response 'This field is required.'. This can happen in 2 reasons:");
                        $climate->info('1. Instagram already sent to you verification code to your email or mobile phone number. Please enter this code.');
                        $climate->info('2. Instagram forced you to phone verification challenge. Try login to Instagram app or website and take a look at what happened.');
                    }
                } else {
                    $climate->info('Instagram Response: ' . json_encode($challenge_resp));
                    $climate->info("Couldn't send a verification code for the login challenge. Please try again later.");
                    $climate->info('- Is this account has attached mobile phone number in settings?');
                    $climate->info('- If no, this can be a reason of this problem. You should add mobile phone number in account settings.');
                    throw new Exception('- Sometimes Instagram can force you to phone verification challenge process.');
                }
            }
            if (isset($challenge_resp->step_data->contact_point)) {
                $contact_point = $challenge_resp->step_data->contact_point;
                if (2 == $choice) {
                    $climate->info('Enter the code sent to your number ending in ' . $contact_point . '.');
                } else {
                    $climate->info('Enter the 6-digit code sent to the email address ' . $contact_point . '.');
                }
            }
            $security_code = getVarFromUser('Security code');
            if (empty($security_code)) {
                do {
                    $security_code = getVarFromUser('Security code');
                } while (empty($security_code));
            }
            if ('3' == $security_code) {
                throw new Exception('Reset in progress...');
            }
            // Verification challenge
            $ig = challange($ig, $login, $password, $api_path, $security_code, $proxy, $climate);
        } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
            throw new Exception('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
        } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
            throw new Exception('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
        } catch (\InstagramAPI\Exception\SentryBlockException $e) {
            throw new Exception('Access to Instagram API restricted for spam behavior or otherwise abusing. You can try to use Session Catcher script (available by https://nextpost.tech/session-catcher) to get valid Instagram session from location, where your account created from.');
        } catch (\InstagramAPI\Exception\IncorrectPasswordException $e) {
            throw new Exception('The password you entered is incorrect. Please try again.');
        } catch (\InstagramAPI\Exception\InvalidUserException $e) {
            throw new Exception("The username you entered doesn't appear to belong to an account. Please check your username in config file and try again.");
        } catch (\Exception $e) {
            throw $e;
        }
        $is_connected_count += 1;
    } while (!$is_connected);
}
/**
 * Define targets for Hypervote
 */
function define_targets($ig, $username, $defined_targets = null, $climate, $auto_confirm = null)
{
    do {
        if (null === $defined_targets) {
            $climate->out('Please define the targets.');
            $climate->out("Write all Instagram profile usernames via comma without '@' symbol.");
            $climate->out('Example: apple, instagram, hostazor');
            $targets_input = getVarFromUser('Usernames');
            if (empty($targets_input)) {
                do {
                    $targets_input = getVarFromUser('Usernames');
                } while (empty($targets_input));
            }
        } else {
            $climate->infoBold('Targets already defined config file. Applying...');
            sleep(1);
            $targets_input = $defined_targets;
        }
        $targets_input = str_replace(' ', '', $targets_input);
        $targets = [];
        $targets = explode(',', trim($targets_input));
        $targets = array_unique($targets);
        $pks = [];
        $filtered_targets = [];
        foreach ($targets as $target) {
            $is_connected = false;
            $is_connected_count = 0;
            if ($target === $username) {
                $climate->errorBold('Please do not add yourself into targets. Your username is skipping...');
                continue;
            }
            do {
                if (10 == $is_connected_count) {
                    if ($e->getResponse()) {
                        $climate->errorBold($e->getMessage());
                    }
                    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                    $climate->errorBold($fail_message);
                    run($ig, $climate);
                }
                try {
                    $user_resp = $ig->people->getUserIdForName($target);
                    $climate->info('@' . $target . ' - [OK]');
                    $filtered_targets[] = $target;
                    $pks[] = $user_resp;
                    $is_connected = true;
                    if (($target != $targets[count($targets) - 1]) && (count($targets) > 0)) {
                        sleep(1);
                    }
                } catch (\InstagramAPI\Exception\NetworkException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                    sleep(7);
                } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                    $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                    $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                    $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                    $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                    $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                    $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing. You can try to use Session Catcher script (available by https://nextpost.tech/session-catcher) to get valid Instagram session from location, where your account created from.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\ThrottledException $e) {
                    $climate->error('Throttled by Instagram because of too many API requests.');
                    $climate->error('Please login again after 1 hour. You reached Instagram limits.');
                    run($ig, $climate);
                } catch (\InstagramAPI\Exception\NotFoundException $e) {
                    $is_connected = true;
                    $is_username_correct = false;
                    do {
                        $climate->error('Instagram profile username @' . $target . ' is incorrect or maybe user just blocked you (Login to Instagram website or mobile app and check that).');
                        $climate->error('Type 3 for skip this target.');
                        $target_new = getVarFromUser('Please provide valid username');
                        if (empty($target_new)) {
                            do {
                                $target_new = getVarFromUser('Please provide valid username');
                            } while (empty($target_new));
                        }
                        if ('3' == $target_new) {
                            break;
                        } else {
                            $is_connected = false;
                            $is_connected_count = 0;
                            do {
                                if (10 == $is_connected_count) {
                                    if ($e->getResponse()) {
                                        $climate->errorBold($e->getMessage());
                                    }
                                    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                                    $climate->errorBold($fail_message);
                                    run($ig, $climate);
                                }
                                try {
                                    $user_resp = $ig->people->getUserIdForName($target_new);
                                    $climate->info('@' . $target_new . ' - [OK]');
                                    $filtered_targets[] = $target_new;
                                    $pks[] = $user_resp;
                                    $is_username_correct = true;
                                    $is_connected = true;
                                } catch (\InstagramAPI\Exception\NetworkException $e) {
                                    sleep(7);
                                } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                    sleep(7);
                                } catch (InstagramAPI\Exception\NotFoundException $e) {
                                    $is_username_correct = false;
                                    $is_connected = true;
                                } catch (\Exception $e) {
                                    $climate->error($e->getMessage());
                                    run($ig, $climate);
                                }
                                $is_connected_count += 1;
                            } while (!$is_connected);
                        }
                    } while (!$is_username_correct);
                } catch (Exception $e) {
                    $climate->errorBold($e->getMessage());
                    run($ig, $climate);
                }
                $is_connected_count += 1;
            } while (!$is_connected);
        }
    } while (empty($filtered_targets));
    $targets = array_unique($filtered_targets);
    $pks = array_unique($pks);
    $data_targ = [];
    for ($i = 0; $i < count($targets); $i++) {
        $data_targ[$i] = [
            'username' => $targets[$i],
            'pk' => $pks[$i],
        ];
    }
    $climate->info('Selected ' . count($targets) . ' targets: @' . implode(', @', $targets) . '.');
	if (!$auto_confirm) { 
    $climate->info('Please confirm that the selected targets are correct. After 10 Sec we will confirm auto.');
    $climate->info('1 - [Yes]');
    $climate->info('2 - [No]');
    $climate->info('3 - [Exit]');
	   $confirm = getVarFromUser('Choice');
	} else {
	 $confirm = '1';
	}
	
	


	
	 
    if (empty($confirm)) {
        do {
            $confirm = getVarFromUser('Choice');
        } while (empty($confirm));
    }
    if ('1' === $confirm || '2' === $confirm || '3' === $confirm) {
        // All fine
    } else {
        $is_choice_ok = false;
        do {
            $climate->error('Choice is incorrect. Type 1, 2 or 3.');
            $confirm = getVarFromUser('Choice');
            if (empty($confirm)) {
                do {
                    $confirm = getVarFromUser('Choice');
                } while (empty($confirm));
            }
            if ('1' === $confirm || '2' === $confirm || '3' === $confirm) {
                $is_choice_ok = true;
            }
        } while (!$is_choice_ok);
    }
    if ('3' === $confirm) {
        run($ig, $climate);
    } elseif ('2' === $confirm) {
        $data_targ = define_targets($ig, $username, $defined_targets, $climate);
    } else {
        // All fine. Going to Hypervote.
    }
    return $data_targ;
}
/**
 * Get varable from user
 */
function getVarFromUser($text)
{
    echo $text . ': ';
    $var = trim(fgets(STDIN));
    return $var;
}
/**
 * Validates proxy address
 */
function isValidProxy($proxy, $climate, $slient = false)
{
    if (false === $slient) {
        $climate->info('Connecting to Instagram...');
    }
    $code = null;
    try {
        $client = new \GuzzleHttp\Client();
        $res = $client->request(
            'GET',
            'http://www.instagram.com',
            [
                'timeout' => 60,
                'proxy' => $proxy,
            ]
        );
        $code = $res->getStatusCode();
        $is_connected = true;
    } catch (\Exception $e) {
        //$climate->error( $e->getMessage() );
        $code = '500';
        //return false;
    }
    return $code;
}
/**
 * Validates proxy address
 */
function finishLogin($ig, $login, $password, $proxy, $climate)
{
    $is_connected = false;
    $is_connected_count = 0;
    try {
        do {
            if (10 == $is_connected_count) {
                if ($e->getResponse()) {
                    $climate->out($e->getMessage());
                }
                $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
                $climate->errorBold($fail_message);
                run($ig, $climate);
            }
            if ('3' == $proxy) {
                // Skip proxy setup
            } else {
                $ig->setProxy($proxy);
            }
            try {
                $login_resp = $ig->login($login, $password);
                if (null !== $login_resp && $login_resp->isTwoFactorRequired()) {
                    // Default verification method is phone
                    $twofa_method = '1';
                    // Detect is Authentification app verification is available
                    $is_totp = json_decode(json_encode($login_resp), true);
                    if ('1' == $is_totp['two_factor_info']['totp_two_factor_on']) {
                        $climate->info('Two-factor authentication required, please enter the code from you Authentication app');
                        $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                        $twofa_method = '3';
                    } else {
                        $climate->info(
                            'Two-factor authentication required, please enter the code sent to your number ending in %s',
                            $login_resp->getTwoFactorInfo()->getObfuscatedPhoneNumber()
                        );
                        $twofa_id = $login_resp->getTwoFactorInfo()->getTwoFactorIdentifier();
                    }
                    $twofa_code = getVarFromUser('Two-factor code');
                    if (empty($twofa_code)) {
                        do {
                            $twofa_code = getVarFromUser('Two-factor code');
                        } while (empty($twofa_code));
                    }
                    $is_connected = false;
                    $is_connected_count = 0;
                    do {
                        if (10 == $is_connected_count) {
                            if ($e->getResponse()) {
                                $climate->errorBold($e->getMessage());
                            }
                            $climate->errorBold($fail_message);
                            run($ig, $climate);
                        }
                        if (0 == $is_connected_count) {
                            $climate->info('Two-factor authentication in progress...');
                        }
                        try {
                            $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                            $is_connected = true;
                        } catch (\InstagramAPI\Exception\NetworkException $e) {
                            sleep(7);
                        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                            sleep(7);
                        } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                            $is_code_correct = false;
                            $is_connected = true;
                            do {
                                $cliate->errorBold('Code is incorrect. Please check the syntax and try again.');
                                $twofa_code = getVarFromUser('Two-factor code');
                                if (empty($twofa_code)) {
                                    do {
                                        $twofa_code = getVarFromUser('Security code');
                                    } while (empty($twofa_code));
                                }
                                $is_connected = false;
                                $is_connected_count = 0;
                                do {
                                    try {
                                        if (10 == $is_connected_count) {
                                            if ($e->getResponse()) {
                                                $climate->error($e->getMessage());
                                            }
                                            $climate->errorBold($fail_message);
                                            run($ig, $climate);
                                        }
                                        if (0 == $is_connected_count) {
                                            $climate->info('Verification in progress...');
                                        }
                                        $twofa_resp = $ig->finishTwoFactorLogin($login, $password, $twofa_id, $twofa_code, $twofa_method);
                                        $is_code_correct = true;
                                        $is_connected = true;
                                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                                        sleep(7);
                                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                        sleep(7);
                                    } catch (\InstagramAPI\Exception\InvalidSmsCodeException $e) {
                                        $is_code_correct = false;
                                        $is_connected = true;
                                    } catch (\Exception $e) {
                                        throw new $e();
                                    }
                                    $is_connected_count += 1;
                                } while (!$is_connected);
                            } while (!$is_code_correct);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                        $is_connected_count += 1;
                    } while (!$is_connected);
                }
                $is_connected = true;
            } catch (\InstagramAPI\Exception\NetworkException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                throw new Exception('Please go to Instagram website or mobile app and pass checkpoint!');
            } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                $climate->error('Instagram Response: ' . json_encode($e->getResponse()));
                $climate->error("Couldn't complete the verification challenge. Please try again later.");
                throw new Exception('Developer code: Challenge loop.');
            } catch (\Exception $e) {
                throw $e;
            }
            $is_connected_count += 1;
        } while (!$is_connected);
    } catch (\Exception $e) {
        $climate->errorBold($e->getMessage());
        run($ig, $climate);
    }
    return $ig;
}
/**
 * Verification challenge
 */
function challange($ig, $login, $password, $api_path, $security_code, $proxy, $climate)
{
    $is_connected = false;
    $is_connected_count = 0;
    $fail_message = "There is a problem with your Ethernet connection or Instagram is down at the moment. We couldn't establish connection with Instagram 10 times. Please try again later.";
    do {
        if (10 == $is_connected_count) {
            if ($e->getResponse()) {
                $climate->errorBold($e->getMessage());
            }
            throw new Exception($fail_message);
        }
        if (0 == $is_connected_count) {
            $climate->info('Verification in progress...');
        }
        try {
            $challenge_resp = $ig->finishChallengeLogin($login, $password, $api_path, $security_code);
            $is_connected = true;
        } catch (\InstagramAPI\Exception\NetworkException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
            sleep(7);
        } catch (\InstagramAPI\Exception\InstagramException $e) {
            $msg = $e->getMessage();
            $climate->out($msg);
            $climate->out('Type 3 - to exit.');
            $security_code = getVarFromUser('Security code');
            if (empty($security_code)) {
                do {
                    $security_code = getVarFromUser('Security code');
                } while (empty($security_code));
            }
            if ('3' == $security_code) {
                throw new Exception('Reset in progress...');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if ('Invalid Login Response at finishChallengeLogin().' == $msg) {
                sleep(7);
                $ig = finishLogin($ig, $login, $password, $proxy, $climate);
                $is_connected = true;
            } else {
                throw $e;
            }
        }
        $is_connected_count += 1;
    } while (!$is_connected);
    return $ig;
}
/**
 * Hypervote loop - Algorithm #2
 */

function hypervote_v1($data, $data_targ, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy = '3')
{

    $view_count = 0;
    $st_count = 0;
    $st_count_seen = 0;
    $begin = strtotime(date('Y-m-d H:i:s'));
    $begin_ms = strtotime(date('Y-m-d H:i:s'));
    $begin_mspoll = strtotime(date('Y-m-d H:i:s'));
    $begin_msslide = strtotime(date('Y-m-d H:i:s'));
    $begin_msquiz = strtotime(date('Y-m-d H:i:s'));
    $begin_msquestion = strtotime(date('Y-m-d H:i:s'));
    $begin_f = strtotime(date('Y-m-d H:i:s'));
    $time_from_logs = strtotime(date('Y-m-d H:i:s'));
    $speed = 0;
	$begin_zaman = time();
	$massview_sleep = false;
    $last_massview_sleep = time();
	 $poll_sleep = false;
    $last_poll_sleep = time();
	$begin_getreel = time();
    $delitel = 0;
	$s_poll_time = 0;
	$poll_time = 0;
    $counter1 = 0;
    $counter2 = 0;
    $stories = [];
	$is_comment = $data->post_comment;
	$is_comment_active = true;
	$countdown_throttled = false;
	$comment_time = null;
	$comment_loop_counter = 0;
	$comment_counter=0;
	$like_counter = 0;
	$comment_status = null;
	$comment_response = null;
	$comment_per_user = $data->post_comment_per_people;
	$comment_speed = $data->comment_speed;
		if ($comment_speed) {
            $comment_delaytime = (int)((86400*$comment_per_user)/$comment_speed);
        } else {
			$comment_delaytime = 8640;
		}
	 $last_sleep_like = time();
	$c_like_counter = 0;
	
	$last_throttled_comment = time();
    $comment_feedback_required = false;
    $comment_like_sleep = true;
	$comment_like_speed = $data->comment_like_speed;
		if ($comment_like_speed) {
            $comment_like_delaytime = (int)(86400/$comment_like_speed);
        } else { 
		$comment_like_delaytime = 8640;
		}
	$is_c_like = $data->post_comment_like;
	$is_c_like_active = true;
	$comment_like_sleep = true;
	$counterUserFeed = 0;
	$c_like_status = null;
	$comment_like_response = null;
	$comment_like_time = time();
	$c_likes_per_user = 1;
	$last_sleep_comment = time();
	$last_sleep_follow = time();
	$follow_counter = 0;
	$unfollow_sleep = true;
	$last_sleep_unfollow = time();
	$last_throttled_c_like = time();                                                                                                     $c_like_feedback_required = false;
    $last_sleep_comment = time();
	$c_like_loop_counter = 0;
	$is_like = $data->post_like;
	$is_like_active = true;
	$counterUserFeed =0;
	
	$is_like_timeline = $data->timeline_post_like;
	$like_status = null;
	$like_response = null;
	$like_loop_counter = 0;
	$likes_per_user = $data->post_like_per_people;
	$counterTimelineFeed = 0;
	$like_time = null;
	$last_throttled_like = time();
	$like_feedback_required = false;
	$likes_speed = $data->like_speed;
       $counterComments = 0;
	   $last_sleep_follow = time();
        $last_sleep_unfollow = time();
		if ($likes_speed) {
            $like_delaytime = (int)((86400*$likes_per_user)/$likes_speed);
        } else {
			$like_delaytime = 8640;
		}
		
	$is_follow = $data->follow;
	$is_follow_active = true;
	$follow_speed = $data->follow_speed;
        if ($follow_speed) {
            $follow_delaytime = (int)(86400/$follow_speed);
        } else {
			$follow_delaytime = 8640;
		}
	$begin_comment = time();
	$begin_like = time();
	$follow_time = null;
	$follow_response = null;
	$follow_status = null;
	$is_unfollow = $data->unfollow;
	$is_unfollow_active = true;
	$unfollow_status = null;
	$unfollow_response = null;
	$unfollow_counter = null;
	$last_throttled_unfollow = time();
    $unfollow_feedback_required = false;
	$unfollow_speed = $data->unfollow_speed;
	 $unfollow_interval = $data->unfollow_interval;
        if ($unfollow_speed) {
            $unfollow_delaytime = (int)(86400/$unfollow_speed);
        } else {
			$unfollow_delaytime = 8640;
		}
	$comment_sleep2 = true;
    $last_sleep_comment2 = time();
	 $behaviour_time = time();
    $mycount = 0;
	$mt_count = 0;
	$seenstory = 0;
	$bio = [];
	$GUSF = true;
	$GURMF = false;
	$GRMF = false;
	
	$mention_temp=false;
    $last_temp_mention = time();
	$last_throttled_mass_views = time();
	$last_telegram_report = time();
    $poll_votes_count = 0;
	$last_throttled_mention = time();
	$mention_throttled = false;
	$mention_count = 0;
	$slider_sleep = false;
    $last_slider_sleep = time();
    $slider_points_count = 0;
    $question_answers_count = 0;
    $quiz_answers_count = 0;
    $story_vivews_count = 0;
	$count_votes_count = 0;
    $follow_c_count = 0;
    $poll_throttled = false;
    $quiz_throttled = false;
    $slider_throttled = false;
    $fresh_stories_range = 0;
    $fresh_stories = 0;
    $next_page = null;
	$mass_view_v2 = true;
    $end_cursor = null;
    $question_throttled = false;
    $total_analyzed_users_count = 0;
	$last_telegram_report = time();
    $countdown_throttled = false;
    $mass_view_throttled = false;
    $last_throttled_poll = time();
    $last_throttled_quiz = time();
    $last_throttled_slider = time();
    $last_throttled_question = time();
    $last_throttled_countdown = time();
    $last_throttled_mass_view = time();
    $last_throttled_follow = time();
    $last_throttled_mass_view_v2 =  time();
	$mass_act = false;
	$numberX = 0;
	$ld = new Language;
	$disabled_view = time();
	$activated_view = time();
	$telegram_error = $data->is_telegram_error_active;
	$telegram_stats = $data->is_telegram_stats_active;
	$step1 = true;
	$profile_username = [];
	$EmptyResponseCatcher = 0;
	$like_sleep = true;
	$follow_sleep = true;
	
    $begin_login = time();
	$data2 = [];
	$path = __DIR__ . '/vendor/mgp25/instagram-php/sessions/' .  $ig->account_id . '/';
	if (!file_exists($path)) {
            mkdir($path);
        }
	
	 $filename_followed = "followed-ids-" . $ig->account_id . ".json";
        $data2 = [];
        if (file_exists($path.$filename_followed)) {
            $followed_string = file_get_contents($path.$filename_followed);
            $followed = json_decode($followed_string, true);
            $followed_string = null;
            unset($followed_string);
            if (isset($followed["followed"]) && is_array($followed["followed"])) {
                $data2 = $followed["followed"];
                $followed = null;
                unset($followed);
            } else {
                // Empty file or non-array
                file_put_contents($path.$filename_followed, "");
            }
        } else {
            // Skip recovery
            file_put_contents($path.$filename_followed, "");
        }
	
       
    $usfile                  = '/vendor/mgp25/instagram-php/sessions/' . $ig->account_id.'-seed-stroies.txt';
	  
	  $mefile                  = '/vendor/mgp25/instagram-php/sessions/' . $ig->account_id.'-mention-peoples.txt';

		
    $climate->infoBold('Hypervote loop started.');
    $targets = [];
    $targets = $data_targ;
    shuffle($data_targ);
    for ($i = 0; $i < count($data_targ); $i++) {
        $data_targ[$i] += [
            'rank_token' => \InstagramAPI\Signatures::generateUUID(),
            'users_count' => 0,
            'max_id' => null,
            'begin_gf' => null,
        ];
    }
	
    do {
        foreach ($data_targ as $key => $d) {
            try {
                if (null == $d['max_id']) {
                    $is_gf_first = 1;
                }
                if (!empty($d['begin_gf'])) {
                    $current_time = strtotime(date('Y-m-d H:i:s'));
                    if (($current_time - $d['begin_gf']) < 7) {
                        $sleep_time = 2 - ($current_time - $d['begin_gf']) + mt_rand(1, 3);
                    }
                }
                /*try {
                $data_targ[ $key ]['begin_gf'] = strtotime( date( 'Y-m-d H:i:s' ) );
                $followers = $ig->people->getFollowers( $d['pk'], $d['rank_token'], null, $d['max_id'] );
                sleep(3);
                } catch ( \InstagramAPI\Exception\NotFoundException $e ) {
                $climate->error( '@' . $d['username'] . ' not found or maybe user blocked you (login to Instagram website or mobile app and check that).' );
                unset( $data_targ[ $key ] );
                continue;
                } catch ( Exception $e ) {
                throw $e;
                }*/
                // DEBUG - BEGIN
                //$followers_json = json_decode($followers);
                // $climate->out(var_dump($followers_json));
                // exit;
                // DEBUG - END
                /*if ( empty( $followers->getUsers() ) ) {
                $climate->error( '@' . $d['username'] . " don't have any follower." );
                unset( $data_targ[ $key ] );
                continue;
                }*/
                /*$follos = json_decode( $followers, true );*/
                //$climate->out(print_r($follos['users'],true));
                /*$data_targ[ $key ]['max_id'] = $follos['next_max_id'];*/
                /*$followers_ids = [];*/
                /*foreach ( $followers->getUsers() as $follower ) {
                $is_private                    = $follower->getIsPrivate();
                $has_anonymous_profile_picture = $follower->getHasAnonymousProfilePicture();
                $is_verified                   = $follower->getIsVerified();
                // Check is user have stories at scrapping
                $latest_reel_media = $follower->getLatestReelMedia();
                if ( ! ( $is_private ) && ! ( $has_anonymous_profile_picture ) && ! ( $is_verified ) && ( null !== $latest_reel_media ) ) {
                // Mark as seen only fresh stories, which posted no more than X hours ago
                if ( isset( $fresh_stories_range ) ) {
                $fresh_stories_min = time() - round( $fresh_stories_range * 60 * 60 );
                if ( $latest_reel_media >= strtotime( $fresh_stories_min ) ) {
                $followers_ids[] = $follower->getPk();
                }
                } else {
                $followers_ids[] = $follower->getPk();
                }
                }
                }*/
                $likers_list = null;
                $filter_c = $data->filter;

                // Debug
                //$get_from_follow=true;
                // Debug

                // while ($next_max_id || $first_loop) {
                if (null !== $d['max_id']) {
                    //$climate->out("Pagination successfuly! @".$d['username']);
                } else {
                    //$climate->out("First page of @".$d['username']);
                }
                if (0 === $filter_c || 1 === $filter_c || 2 === $filter_c && $step1) {
                    try {
						
                        $mediaFeed = $ig->timeline->getUserFeed($d['pk'], $d['max_id']);
						} catch (\InstagramAPI\Exception\ThrottledException $e) {
                        $get_from_follow=true;
                        sleep(1);
						  } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 3;
                        continue;
						$EmptyResponseCatcher++;
						if ($EmptyResponseCatcher >= 1) {
							$climate->error('Hypervoter Restarting.');
                        hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
						}
                     } catch (\InstagramAPI\Exception\NotFoundException $e) {
						 $climate->error($d['username']. "has no data in his/her profile. Skipping...");
                            continue; 
					 }
					 
                         $items = null;
						 if (!$mediaFeed->getItems()) {
							 continue;
						 }
                        $items = $mediaFeed->getItems();
                        if ($items == null) {
                            $climate->error($d['username']. "Cant fetch post. Sleeping 1 min..");
                            sleep(2*3);
                            continue;
                        }
                        if (empty($items)) {
                            $climate->error($d['username']. "has no data in his/her profile. Skipping...");
                            continue;
                        }
                        sleep(2);
                    
                    //$climate->out(print_r($mediaFeed->getNextMaxId()));
                    $data_targ[$key]['max_id'] = $mediaFeed->getNextMaxId();
					
                    //$climate->out("Next_max_id: " . $data[$key]['max_id']);
                    $counsa = 100 % count($items);
                    $vprocess = $climate->progress()->total(($counsa * count($items)));
                    foreach ($items as $item) {
                        try {
							$mediaId = null;
                            $mediaId = $item->getId();
                            $vprocess->advance(floor($counsa), 'Collecting data from @'.$d['username']);
							 $likers_list = [];
                            $likers_list[] = $ig->media->getLikers($mediaId);
                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                            $get_from_follow=true;
                            sleep(1);
							  } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 30;
                        continue;
						$EmptyResponseCatcher++;
						if ($EmptyResponseCatcher >= 1) {
							$climate->error('Hypervoter Restarting.');
                        hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
						}
                         } catch (\InstagramAPI\Exception\NotFoundException $e) {
						 $climate->error($d['username']. "has no data in his/her profile. Skipping...");
                            continue; 
					 }
                    }
                }
                if (1 === $filter_c || 2 === $filter_c && !$step1) {
                    try {

                        $data_targ[ $key ]['begin_gf'] = strtotime(date('Y-m-d H:i:s'));
						 unset($followers);
						  $followers=null;
                        //$followers = $ig->people->getFollowers($d['pk'], $d['rank_token'], null, $d['max_id']);
                        $followers = $ig->people->getFollowerWeb($d['pk'], 50, $d['max_id']);
						
						  } catch (\InstagramAPI\Exception\ThrottledException $e) {
                        $get_from_follow=false;
                    } catch (\InstagramAPI\Exception\NotFoundException $e) {
                        $climate->error('@' . $d['username'] . ' not found or maybe user blocked you (login to Instagram website or mobile app and check that).');
                        unset($data_targ[ $key ]);
                        continue;
                    } catch (Exception $e) {
                        throw $e;
                    }
					if (empty($followers)) {
						continue;
					}
						$fresp=null;
						if (empty(json_decode($followers->getUserIDGraphData()))) {
							continue;
						}
                        $fresp = json_decode($followers->getUserIDGraphData());
					
                        if($fresp->edge_followed_by->page_info->has_next_page){
                            $next_page = 50;
                            $data_targ[$key]['max_id'] = $fresp->edge_followed_by->page_info->end_cursor;
                        }
                        //$climate->json($fresp->edge_followed_by->edges);
                        //$climate->error($fresp->edge_followed_by->page_info->end_cursor);


                        
                        /*$climate->error('------ Starting Over ------');
                        $climate->error('---------------------------');
                        sleep(5);*/
                  

                    // DEBUG - BEGIN
                    //$followers_json = json_decode($followers);
                 //  $climate->out(var_dump($fresp));
                    // exit;
                    // DEBUG - END
                    if (empty($fresp->edge_followed_by->edges)) {
                        $climate->error('@' . $d['username'] . " don't have any follower. 2");
                        unset($data_targ[ $key ]);
                        continue;
                    }
                }
				 if (file_exists(__DIR__ .'/'.$usfile)) {
                        if (filesize(__DIR__ .'/'.$usfile) > 0) {
                            $q_sids = file_get_contents(__DIR__ .'/'.$usfile);
                            $qisds_ = explode(',', $q_sids);
                        } else {
                            $qisds_ = null;
                        }
                    } else {
                        $userfile = fopen(__DIR__ .'/'.$usfile, 'w');
                        fwrite($userfile, '');
                        fclose($userfile);
                        $qisds_ = null;
                    }

                //$climate->out(print_r($likers_list,true));
				unset($followers_ids);
				$followers_ids = null;
                $followers_ids = [];
                if (0 === $filter_c || 2 === $filter_c && !empty($likers_list)) {
                    foreach ($likers_list as $likers) {
                        //$climate->out($likers->getUsers());
                        foreach ($likers->getUsers() as $follower) {
                            /*foreach ($followers as $follower) {*/
                            $has_anonymous_profile_picture = $follower->getHasAnonymousProfilePicture();
							$is_business = $data->business;
							if ($is_business) {
							$business   = $follower->getIsBusiness();
							} else {
                             $business = false;
							}		
									
									
									
								
                            $is_private        = $follower->getIsPrivate();
                            $is_verified       = $follower->getIsVerified();
                             $latest_reel_media = 0;
                            if (!($is_private) && !($is_verified) && !($has_anonymous_profile_picture) && !($business)) {

                                if (!empty($fresh_stories_range)) {
                                    $fresh_stories_min = time() - round($fresh_stories_range*60);
                                    if ($latest_reel_media >= $fresh_stories_min) {
                                        $followers_ids[] = $follower->getPk();
										$step1 = false;
                                    }
                                } else {
                                    // Check is latest
                                    $followers_ids[] = $follower->getPk();
									$step1 = false;
                                }
                            }
                            /*}*/
                        }
                    }
                } elseif (4 === $filter_c || 5 === $filter_c && !empty($fresp)) {
                    $followers_ids = null;
                    unset($followers_ids);
                    $followers_ids = [];
					unset($follos);
					$follos = null;
                    $follos = $fresp->edge_followed_by;
					//$climate->out(var_dump($follos));
                    $data_targ[ $key ]['max_id'] = $fresp->edge_followed_by->page_info->end_cursor;
					//	$climate->out(var_dump($data_targ[ $key ]['max_id']));
                    $followers_ids = [];
                   // $counsa = 100 % count($follos->edges);
				    
                   // $vprocess = $climate->progress()->total(($counsa * count($follos->edges)));
                     foreach ($follos->edges as $follower) {
                      // $climate->error($follower->node->id);
                        $is_private                    = $follower->node->is_private;
                        $latestReelMedia               = $follower->node->reel->latest_reel_media;
						if($data->is_following_ignore_active) {
                        $followed                      = $follower->node->followed_by_viewer;
						} else {
							$followed  = false;
						}
						if($data->is_requested_ignore_active) {
                        $follow_request                = $follower->node->requested_by_viewer;
						} else {
							$follow_request  = false;
						}
						
                        $is_verified                   = $follower->node->is_verified;
						
                        // Check is user have stories at scrapping
                      
                        if (!($is_private) && !($is_verified)) {
                           // $vprocess->advance(floor($counsa), 'Collecting data from @'.$d['username']);
                            // Mark as seen only fresh stories, which posted no more than X hours ago
                            if (isset($fresh_stories_range)) {
                            
                               
                                    $followers_ids[] = $follower->node->id;
									
								
                            } else {
                                $followers_ids[] = $follower->node->id;
									
								if ($data->is_mention_active) {
									$samepeople         = $qisds_;
						 $uniqfollower = $follower->node->id;
						 if (in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
								  $profile_username[] = '@' . $follower->node->owner->username . '';
								   $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $follower->node->id;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));
								}
								$step1 = true;
                            }
                        }
                    }

                   // $climate->json($followers_ids);
                }
			
				
			 
			 
		 if($data->is_follower_ignore_active) {
		   if ($followers_ids) {
                            $followersIds = array_unique($followers_ids);
                            $friendships = json_decode($ig->people->getFriendships($followers_ids));

                            foreach ($friendships->friendship_statuses as $friendshipKey => $friendship) {
                                if ($friendship->following === false) {
                                    $users_ids[] = $friendshipKey;
                                }
                            }

                            if ($users_ids) {
                                $followers_ids = $users_ids;
                            }
                        }
		 }
                  	
                if (count($followers_ids) === 0) {
                    $climate->error('@' . $d['username'] . " don't have any follower. 3");
                    unset($data_targ[ $key ]);
                    continue;
                }
                // Re-indexing array
                $followers_ids = array_values($followers_ids);
                $data_targ[$key]['users_count'] = $d['users_count'] + count($followers_ids);
                $number = count($followers_ids);
				
				$total_analyzed_users_count = $total_analyzed_users_count + $number;
                if ($is_gf_first) {
                    $climate->info($number . ' followers of @' . $d['username'] . ' collected.');

                    $is_gf_first = 0;
                } else {
                    $climate->info("Next " . $number . " followers with valid stories of @" . $d['username'] . " collected. Total: " . number_format($data_targ[$key]['users_count'], 0, '.', ' ') . " followers of @" . $d['username'] . " parsed.");
                }
                $index_new = 0;
                $index_old = 0;
				 if (1 === $filter_c) {
                      $fresp = json_decode($followers->getUserIDGraphData());
                        if($fresp->edge_followed_by->page_info->has_next_page){
                            $next_page = 50;
                           $next_max_id = $fresp->edge_followed_by->page_info->end_cursor;
                        } else {
                            $next_max_id = null;
                        }
                    } elseif (0 === $filter_c) {
						if ($mediaFeed->getNextMaxId()) {
						$next_max_id = $mediaFeed->getNextMaxId();
					} 
					}					elseif (2 === $filter_c) {
                        if ($mediaFeed->getNextMaxId()) {
                            $next_max_id = $mediaFeed->getNextMaxId();
                        } else {
                            $next_max_id = null;
                        }
                    } else { 
					$next_max_id = null;
					}
                do {
                    $index_new += 20;
                    if (!isset($followers_ids[$index_new])) {
                        do {
                            $index_new -= 20;
                        } while (!isset($followers_ids[$index_new]));
                    }
                    if ($index_new < $index_old) {
                        break;
                    }
                    $ids = [];
                  
                    
                    for ($i = $index_old; $i <= $index_new; $i++) {
                        if (isset($followers_ids[$i])) {
                           
                                $ids[] = $followers_ids[$i];
                           
                        }
                    }
                 
                    
                   


				
                    try {
                        try {
							$stories_reels = null;
                            $stories_reels = $ig->story->getReelsMediaFeedWeb($ids);
							 $now_ms = time();
                                         if ($now_ms - $begin_getreel >= 7) {
                                                                // All fine
                                                            } else {
                                                                $sleep =  (7 - ($now_ms - $begin_getreel));
                                                                sleep($sleep);
                                                            } $begin_getreel = time();
                          
                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                            $climate->darkGray('getReelsMediaFeedWeb Throttled! Resting during 15 minutes before try again.');
                            sleep(15*60);
                        } catch (\InstagramAPI\Exception\BadRequestException $e) {
                            // Invalid reel id list.
                            // That's mean that this users don't have stories.
							  } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 30;
                        continue;
						$EmptyResponseCatcher++;
						if ($EmptyResponseCatcher >= 1) {
							$climate->error('Hypervoter Restarting.');
                        hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
						}
                        } catch (Exception $e) {
                            throw $e;
                        }
                        $counter1 += 1;
                        if (!json_decode($stories_reels)) {
                            $climate->darkGray('Stories_Reels() is empty. Skipping...');
                            continue;
                        }
						$reels = null;
                        $reels = json_decode($stories_reels);
                        if (null === $reels) {
                            $climate->darkGray('Getreel() is empty. Skipping...');
                            continue;
                        }
                        $reels = $reels->data->reels_media;
                         if (null === $reels) {
                            $climate->darkGray('Getreel() is empty. Skipping...');
                            continue;
                        }

                        foreach ($reels as $r) {
                            // $items = null;
                            // unset($items);
                            // $items = [];
                            // $items = $r->getItems();

                            // $stories_loop      = [];

                            $items        = [];
                           
                             if (!$r->items) {
                                                    // Item is not valid
                                                    continue;
                                                }  
                               if ($r->seen) {
                                   continue;
							   }								   
                            $items = $r->items;
                             if (null === $items) {
                            $climate->darkGray('Getreel() is empty. Skipping...');
                            continue;
                        }
						if ($data->is_mention_active) {
							 foreach ($items as $item) { 
									$samepeople         = $qisds_;
						 $uniqfollower = $item->owner->id;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
				
								  $profile_username[] = '@' . $item->owner->username . '';
								 
						   $profile_username = array_unique( $profile_username);
								   $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->owner->id;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));
				 }
								}
						
				         	
						  
						
										
											
						
							
															
                            $votableStoryUsers = false;
						
                            /**
                             *
                            
                             * WE COLLECT VOTABLE STORIES HERE
                             *
                             *
                             */
							 

                            foreach ($items as $item) {
                                if (count($item->tappable_objects) > 0) {
                                    foreach ($item->tappable_objects as $object) {
                                        if ($object->__typename === 'GraphTappableFallback'  || $object->__typename === 'GraphTappableStoryPoll' || $object->__typename === 'GraphTappableStorySlider') {
                                            $votableStoryUsers[] = $item->owner->id;
                                        }
                                    }
                                }

                                // Find the last story of the user - start
                            }
							$masslookingv2 = $data->masslookingv2 ? $data->masslookingv2 : false;
					 $masslookingv2_verified = $data->is_verified ? $data->is_verified : false;
					$mlv2_speed =  $data->masslookingv2_speed ? $data->masslookingv2_speed : 5000;
							 if (!$masslookingv2_verified) {
								 
                    
									 $masslookingv2_de = round(60*60*24*200/$mlv2_speed);
								 } else {
									 $masslookingv2_de = 15;
								 }
								 
					                 if (time() - $last_throttled_mass_view_v2 >= $masslookingv2_de) {
                                    $mass_view_v2 = true;
                                    $last_throttled_mass_view_v2 = time();
                                }
                 if ($masslookingv2 && $mass_view_v2)  {
					  
                  



					
    
    
    
                                    foreach ($items as $item) {
                                       
                                        $stories_loop = [];
                                     
                                        $stories_loop[] = [
										'media_id' => $item->id,
										'user_pk' =>  $item->owner->id,
                                        'seen_at' =>  time(),
                                        'taken_at' =>  $item->taken_at_timestamp
									];	
									  
    
                                        // Find the last story of the user - end
    
                                        if (count($stories_loop) > 0) {
                                            if (empty($stories)) {
                                                $stories = $stories_loop;
                                            } else {
                                                $stories = array_merge($stories, $stories_loop);
                                            }
        
                                            $st_count =  $st_count + count($stories_loop);
                                            $view_count = $view_count + count($stories_loop);
        
                                         
        
                                            $now_f = time();
                                            if ($now_f - $begin_f > 1) {
                                                $begin_f = time();
                                                // Debug
                                                // output($st_count . " stories found. / Debug: getReelsMediaFeed (" . $counter1 . "), markMediaSeen (" . $counter2 . ")");
                                                $climate->info($st_count . " stories found.");
                                            }
        
                                            if ($st_count >= 200) {
                                                // Debug
                                                // output($st_count . " stories found. / Debug: getReelsMediaFeed (" . $counter1 . "), markMediaSeen (" . $counter2 . ")");
                                                $climate->info($st_count . " stories found.");
        
                                                $now_ms = time();
                                                 if ($now_ms - $begin_ms >= 2) {
                                                    // all fine
                                                } else {
                                                    $counter3 = 2 - ($now_ms - $begin_ms) + rand(1, 3);
                                                    $climate->darkGray('Starting ' . $counter3 . ' second(s) delay for bypassing Instagram limits.');
                                                    $vProgress = $climate->progress()->total($counter3);
                                                    do {
                                                        $vProgress->advance(1, $counter3. 'second(s) left');
                                                        sleep(1);
                                                        $counter3 -= 1;
                                                    } while (0 != $counter3);
                                                }
        
                                                // Check that stories not expired
                                            
    
    
                                                // Mark media seen sections
                                                $is_connected = false;
                                                $is_connected_count = 0;
                                                $fail_message =  "We couldn't establish connection with Instagram 7 times. Please try again later.";
                                                   
                                                do {
                                                    if ($is_connected_count == 7) {
                                                        if ($e) {
                                                            $climate->info("RESPONSE: " . $e->getMessage());
                                                        }
                                                        throw new Exception($fail_message);
                                                    }
        
                                                    // Mark collected stories as seen
                                                    // Connection break adaptation for mobile proxies
                                                    try {
														 $batchFstory = $ig->internal->batchFetch('story_seen');
                                                        $mark_seen_resp = $ig->story->markMediaSeen($stories);
														
                                                        $is_connected = true;
														$mycount += 200;
														$mass_view_v2 = false;
                                                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                                                        sleep(3);
                                                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                                                        sleep(3);
                                                    } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                        throw $e;
													  } catch (\InstagramAPI\Exception\ThrottledException $e) {
														  $mass_view_v2 = false;
														  
                                                    } catch (Exception $e){
                                                        throw $e;
                                                    }
    
                                                    $is_connected_count += 1;
                                                } while (!$is_connected); 
    
                                                $begin_ms = time();
                                                
                                                $st_count_seen = number_format($st_count, 0, '.', ' ');
                                                $counter2 += 1;
                                                
                                                $climate->info($st_count_seen . " stories marked as seen.");
        
                                                $climate->info("");
                                                $climate->info("Total: " . number_format($view_count, 0, '.', ' ') . " stories successfully seen.");
                                                $climate->info("© Hypervoter Pro Terminal. Developed by Hypervoter Team (https://hypervoter.com)");
                                                $climate->info("");
                                                $climate->info("By default we skip private accounts, accounts with anonymous profile picture or verified accounts.");
                                                $climate->info("");
                            
                                                // Initialize arrays and parameters again
                                                $stories = [];
                                                $st_count = 0;
                                            }
                                        }
                                    }
				
				}
                            /**
                            *
                            *
                            * UNTIL HERE
                            *
                            *
                            */

                            if ($votableStoryUsers) {
                                foreach ($votableStoryUsers as $userId) {
									 $randomtimebeh = rand(1800,3600);
                                                        if (isActionCanBePerformed($randomtimebeh, $behaviour_time)) {
                                                            $behaviour_time = time();
                                                            behaviourEmulation($ig, $climate, $login, $userId);
                                                        }
                                   
									if ($GUSF === true) {
										try {
										$stories_reels = null;
										        
                                              
                                        $stories_reels = $ig->story->getUserStoryFeed($userId);
										} catch (\InstagramAPI\Exception\ThrottledException $e) {
											$GUSF = false;
											$GURMF === true;
										} catch (\InstagramAPI\Exception\BadRequestException $e) {
										 
                                        // Invalid reel id list or bad response
                                        throw $e;
										}
									} elseif ($GURMF === true && $GUSF === false) {
										try {
										$stories_reels = null;
										        
                                              
                                        $stories_reels = $ig->story->getUserReelMediaFeed ($userId);
										} catch (\InstagramAPI\Exception\ThrottledException $e) {
											$GURMF = false;
											
										} catch (\InstagramAPI\Exception\BadRequestException $e) {
										 
                                        // Invalid reel id list or bad response
                                        throw $e;
										}
									} elseif ($GRMF === true && $GURMF === false && $GUSF === false) {
										try {
										$stories_reels = null;
										        
                                              
                                        $stories_reels = $ig->story->getReelsMediaFeed ($userId);
										} catch (\InstagramAPI\Exception\ThrottledException $e) {
											$GRMF = false;
										} catch (\InstagramAPI\Exception\BadRequestException $e) {
										 
                                        // Invalid reel id list or bad response
                                        throw $e;
										}
									}
                                      $now_ms = time();
                                         if ($now_ms - $begin_zaman >= 5) {
                                                                // All fine
                                                            } else {
                                                                $sleep =  (5 - ($now_ms - $begin_zaman));
                                                                sleep($sleep);
                                                            } $begin_zaman = time();
                                    
									if ($GUSF === true) {
                                    if (is_null($stories_reels)) {
                                        unset($stories_reels);
                                        $votableStoryUsers = false;
                                        break;
                                    }
                                    if (!json_decode($stories_reels)) {
                                        $climate->darkGray('Stories_Reels() is empty. Skipping...');
                                        continue;
                                    }
									$itemsJson = null;
											
                                            $itemsJson = json_decode($stories_reels);
										   if (!isset($itemsJson)) {
											   continue;
										   }
                                            // //error_log($Account->get('id')  .  'json decoded');
                                            //$items     = $stories_reels->getReel()->getItems();
											
                                            
                                            // //error_log($Account->get('id')  .  'Reel founded.');
                                            $items = null;
											if (!$itemsJson->reel)
											{
												continue;
											}
											if (!$itemsJson->reel->items)
											{
												continue;
											}
											
											$items = null;
											unset($items);
											if (!$itemsJson->reel->items)
											{
												continue;
											}
                                            $items    = $itemsJson->reel->items;
									 if (null === $items) {
                                        $climate->darkGray('Getreel() is empty. Skipping...');
                                        continue;
                                    }
									 $stories_loop[] = null;
									} elseif ( $GURMF === true && $GUSF === false) {
										 if (is_null($stories_reels)) {
                                        unset($stories_reels);
                                        $votableStoryUsers = false;
                                        break;
                                    }
                                    if (!json_decode($stories_reels)) {
                                        $climate->darkGray('Stories_Reels() is empty. Skipping...');
                                        continue;
                                    }
									  $itemsJson = json_decode($stories_reels);
										   if (!isset($itemsJson)) {
											   continue;
										   }
									 if (!$itemsJson->items)
											{
												continue;
											}
											
											$items = null;
											unset($items);
											if (!$itemsJson->items)
											{
												continue;
											}
                                            $items    = $itemsJson->items;
									} elseif ($GRMF === true && $GURMF === false && $GUSF === false) {
									  if (is_null($stories_reels)) {
                                        unset($stories_reels);
                                        $votableStoryUsers = false;
                                        break;
                                    }
                                    if (!json_decode($stories_reels)) {
                                        $climate->darkGray('Stories_Reels() is empty. Skipping...');
                                        continue;
                                    }
									$itemsJson = null;
											
                                            $itemsJson = json_decode($stories_reels);
										   if (!isset($itemsJson)) {
											   continue;
										   }
                                            // //error_log($Account->get('id')  .  'json decoded');
                                            //$items     = $stories_reels->getReel()->getItems();
											
                                            
                                            // //error_log($Account->get('id')  .  'Reel founded.');
                                            $items = null;
											if (!$itemsJson->reels)
											{
												continue;
											}
											if (!$itemsJson->reels)
											{
												continue;
											}
											
											$items = null;
											$reels = $itemsJson->reels;
											unset($items);
											
											foreach ($reels as $r ) {
												
                                            $items    = $r->items;
											}
									 if (null === $items) {
                                        $climate->darkGray('Getreel() is empty. Skipping...');
                                        continue;
                                    }
									 $stories_loop[] = null;
									} elseif ($GRMF === false && $GURMF === false && $GUSF === false) {
										$climate->darkGray('Story parse function got limitation, we will sleep 12 hours..');
										sleep(43200);
										}
									
 

                                    foreach ($items as $item) {
                                        $mentionwindows = rand(3610, 3625);
                        if (time() - $last_temp_mention >= $mentionwindows) {
								
                           $mention_temp=false;
                            $last_temp_mention = time();
                        }
						
						 
                              if (!$mention_throttled && !$mention_temp && $data->is_mention_active) {
							
                                        if (empty($usernamesX)) {
							   if ($profile_username == null && !$profile_username) {
								   continue;
							   }
							
                                                $usernamesX = array_unique($profile_username);
                                            } else {
												 if ($profile_username == null && !$profile_username) {
								   continue;
							   }
												$profile_username = array_unique($profile_username);
                                                $usernamesX  = array_merge($usernamesX, $profile_username);
                                            } 
										
										 
										 
										
												
                        if ($profile_username == null && !$profile_username) {
								   continue;
							   }
                                           
                                           $numberX = $numberX + count($profile_username);
										     
											
										    
										
										
										if ( $numberX >= 9) {
											$bio2 = 'Hey! ' . $usernamesX[0] . ' ' . $usernamesX[1] . '  ' . $usernamesX[2] . ' ' . $usernamesX[3] . ' ' . $usernamesX[4] . ' ' . $usernamesX[5] . '  ' . $usernamesX[6] . '  ' . $usernamesX[7] . ' ' . $usernamesX[8] . ' ';
											
											
                                            try {
												$resp = null;
                                                $resp = $ig->account->setBiography($bio2);
												
												} catch (\InstagramAPI\Exception\InstagramException $e) {
													$numberX = 0;
													  $usernamesX = [];
													  $profile_username = [];
													  $bio2 = null;
													  continue;
												} catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                $climate->magenta("Bypassing action limits...");
                                               

                                                $mention_throttled = true;
                                                } catch (\Exception $e) {
													$numberX = 0;
													  $usernamesX = [];
													  $profile_username = [];
													  $bio2 = null;
													  continue;
												}
												$response = json_decode($resp);
												if ($response->status == "ok") {
												
												$bio2 = null;
											    $mention_temp=true;
												$usernamesX = [];
												$numberX = 0;
												  $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
												$qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));
                                                
												
											 
											 $profile_username = [];
												 $mycount+= 9;
												 $mention_count += 9;
												 $numberX = 0;
												  $climate->magenta(date('H:i:s') .  ' - 9 User Mentioned  Total Mention: ' . $mention_count . ' Total Actions : ' . $mycount);
												  $numberX=0;
												} else {
													 $climate->magenta(date('H:i:s') .  ' Problem Detected ' . $response->status);
													  $numberX = 0;
													  $usernamesX = [];
													  $profile_username = [];
													  $bio2 = null;
													  continue;
												}

                                               
												
                                          
                                            
                                           
                                          
											$numberX = 0;
												}
									}
                                       
								 									
							  
						 $throttled_windows = rand(1000, 3600);
                        
										 
                                        if (isset($item->story_polls) && !isset($item->story_polls[0]->poll_sticker->viewer_vote) && $item->can_reply && !$poll_throttled ) {
                                            if (!$data->is_poll_vote_active && !isActionCanBePerformed(60, $poll_time)) {
                                                continue;
                                            }

                                            $poll_id  = $item->story_polls[0]->poll_sticker->poll_id;
                                            $media_id = $item->id;
                                            $option1  = $item->story_polls[0]->poll_sticker->tallies[0]->count;
                                            $option2  = $item->story_polls[0]->poll_sticker->tallies[1]->count;
                                            $vote     = $option1 > $option2 ? 0 : 1;
											if($data->is_same_user_ignore_active) {
						$samepeople         = $qisds_;
						 $uniqfollower = $item->user->pk;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
						}

                                            try {
												
                                                $resp = $ig->story->votePollStory($media_id, $poll_id, $vote);
                                                $response = json_decode($resp);
												

                                               

                                                if ($response->status == 'ok') {
                                                    $poll_votes_count++;
													$poll_sleep = true;
                                                    $mycount++;
													$poll_time = time();
													 $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));

                                                    $climate->magenta(date('H:i:s') .  ' - Poll Voted : ' . $vote . ' Votes Given: ' . $poll_votes_count . ' Total Actions : ' . $mycount);
													$now_ms = time();
                                         if ($now_ms - $begin_zaman >= 5) {
                                                                // All fine
                                                            } else {
                                                                $sleep =  (5 - ($now_ms - $begin_zaman));
                                                                sleep($sleep);
                                                            } $begin_zaman = time();
                                                } else {
                                                    $climate->magenta(date('H:i:s') .' - Fail to vote poll \n');
                                                }
                                            } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                                sleep(2);
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                $climate->magenta("Bypassing action limits...");
                                              

                                                $poll_throttled = true;
                                            } catch (\Exception $e) {

                                                            //throw $e;
                                                continue;
                                            }
                                        }
										
										//countdown
										if (isset($item->story_countdowns) && $item->can_reply && isset($item->story_countdowns[0]->countdown_sticker->following_enabled)) {
                                            if (!$data->is_countdown_active && !$countdown_throttled) {
                                                continue;
                                            }

                                            $countdown_id  = $item->story_countdowns[0]->countdown_sticker->countdown_id;
                                            $realfilenames = $ig->account_id.'-cafile.txt';
                                            if (file_exists(__DIR__ .'/'.$realfilenames)) {
                                                if (filesize(__DIR__ .'/'.$realfilenames) > 0) {
                                                    $c_nts = file_get_contents(__DIR__ .'/'.$realfilenames);
                                                     $cntss = explode(',', $c_nts);
                                                } else {
                                                    $cntss = null;
                                                }
                                            } else {
                                                $countdown_answers_file = fopen(__DIR__ .'/'.$realfilenames, 'w');
                                                fwrite($countdown_answers_file, '');
                                                fclose($countdown_answers_file);
                                                $qids_ = null;
                                            }

                                            if (!empty($cntss) && in_array($countdown_id,  $cntss)) {
                                                continue;
                                            }
											if($data->is_same_user_ignore_active) {
						$samepeople         = $qisds_;
						 $uniqfollower = $item->user->pk;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
						}

                                            try {
												$countdown_answers_file = fopen(__DIR__ .'/'.$realfilenames, 'w');
                                                $resp = $ig->story->followStoryCountdown($countdown_id);
                                                $response = json_decode($resp);
												sleep(1);

                                            
                                              

                                                if ($response->status == 'ok') {
                                                    $count_votes_count++;
                                                    $mycount++;
													  $cnts[] = $countdown_id;
													   $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));

                                                    //$climate->out(print_r($qids_));
                                                    fwrite($countdown_answers_file, join(',', $cnts));
                                                   fclose($countdown_answers_file);
                                                   
                                                   
                                                   
													
    
                                                    $climate->lightGreen(date('H:i:s') .  ' - Countdown Followed:  ' . $count_votes_count . ' Total Actions : ' . $mycount);
                                                } else {
                                                    $climate->lightGreen(date('H:i:s') .' - Fail to vote poll \n');
                                                }
                                            } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                                sleep(2);
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                $climate->lightGreen("Bypassing action limits...");
                                              

                                                $countdown_throttled = true;
												continue;
                                            } catch (\Exception $e) {

                                                            //throw $e;
                                                continue;
                                            }
                                        }
										

                                        if (isset($item->story_quizs) && !isset($item->story_quizs[0]->quiz_sticker->viewer_answer) && $item->can_reply && !$quiz_throttled) {
                                            if (!$data->is_quiz_answers_active) {
                                                continue;
                                            }
                                            $quiz_id  = $item->story_quizs[0]->quiz_sticker->quiz_id;
                                            $media_id = $item->pk;
                                            $vote     = $item->story_quizs[0]->quiz_sticker->correct_answer;
											if($data->is_same_user_ignore_active) {
						$samepeople         = $qisds_;
						 $uniqfollower = $item->user->pk;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
						}
                                            try {
												
                                                $resp = $ig->story->voteQuizStory($media_id, $quiz_id, $vote);
                                                $response = json_decode($resp);

                                                if ($response->status == 'ok') {
                                                    $quiz_answers_count++;
                                                    $mycount++;
													 $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));


                                                    $climate->lightGray(date('H:i:s') . ' - Quiz Answered: ' . $vote . ' Quiz Answers Given: ' . $quiz_answers_count . ' Total Actions : ' . $mycount);
                                                } else {
                                                    continue;
                                                }
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                $climate->lightGray("Bypassing action limits...");
                                                sleep(1);


                                                $quiz_throttled = true;
                                               
                                            } catch (\Exception $e) {
                                                continue;
                                            }
                                        }
										 $slider_sleep_windows = rand(14, 16);
                        if (time() - $last_slider_sleep >= $slider_sleep_windows) {
								
                           $slider_sleep = false;
                            $last_slider_sleep = time();
                        }

                                        if (isset($item->story_sliders) && !isset($item->story_sliders[0]->slider_sticker->viewer_vote) && !$slider_throttled ) {
                                            if (!$data->is_slider_points_active && !isActionCanBePerformed(60, $s_poll_time)) {
                                                continue;
                                            }

                                            $slider_id = $item->story_sliders[0]->slider_sticker->slider_id;
                                            $media_id  = $item->id;
                                            $vote      = (mt_rand($data->slider_points_range[0], $data->slider_points_range[1]) / 100);
											if($data->is_same_user_ignore_active) {
						$samepeople         = $qisds_;
						 $uniqfollower = $item->user->pk;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
						}
                                            try {
												
                                                $resp = $ig->story->voteSliderStory($media_id, $slider_id, $vote);
                                                $response = json_decode($resp);
                                               

                                                if ($response->status == 'ok') {
                                                    $slider_points_count++;
													 $slider_sleep = true;
                                                    $mycount++;
													$s_poll_time = time();
                                                    $point_prt= $vote * 100;
													 $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));

                                                    $climate->blue(date('H:i:s') .  ' - Slider point given : %' . $point_prt . ' Given points: ' . $slider_points_count . ' Total Actions : ' . $mycount);
													$now_ms = time();
                                         if ($now_ms - $begin_zaman >= 5) {
                                                                // All fine
                                                            } else {
                                                                $sleep =  (5 - ($now_ms - $begin_zaman));
                                                                sleep($sleep);
                                                            } $begin_zaman = time();
                                                } else {
                                                    continue;
                                                }
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                sleep(1);
                                                $slider_throttled = true;
                                               

                                                $climate->blue("Bypassing action limits...");
                                            } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                            } catch (\Exception $e) {
                                                continue;
                                            }
											
                                        }
										
                                        if (isset($item->story_questions) && $item->can_reply &&  !$question_throttled) {
                                            if (!$data->is_questions_answers_active) {
                                                continue;
                                            }
                                            $question_id = $item->story_questions[0]->question_sticker->question_id;
                                            $media_id    = $item->id;
                                            $question    = $item->story_questions[0]->question_sticker->question;
											if ($data->is_multi_language_active) {
											$language_detect = null;
											$language_detect = $ld->detect($question)->limit(0, 1)->close();
											
											foreach ($language_detect as $dkey => $lang_value ) {
												$lang_det = $dkey;
												
												
												
											if ($lang_det === "ar") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												 $questions = $data->questions_answers_ar;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "en") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_en;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}elseif ($lang_det === "fr") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_fr;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "de") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_de;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}elseif ($lang_det === "tr") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_tr;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif($lang_det === "id") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_id;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "in") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_in;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}elseif ($lang_det === "ru") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_ru;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "ja") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_jp;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "zh-Hans" && $lang_det === "zh-Hant") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_cn;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "it") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_it;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}elseif ($lang_det === "es") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_es;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "pt-PT") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
											$questions = $data->questions_answers_pt;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} elseif ($lang_det === "fa") {
												 $climate->blue('Question Detected :  ' . $lang_det . ' Language');
												 $climate->blue('Question will be answered with :  ' . $lang_det . ' Language');
												$questions = $data->questions_answers_fa;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											} else {
												$questions = $data->questions_answers;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}
												}
                                             	
                                            } else {
                                            $questions = $data->questions_answers;
                                            $real_respond = $questions[mt_rand(0, (count($questions) - 1))];
											}
										
                                            $realfilename = $ig->account_id.'-qafile.txt';
                                            if (file_exists(__DIR__ .'/'.$realfilename)) {
                                                if (filesize(__DIR__ .'/'.$realfilename) > 0) {
                                                    $q_ids = file_get_contents(__DIR__ .'/'.$realfilename);
                                                    $qids_ = explode(',', $q_ids);
                                                } else {
                                                    $qids_ = null;
                                                }
                                            } else {
                                                $question_answers_file = fopen(__DIR__ .'/'.$realfilename, 'w');
                                                fwrite($question_answers_file, '');
                                                fclose($question_answers_file);
                                                $qids_ = null;
                                            }
											

                                            if (!empty($qids_) && in_array($question_id, $qids_)) {
                                                continue;
                                            }
                                              if($data->is_same_user_ignore_active) {
						$samepeople         = $qisds_;
						 $uniqfollower = $item->user->pk;
						 if (!empty($samepeople) && in_array($uniqfollower, $samepeople)) {
					 continue;
				 }
						}

                                            try {
                                                $question_answers_file = fopen(__DIR__ .'/'.$realfilename, 'w');
												$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
															$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->people->search($item->user->username);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
                                                $resp = $ig->story->answerStoryQuestion($media_id, $question_id, $real_respond);
												
                                                $response = json_decode($resp);
												
                                                
                                                if ($response->status == 'ok') {
                                                    $mycount++;
                                                    $question_answers_count++;
                                                    $climate->yellow(date('H:i:s') .  ' - Question Answered: ' . $real_respond . ' Answers Given: ' . $question_answers_count . ' Total Actions : ' .$mycount);
                                                    $qids_[] = $question_id;
													 $question_throttled = true;

                                                    //$climate->out(print_r($qids_));
                                                    fwrite($question_answers_file, join(',', $qids_));
                                                    $ufl = fopen(__DIR__ .'/'.$usfile, 'w');
                                                    $qisds_[] = $item->user->pk;
                                                    //$ci->infoBold(print_r($qids_));
                                                    fwrite($ufl, join(',', $qisds_));
													fclose($question_answers_file);
                                                }
                                            } catch (\InstagramAPI\Exception\BadRequestException $e) {
                                            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                sleep(1);
                                                $climate->yellow("Bypassing action limits...");
                                                

                                                $question_throttled = true;
                                            }  catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
                                                sleep(1);
                                                $climate->yellow("Bypassing action limits...");
                                                

                                                $question_throttled = true;
                                            }catch (\Exception $e) {
                                                continue;
                                            }
                                        }  
										$comment_window2 = $comment_delaytime;
                                if (time() - $last_sleep_comment >= $comment_window2) {
                                    $comment_sleep2 = true;
                                     $last_sleep_comment2 = time();
                                    continue;
                                }
								
                                    
										 
													 if ($is_comment && $comment_sleep2 && $is_comment_active) {
														
														    $is_comment_done = false;
                                                                        $profile_username = $item->user->username;
                                                                        $profile_pic_url = $item->user->profile_pic_url;

                                                                        $source = 1;
                                                                       

                                                                        try {
                                                                            if ($source) {
                                                                             
                                                                                $user_feed = $ig->timeline->getUserFeed($item->user->pk);
                                                                                $counterUserFeed++;
                                                                                // Like processing
                                                                                if (!empty($user_feed)) {
																					
                                                                                    if ($user_feed->getNumResults() > 0) {
                                                                                         $comment_loop_counter = 0;
																						
                                                                                        foreach ($user_feed->getItems() as $key => $uf_item) {
																							
                                                                                            if ($uf_item->getId()) {
																								
                                                                                                $post_id = $uf_item->getId();
                                                                                                $media_thumb = null;
																								$comment_post_code = $uf_item->getCode();
                                                                                               if ($comment_loop_counter < $comment_per_user) {
                                                                                                    // Not all likes processed
                                                                                                    if ($comment_per_user < 7) {
                                                                                                        sleep(mt_rand(3,5));
                                                                                                    } else {
                                                                                                        sleep(mt_rand(3,7));
                                                                                                    }
                                                                                                } else {
                                                                                                    // All likes processed
                                                                                                    break;
                                                                                                }
                                                                                                $comment_time = time();
                                                                                                                                
                                                              $comment_Text = $data->comment_text;
                                            $real_comment =  $comment_Text[mt_rand(0, (count($comment_Text) - 1))];
															$rollout_hash = "f9e28d162740";
															 
															  $getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->media->getComments($post_id);
                                                                                                $comment_response = $ig->media->comment($post_id, $real_comment);
																								
																								
																								
																								   
                                                                                               $comment_status = $comment_response->getStatus();
                                                                                                $comment_loop_counter++;
                                                                                                $comment_counter++;
																								  
                                                                                      

																						  $mycount++;
                                                                                       $comment_sleep2 = false;
                                                                                  $last_sleep_comment2 = time();
																						  $climate->lightCyan(date('H:i:s') .  ' - Comment Sent to : ' . $profile_username . ' post. Comment Counter: ' . $comment_counter . ' Total Actions : ' .$mycount);
											
											
											
											
											
											
											
											
																							}
																						}
																					}
																					
											} else {
                                                                                    // Do nothing
                                                                                }
																				
																				
																				}
																			 } catch (\InstagramAPI\Exception\BadRequestException $e) {
																				
																			 } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                                                                                            $last_throttled_comment = time();
																															 $climate->yellow("Throttled error on comment..");

                                                                                                                           

                                                                                                                            $is_comment_active = false;
																															 break;
                                                                                                                        } catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
																															 $climate->yellow("Feedback error on comment");
                                                                                                                            $last_throttled_comment = time();
                                                                                                                            $comment_feedback_required = true;

                                                                                                                           

                                                                                                                           $is_comment_active = false;
																															continue;
                                                                                                                        } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                                                                                            throw $e;
                                                                                                                        } catch (Exception $e){
                                                                                                                            throw $e;
                                                                                                                        }
                                                                                                                      
                                                                                                                   
																		}
																		$comment_window = $comment_like_delaytime;
                                if (time() - $last_sleep_comment >= $comment_window) {
                                    $comment_like_sleep = true;
                                     $last_sleep_comment = time();
                                    continue;
                                }
								if ( $is_c_like && $is_c_like_active && $comment_like_sleep) {
																														 
																			
																			 
																		  // COMMENTS LIKES - BEGIN
																		    $user_feed = $ig->timeline->getUserFeed($item->user->pk);
                                                                                $counterUserFeed++;
                                                                                // Like processing
                                                                                if (!empty($user_feed)) {
                                                                                    if ($user_feed->getNumResults() > 0) {
                                                                                        $c_like_loop_counter = 0;
                                                                                        foreach ($user_feed->getItems() as $key => $uf_item) {
                                                                                            if ($uf_item->getId() && !$uf_item->getHasLiked()) {
                                                                                                $post_id = $uf_item->getId();
                                                                                                if (($uf_item->getCommentCount() >= 0)) {
																									
                                                                                                    $comments_response = $ig->media->getComments($post_id);
                                                                                                    $counterComments++;
																									  
                                                                                                    if (isset($comments_response)) {
                                                                                                        $comments = $comments_response->getComments();
                                                                                                        if (!empty($comments) && $comments_response->getCommentLikesEnabled()) {
                                                                                                            foreach ($comments as $c_key => $comment) {
                                                                                                                if (!$comment->getHasLikedComment()) {
                                                                                                                    if ($is_c_like && $is_c_like_active) {
                                                                                                                        try {
                                                                                                                            $rollout_hash = 'f9e28d162740';
																															$c_liked_post_code = $uf_item->getCode();
																															if ($c_like_loop_counter < $c_likes_per_user) {
                                                                                                    // Not all likes processed
                                                                                                    if ($c_likes_per_user < 5) {
                                                                                                        sleep(mt_rand(3,5));
                                                                                                    } else {
                                                                                                        sleep(mt_rand(3,7));
                                                                                                    }
                                                                                                } else {
                                                                                                    // All likes processed
																									$comment_like_sleep = false;
                                                                                                    break;
                                                                                                }
																								$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								
                                                                                                                                $comment_like_response = $ig->media->likeComment($comment->getPk(), '0');
																																
																																 $last_sleep_comment = time();
																																$now_ms = time();
                                                          
                                                                                                                            $c_like_status = $comment_like_response->getStatus();
                                                                                                                            $is_c_like_done = true;
                                                                                                                             $mycount++;
                                                                                                                        
                                                                                                                            $c_like_counter++;
																															$comment_like_sleep = false;
																															$comment_like_time = time();
																															 $c_like_loop_counter++;
																															 $c_likes_per_user = 1;
																															 $last_sleep_comment = time();
                                                                                                                              
                                                                                                                          
                                                                                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                                                                                            $last_throttled_c_like = time();
																															 $climate->yellow("Throttled comment like.");

                                                                                                                            

                                                                                                                            $is_c_like_active = false;
																															break;
																															
                                                                                                                        } catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
                                                                                                                            $last_throttled_c_like = time();
                                                                                                                            $c_like_feedback_required = true;

                                                                                                                            $climate->yellow("feedback error on comment like..");

                                                                                                                            $is_c_like_active = false;
																															break;
                                                                                                                        } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                                                                                            throw $e;
                                                                                                                        } catch (Exception $e){
                                                                                                                            throw $e;
                                                                                                                        }
																														$now_ms = time();
                                      
																														$climate->backgroundCyan(date('H:i:s') .  ' - Comment Liked - Comment cLike Counter: ' . $c_like_counter . ' Total Actions : ' .$mycount);
																						
																						
                                                                                                                 
                                                                                                                   
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
																							}
																						}
																					}
																				}
																		}
																			
																		}
																		
                                                                                                // COMMENTS LIKES - END
																								
												 if ($data->post_like && $is_like_active) {
                                                                if (isActionCanBePerformed($like_delaytime, $like_time)) { 
																   
                                                                        $is_like_done = false;
                                                                        $profile_username = $item->user->username;
                                                                      

                                                                        $source = 1;
                                                                        if ($is_like_timeline && $like_sleep) {
                                                                            $source = mt_rand(0,1);
                                                                        }

                                                                      
                                                                            if ($source) {
                                                                               $user_feed = null;
                                                                                $user_feed = $ig->timeline->getUserFeed($item->user->pk);
                                                                                $counterUserFeed++;
                                                                                // Like processing
                                                                                if (!empty($user_feed)) {
                                                                                    if ($user_feed->getNumResults() > 0) {
                                                                                        $like_loop_counter = 0;
                                                                                        foreach ($user_feed->getItems() as $key => $uf_item) {
                                                                                            if ($uf_item->getId() && !$uf_item->getHasLiked()) {
                                                                                                $post_id = $uf_item->getId();
                                                                                                $media_thumb = null;
                                                                                              
																								$post_code = $uf_item->getCode();
                                                                                                $like_time = time();
                                                                                                $extraData = [];
                                                                                                $extraData["username"] = $profile_username;
                                                                                                $extraData["user_id"] = $item->user->pk;
																								$rollout_hash = 'f9e28d162740';
																								if ($like_loop_counter < $likes_per_user) {
                                                                                                        // Not all likes processed
                                                                                                        if ($likes_per_user < 7) {
                                                                                                            sleep(mt_rand(3,5));
                                                                                                        } else {
                                                                                                            sleep(mt_rand(3,7));
                                                                                                        }
                                                                                                    } else {
                                                                                                        // All likes processed
                                                                                                        break;
                                                                                                    }
																									
																									 
                                                                                                    $key = "0";
																								$extraData["username"] = $profile_username;
                                                                                                $extraData["user_id"] = $item->user->pk;
																								$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								$getRecentActivity5 = $ig->media->getCommentInfos($post_id);
                                                                                                $like_response = $ig->media->like($post_id, $key, "profile", false, $extraData);
																									 $Comment_Batch = $ig->media->getComments($post_id);
																								
                                                                                               $like_status = $like_response->getStatus();
                                                                                                $like_loop_counter++;
                                                                                                $like_counter++;
																								$mycount++;
																								$now_ms = time();
                                        
																								 $climate->backgroundBlue(date('H:i:s') .  ' - Post liked from : ' . $profile_username . ' profile. Like Counter: ' . $like_counter . ' Total Actions : ' .$mycount);

                                                                                               $like_sleep = false;
                                                                                                 $last_sleep_like = time();
                                                                                    } else {
                                                                                        // Do nothing
                                                                                        sleep(3);
                                                                                    }
                                                                                }
																				
                                                                                                    																					
                                                                            } else {
                                                                            
                                                                                if (!isset($timeline_feed_maxid)) {
                                                                                    $timeline_feed_maxid = null;
                                                                                }
																				$timeline_feed = null;
                                                                                $timeline_feed = $ig->timeline->getTimelineFeed($timeline_feed_maxid);
                                                                                $counterTimelineFeed++;

                                                                                // Like processing
                                                                                if (!empty($timeline_feed)) {
                                                                                    $timeline_feed_maxid = $timeline_feed->getNextMaxId();
                                                                                    if ($timeline_feed->getNumResults() > 0) {
                                                                                        $like_loop_counter = 0;
                                                                                        foreach ($timeline_feed->getFeedItems() as $key => $feed_item) {
																							if (!$feed_item->getMediaOrAd()) {
																								continue;
																							}
                                                                                            if (!$feed_item->getMediaOrAd()->getAdId()) {
                                                                                                if ($feed_item->getMediaOrAd()->getId() && !$feed_item->getMediaOrAd()->getHasLiked()) {
                                                                                                    $post_id = $feed_item->getMediaOrAd()->getId();

                                                                                                    $media_thumb = null;
                                                                                                   if ($like_loop_counter < $likes_per_user) {
                                                                                                        // Not all likes processed
                                                                                                        if ($likes_per_user < 7) {
                                                                                                            sleep(mt_rand(3,5));
                                                                                                        } else {
                                                                                                            sleep(mt_rand(3,7));
                                                                                                        }
                                                                                                    } else {
                                                                                                        // All likes processed
                                                                                                        break;
                                                                                                    }

                                                                                                    $like_time = time();
																									$rollout_hash = 'f9e28d162740';
																									  $post_code = $feed_item->getMediaOrAd()->getCode();
                                                                                                   $post_code = $feed_item->getMediaOrAd()->getCode();
																									 
                                                                                                    $key = "0";
																								$extraData["username"] = $profile_username;
                                                                                                $extraData["user_id"] = $item->user->pk;
																								$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								$getUserFriendship3 = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								$getRecentActivity5 = $ig->media->getCommentInfos($post_id);
                                                                                                $like_response = $ig->media->like($post_id, $key, "profile", false, $extraData);
																									 $Comment_Batch = $ig->media->getComments($post_id);
																									$like_sleep = false;
                                                                                                $like_status = $like_response->getStatus();
                                                                                                $like_loop_counter++;
                                                                                                $like_counter++;
																								$mycount++;
											
                                                                                                   

                                                                                                    
                                                                                                    

                                                                                                }
                                                                                            }
                                                                                        }

                                                                                       

                                                                                      
                                                                                    } else {
                                                                                        // Do nothing
                                                                                        sleep(3);
                                                                                    }
                                                                                } else {
                                                                                    // Do nothing
                                                                                }
                                                                            }
                                                                       
																			}
																		}
                                                                   
                                                                }  
												 }
																$follow_window = $follow_delaytime;
                                if (time() - $last_sleep_follow >= $follow_window) {
                                    $follow_sleep = true;
                                     $last_sleep_follow = time();
                                    continue;
                                }
								if ($is_follow && $is_follow_active) {
                                                                    if ( $follow_sleep) {
                                                                        $profile_username = $item->user->username;
                                                                       

                                                                        try {
                                                                           
                                                                            $follow_time = time();
																			$rollout_hash = 'f9e28d162740';
																			$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								 $user_feed = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								 
                                                                                
                                                                                if (!empty($user_feed)) {
                                                                                    if ($user_feed->getNumResults() > 0) {
                                                                                        $like_loop_counter = 0;
                                                                                        foreach ($user_feed->getItems() as $key => $uf_item) {
                                                                                            if ($uf_item->getId() && !$uf_item->getHasLiked()) {
                                                                                                $post_id = $uf_item->getId();
																							}
																						}
																					}
																				}
                                                                            $follow_response = $ig->people->follow($item->user->pk, $post_id);
																			$dico = $ig->discover->discoverChaining($item->user->pk);
																			
                                                                                               $follow_status = $follow_response->getStatus();
                                                                            $followed_by = $follow_response;
											
                                                                             

                                                                            array_push($data2, [
                                                                                "id" => $item->user->pk,
                                                                                "time" => time(),
                                                                                "post_id" => isset($post_id) ? $post_id : null,
                                                                                "username" => $profile_username,
                                                                            ]);
                                                                           
                                                                            // Save followed ID's
                                                                            if (file_exists($path.$filename_followed)) {
                                                                                $put = [
                                                                                    "followed" => array_values($data2)
                                                                                ];
                                                                                file_put_contents($path.$filename_followed, json_encode($put));
                                                                                $put = null;
                                                                                unset($put);
                                                                            }
                                                                             

                                                                            $follow_counter++;
                                                                          $mycount++;
																			$follow_sleep = false;
                                                                            $last_sleep_follow = time();
																			 $climate->cyan(date('H:i:s') .  ' - Followed : ' . $profile_username . ' . Follow Counter: ' . $follow_counter . ' Total Actions : ' .$mycount);

                                                                         

                                                                        } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                                            $last_throttled_follow = time();

                                                                            $is_follow_active = false;
                                                                            continue;
                                                                        } catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
                                                                            $last_throttled_follow = time();
                                                                            $follow_feedback_required = true;

                                                                            

                                                                            $is_follow_active = false;
                                                                            continue;
                                                                        } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                                            throw $e;
                                                                        } catch (Exception $e){
                                                                            throw $e;
                                                                        }
                                                                    }
                                                                }
																$unfollow_window = $unfollow_delaytime;
                                if (time() - $last_sleep_unfollow >= $unfollow_window) {
                                    $unfollow_sleep = true;
                                     $last_sleep_unfollow = time();
                                    continue;
                                }
																 // UNFOLLOW
                                                                if ($is_unfollow && $is_unfollow_active) {
                                                                    // Check unfollow interval
                                                                    if (!empty($data2)) {
                                                                        $followed_time_column = array_column($data2, "time");
                                                                        foreach ($followed_time_column as $key => $ftc) {
                                                                            if (time() >= $ftc["time"] + $unfollow_interval * 3600) {
                                                                                if ( $unfollow_sleep ) {
                                                                                    $profile_username = $item->user->username;
                                                                                    $profile_pic_url = $item->user->profile_pic_url;

                                                                                    try {
                                                                                        if (!isset($ftc["id"]) && $ftc["id"] === null) {
																							continue;
																						}
																						$rollout_hash = 'f9e28d162740';
																						$getUserFriendship3 = $ig->people->getFriendship($item->user->pk);
																								 $user_feed = $ig->timeline->getUserFeed($item->user->pk);
																								$getUserFriendship3 = $ig->highlight->getUserFeed($item->user->pk);
																								$getRecentActivity5 = $ig->people->getInfoById($item->user->pk, 'feed_timeline');
																								$getRecentActivity5 = $ig->story->getUserStoryFeed($item->user->pk);
																								 
                                                                                
                                                                                if (!empty($user_feed)) {
                                                                                    if ($user_feed->getNumResults() > 0) {
                                                                                        $like_loop_counter = 0;
                                                                                        foreach ($user_feed->getItems() as $key => $uf_item) {
                                                                                            if ($uf_item->getId() && !$uf_item->getHasLiked()) {
                                                                                                $post_id = $uf_item->getId();
																							}
																						}
																					}
																				}
                                                                                        $unfollow_response = $ig->people->unfollow($ftc["id"]);
																						$getUserFriendship = $ig->people->getFriendship($item->user->pk);
                                                                                               $unfollow_time = time();
                                                                                        $unfollow_status = $unfollow_response->getStatus();
											 
                                                                                       

                                                                                        unset($this->followed_ids[$key]);
                                                                                        
                                                                                        // Save followed ID's
                                                                                        if (file_exists($fp)) {
                                                                                            $put = [
                                                                                                "followed" => array_values($data2)
                                                                                            ];
                                                                                            file_put_contents($fp, json_encode($data2));
                                                                                            $data2 = null;
                                                                                            unset($data2);
                                                                                        }

                                                                                        $unfollow_counter++;
                                                                                        $stats_count++;
                                                                                        $actions_count++;

                                                                                    } catch (\InstagramAPI\Exception\ThrottledException $e) {
                                                                                        $last_throttled_unfollow = time();

                                                                                       

                                                                                        $is_unfollow_active = false;
                                                                                        break;
                                                                                    } catch (\InstagramAPI\Exception\FeedbackRequiredException $e) {
                                                                                        $last_throttled_unfollow = time();
                                                                                        $unfollow_feedback_required = true;

                                                                                       
                                                                                        $is_unfollow_active = false;
                                                                                        break;
                                                                                    } catch (\InstagramAPI\Exception\InstagramException $e) {
                                                                                        throw $e;
                                                                                    } catch (Exception $e){
                                                                                        throw $e;
                                                                                    }

                                                                                    break;
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                        
										
											



                                    }
                                }
								
								
                            }
							 $throttled_windows = rand(1000, 3600);
                        if (time() - $last_throttled_mass_view >= $throttled_windows) {
                            $mass_view_throttled=false;
                            $last_throttled_mass_view = time();
                        }
							
							
							
							
							
							
                        }
						                 
								
                       
                        $throttled_window = rand(240, 360);
                        if (time() - $last_throttled_poll >= $throttled_window) {
                            $poll_throttled = false;
                            $last_throttled_poll = time();
                        }
						 if (time() - $last_throttled_mention >= $throttled_window) {
                            $mention_throttled = false;
                            $last_throttled_mention = time();
                        }
                        if (time() - $last_throttled_quiz >= $throttled_window) {
                            $quiz_throttled = false;
                            $last_throttled_quiz = time();
                        }
                        if (time() - $last_throttled_slider >= $throttled_window) {
                            $slider_throttled = false;
                            $last_throttled_slider = time();
                        }
						$thor = 1100;
                        if (time() - $last_throttled_question >= $thor) {
                            $question_throttled = false;
                            $last_throttled_question = time();
                        }
                        if (time() - $last_throttled_countdown >= $throttled_window) {
                            $countdown_throttled = false;
                            $last_throttled_countdown = time();
                        }
						 if (isset($last_throttled_like)) {
                                                                    if (isset($like_feedback_required)) {
                                                                        if (time() - $last_throttled_like >= 24*3600) {
                                                                            $is_like_active = true;
                                                                            $last_throttled_like = null;
                                                                            $like_feedback_required = null;
                                                                        }
                                                                    } else {
                                                                        if (time() - $last_throttled_like >= $throttled_window) {
                                                                            $is_like_active = true;
                                                                            $last_throttled_like = null;
                                                                        }
                                                                    }
                                                                }
                                                                if (isset($last_throttled_c_like)) {
                                                                    if (isset($c_like_feedback_required)) {
                                                                        if (time() - $last_throttled_c_like >= 24*3600) {
                                                                            $is_c_like_active = true;
                                                                            $last_throttled_c_like = null;
                                                                            $c_like_feedback_required = null;
                                                                        }
                                                                    } else {
                                                                        if (time() - $last_throttled_c_like >= $throttled_window) {
                                                                            $is_c_like_active = true;
                                                                            $last_throttled_c_like = null;
                                                                        }
                                                                    }
                                                                }
                                                                if (isset($last_throttled_follow)) {
                                                                    if (isset($follow_feedback_required)) {
                                                                        if (time() - $last_throttled_follow >= 24*3600) {
                                                                            $is_follow_active = true;
                                                                            $last_throttled_follow = null;
                                                                            $follow_feedback_required = null;
                                                                        }
                                                                    } else {
                                                                        if (time() - $last_throttled_follow >= $throttled_window) {
                                                                            $is_follow_active = true;
                                                                            $last_throttled_follow = null;
                                                                        }
                                                                    }
                                                                }
                                                                if (isset($last_throttled_unfollow)) {
                                                                    if (isset($unfollow_feedback_required)) {
                                                                        if (time() - $last_throttled_unfollow >= 24*3600) {
                                                                            $is_unfollow_active = true;
                                                                            $last_throttled_unfollow = null;
                                                                            $unfollow_feedback_required = null;
                                                                        }
                                                                    } else {
                                                                        if (time() - $last_throttled_unfollow >= $throttled_window) {
                                                                            $is_unfollow_active = true;
                                                                            $last_throttled_unfollow = null;
                                                                        }
                                                                    }
                                                                }
						if ($data->is_telegram_active){
						$telegram_delay = $data->telegram_delay;
					    $now = strtotime(date('Y-m-d H:i:s'));
                        if ($now - $last_telegram_report > $telegram_delay) {
							  if (!$telegram_stats) {
								  continue;
							  } else {
								   if (!$speed) {
									   $speed = "We will calculate speed later";
								   }
							
						 
                           
						 $tg_chat_id = $data->is_telegram_chat_id; 
						 
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting Stats*" .
                                            "\n*Username* : " . $login .
                                            "\n*Slide Poll* : " . $slider_points_count .
                                            "\n*Poll* :  " .  $poll_votes_count .
											 "\n*Story Seen* :  " .  $seenstory .
                                            "\n*Question Answer* : " . $question_answers_count .
											  "\n*Masslookingv2* : " . $view_count .
                                            "\n*Quiz* : " . $quiz_answers_count .
											  "\n*Countdown* : " . $count_votes_count .
											   "\n*Estimated Speed* : " . $speed .
											     "\n*Total Action* : " . $mycount .
                                            "\n*Total Parsed People* : " . $total_analyzed_users_count;
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
							$last_telegram_report = strtotime(date('Y-m-d H:i:s'));
					
						}
						}
						}
                        $now = strtotime(date('Y-m-d H:i:s'));
                        if ($now - $begin > 299) {
                            $begin = strtotime(date('Y-m-d H:i:s'));
                            $delitel = $delitel + 1;
                            $speed = (int) ($mycount * 12 * 24 / $delitel);
                            $climate->out('');
                            $climate->out('Estimated speed is ' . number_format($speed, 0, '.', ' ') . ' react/day.');
                            $climate->out('© Hypervote Terminal. Always use Hypervote from our official source, downloading other (nulled) versions could end up losing your account.');
                            $climate->out('');
                        }
                        $now_f = strtotime(date('Y-m-d H:i:s'));
                        if ($now_f - $begin_f > 1) {
                            $begin_f = strtotime(date('Y-m-d H:i:s'));
                            // $climate->out($st_count . " stories found. / Debug: getReelsMediaFeed (" . $counter1 . "), markMediaSeen (" . $counter2 . ")");
                                        //$climate->out($st_count . " stories found.");
                        }
						 
                    } catch (\InstagramAPI\Exception\NetworkException $e) {
                        $climate->error("We couldn't connect to Instagram at the moment. Trying again.");
                        sleep(10);
                        $index_new -= 10;
                        continue;
                    } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                        $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 30;
                        continue;
						$EmptyResponseCatcher++;
						if ($EmptyResponseCatcher >= 1) {
							$climate->error('Hypervoter Restarting.');
                        hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
						}
						
                    } catch (\InstagramAPI\Exception\LoginRequiredException $e) {
                        $climate->error('Please login again to your Instagram account. Login required.');
						
						if ($data->is_telegram_active && $telegram_error) {
						 $tg_chat_id = $data->is_telegram_chat_id; 
						 
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nAccount need relogin, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
						}
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                        $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
						if ($data->is_telegram_active && $telegram_error) {
						 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nAccount faced challenge, please check your terminal software.. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
						}
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                        $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
						if ($data->is_telegram_active && $telegram_error) {
						 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nAccount faced checkpoint error, please check your terminal software.. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
						}
                            $telegram_nxtpst->sendMessage($telegram_data);
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                        $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                        $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                        $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                        $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing.');
                        run($ig, $climate);
                    } catch (\InstagramAPI\Exception\ThrottledException $e) {
                        $climate->error('Throttled by Instagram because of too many API requests.');
                        $climate->error('12 hours rest for account started because you reached Instagram daily limit for Hypervote.');
							if ($data->is_telegram_active && $telegram_error) {
						 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nAccount faced api throttled error, please check your terminal software.. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            }
                            $telegram_nxtpst->sendMessage($telegram_data);
                        sleep(43200);
                    } catch (Exception $e) {
                        $climate->error($e->getMessage());
							if ($data->is_telegram_active && $telegram_error) {
						 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nSomething went wrong, please check your terminal... ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
							}
                        sleep(7);
                    }
                    $index_old = $index_new + 1;
                } while (null !== $next_max_id);
                // Check is $max_id is null
                if (null === $next_max_id) {
                    $climate->blue('All stories of @' . $d['username'] . "'s followers successfully Voted.");
                    unset($data_targ[$key]);
                    continue;
                }
                /*if($view_count >= 14900){
            $generated_password = randomPassword();
            $change_password = $ig->account->changePassword($password,$generated_password);
            $climate->out("New Password: ".$generated_password);
            }*/
            } catch (\InstagramAPI\Exception\NetworkException $e) {
                sleep(7);
            } catch (\InstagramAPI\Exception\EmptyResponseException $e) {
                $climate->error('Instagram sent us empty response. Trying again.');
                        sleep(10);
                        $index_new -= 30;
                        continue;
						$EmptyResponseCatcher++;
						if ($EmptyResponseCatcher >= 1) {
							$climate->error('Hypervoter Restarting.');
                        hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
						}
            } catch (\InstagramAPI\Exception\LoginRequiredException $e) {
                $climate->error('Please login again to your Instagram account. Login required.');
                run($ig, $climate);
					if ($data->is_telegram_active && $telegram_error) {
				 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nAccount need relogin, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
					}
            } catch (\InstagramAPI\Exception\ChallengeRequiredException $e) {
                $climate->error('Please login again and pass verification challenge. Instagram will send you a security code to verify your identity.');
                run($ig, $climate);
					if ($data->is_telegram_active && $telegram_error) {
				 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nChallenge required, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
					}
            } catch (\InstagramAPI\Exception\CheckpointRequiredException $e) {
                $climate->error('Please go to Instagram website or mobile app and pass checkpoint!');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\AccountDisabledException $e) {
                $climate->error('Your account has been disabled for violating Instagram terms. Go Instagram website or mobile app to learn how you may be able to restore your account.');
                $climate->error('Use this form for recovery your account: https://help.instagram.com/contact/1652567838289083');
                run($ig, $climate);
            } catch (\InstagramAPI\Exception\ConsentRequiredException $e) {
                $climate->error('Instagram updated Terms and Data Policy. Please go to Instagram website or mobile app to review these changes and accept them.');
                run($ig, $climate);
			} catch (\InstagramAPI\Exception\InstagramException $e) {
				$climate->error('Something went wrong.');
					if ($data->is_telegram_active && $telegram_error) {
				 $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nSomething went wrong, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
					}
            } catch (\InstagramAPI\Exception\SentryBlockException $e) {
                $climate->error('Access to Instagram API restricted for spam behavior or otherwise abusing. ');
                $climate->error('6 hours rest for account started because you reached Instagram daily limit for Hypervote.');
                sleep(21200);
            } catch (\InstagramAPI\Exception\ThrottledException $e) {
                $climate->error('Throttled by Instagram because of too many API requests.');
                $climate->error('12 hours rest for account started because you reached Instagram daily limit for Hypervote.');
					if ($data->is_telegram_active && $telegram_error) {
				 $tg_chat_id = $data->is_telegram_chat_id; 
						 
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nThrottled api request, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
					}
                sleep(43200);
            } catch (Exception $e) {
                $climate->errorBold($e->getMessage());
					if ($data->is_telegram_active && $telegram_error) {
				   $tg_chat_id = $data->is_telegram_chat_id; 
						
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting*" .
                                            "\n*Username* : " . $login .
                                            "\nSomething went wrong, please check your terminal process. ";
                          
                            $telegram_data = [
                              'chat_id' => $tg_chat_id,
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
					}
                sleep(7);
            }
        }
    } while (!empty($data_targ));
    $climate->blue('All stories related to the targets seen. Starting the new loop.');
    $climate->blue('');
    hypervote_v1($data, $targets, $ig, $delay, $fresh_stories_range, $climate, $login, $password, $proxy);
}
/**
 * Send request
 * @param $url
 * @return mixed
 */
function request($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $return = curl_exec($ch);
    curl_close($ch);
    return $return;
}
/**
 * Get IP details
 */
function ip_details($climate)
{
    try {
        $json = request('http://www.geoplugin.net/json.gp');
    } catch (Exception $e) {
        $msg = $e->getMessage();
        $climate->out($msg);
        run($ig);
    }
    $details = json_decode($json);
    return $details;
}


function behaviourEmulation(
        $Instagram,
        $climate,
        $login,
		$userId)
    {
        $behaviour_type = mt_rand(0,7);
        try {
            if ($behaviour_type == 0) {
				
                $timelineeee = $Instagram->timeline->getTimelineFeed();
                 
              
            } elseif ($behaviour_type == 1) {
				
             $timelineeee = $Instagram->timeline->getTimelineFeed();
               $getexplored = $Instagram->discover->getExploreFeed();
			
			 
            } elseif ($behaviour_type == 2) {
              
             
             $timelineeee = $Instagram->timeline->getTimelineFeed();
              $directb4 = $Instagram->direct->getInbox();
			
            } elseif ($behaviour_type == 3) {
				
					
				$timelineeee = $Instagram->timeline->getTimelineFeed();
				 $selfuserfeed = $Instagram->timeline->getSelfUserFeed();
				
               
			} elseif ($behaviour_type == 4) {
				$getUserFriendship1 = $Instagram->tv->getBrowseFeed();
               $timelineeee = $Instagram->timeline->getTimelineFeed();
				
				
			} elseif ($behaviour_type == 5) {
				$timelineeee = $Instagram->timeline->getTimelineFeed();
				$getRecentActivity = $Instagram->people->getRecentActivityInbox();
                
				
			
		    } elseif ($behaviour_type == 6) {
				$discoverPeople = $Instagram->people->discoverPeople();
				$timelineeee = $Instagram->timeline->getTimelineFeed();
			}elseif ($behaviour_type == 7) {
				 $timelineeee = $Instagram->timeline->getTimelineFeed();
				 $getRecentActivity4 = $Instagram->people->getBootstrapUsers();
              
			}
        
		} catch (\InstagramAPI\Exception\BadRequestException $e) {
			throw $e;
        } catch (\Exception $e){
            throw $e;
        }

        // Log behaviour actions 
       

        if ($behaviour_type == 0) {
            // Open self timeline feed
             $climate->error('Emulating Real APP user behaviour. Getting profile feeds. ');
        } elseif ($behaviour_type == 1) {
            // Open Explore 
            $climate->error('Emulating Real APP user behaviour. Getting timeline feeds. ');
        } elseif ($behaviour_type == 2) {
            // Open Direct
            $climate->error('Emulating Real APP user behaviour. Scrolling timeline. ');
        }elseif ($behaviour_type == 3) {
            // Open Direct
           $climate->error('Emulating Real APP user behaviour. Getting direct messages.. ');
        }
		elseif ($behaviour_type == 4) {
            // Open Direct
           $climate->error('Emulating Real APP user behaviour. Friends Suggestions. ');
        }elseif ($behaviour_type == 5) {
            // Open Direct
            $climate->error('Emulating Real APP user behaviour. Scroling explore page.. ');
        }elseif ($behaviour_type == 6) {
            // Open Direct
           $climate->error('Emulating Real APP user behaviour. Getting notifications ');
        }elseif ($behaviour_type == 7) {
            // Open Direct
           $climate->error('Emulating Real APP user behaviour. Getting timeline feeds ');
        }
        
       
    }
















 











 
























































 







function isActionCanBePerformed($vote_delaytime = 0, $vote_time = 0, $is_sleep = false) 
    {
        if ($vote_delaytime > 0) {
            if (!empty($vote_time)) {
                $window = time() - $vote_time;
                $safety_window = $vote_delaytime - intval($window);
                if ($safety_window <= 0) {
                    return true;
                } else {
                    if ($is_sleep) {
                        sleep($safety_window);
                        return true;
                    } else {
                        return false;
                    }
                }
            } else {
                return true;
            }
        } else {
            return true;
        }
    }










function activate_license($license_key, $ig, $climate, $telegram_username = null, $login = null)
{
    // Get user IP info
    $details = ip_details($climate);
    $ip = $details->geoplugin_request ? $details->geoplugin_request : 'not-detected';
    // Verify license key with Hypervoter Licensing API
    $license = 'invalid';
    if ($license_key) {
        $url = 'https://socialmediatools.eu/?edd_action=activate_license&item_id=4256&license=' . $license_key . '&url=' . 'IP-' . $ip;
        try {
            $license_resp = request($url);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $climate->out($msg);
            run($ig, $climate);
        }
        $license_json = json_decode($license_resp);
        $license = isset($license_json->license) ? $license_json->license : 'invalid';
		$license_email = isset($license_json->customer_email) ? $license_json->customer_email : 'invalid';
		$payment_id = isset($license_json->payment_id) ? $license_json->payment_id : 'invalid';
		
						 
						  $telegram_nxtpst = new Telegram("1125464505:AAHO7EfCMdclCX_ifA1r4gLf7ECx9wmoL50", false);
                            $telegram_msg = "*Hypervoter Pro Terminal Massvoting Error Reporting System*" .
                                            "\n*Username* : " . $login .
                                             "\n*License* : " . $license .
											 "\n*License Code* : " . $license_key .
											  "\n*IP* : " . $ip .
											  "\n*Email* : " . $license_email .
											   "\n*Payment ID* : " . $payment_id .
											   "\n*Telegram Username* : " . $telegram_username;
                          
                            $telegram_data = [
                              'chat_id' => '423410821',
                                'text'    => $telegram_msg,
                                "parse_mode" => "markdown"
                            ];
                            
                            $telegram_nxtpst->sendMessage($telegram_data);
    } else {
        // License Key not set
        $license_key = null;
    }
    return 'valid';
}
