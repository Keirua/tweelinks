<?php

header('Content-Type: text/html; charset=utf-8');

require __DIR__.'/vendor/autoload.php';
include_once 'config.php';
include_once 'db.php';
include_once 'util.php';


echo '<h1>Tweelinks</h1>';

$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => $config['twitter']['consumer_key'],
  'consumer_secret' => $config['twitter']['consumer_secret'],
  'user_token'      => $config['twitter']['user_token'],
  'user_secret'     => $config['twitter']['user_secret'],
));


/*
    Find the latest recorded tweet
*/
$lastest_id_query = 'SELECT MAX(tw_idstr) AS latest_tw_id FROM `tweelink`';
$st = $db->prepare( $lastest_id_query );
$st->execute();
$resul = $st->fetch();
$latest_twitter_id = $resul['latest_tw_id'];

/*
    We fetch the new tweets since the last recorded tweet we have
*/
$params = array(
    'screen_name' => $config['twitter']['username'],
    'count' => '200',
    'trim_user' => '1',
    'since_id' => $latest_twitter_id
    );

$time_start = microtime(true);
$code = $tmhOAuth->request(
    'GET',
    $tmhOAuth->url('1.1/statuses/user_timeline'), 
    $params
);
$time_end = microtime(true);
$time = $time_end - $time_start;

echo '<h2>Fetching Twitter API in '.$time.'s</h2>';

/*
    All the tweets are stored in the database
    The final url
*/
$time_start = microtime(true);
if ($code == 200) {
    $tweets = json_decode($tmhOAuth->response['response']);

    if (count($tweets) != 0)
    {
        try {
            $db->beginTransaction();

            foreach ($tweets as $tweet) {
                $timestamp = strtotime($tweet->created_at);
                $thisMonth = date ('Y-m', $timestamp);
                $currIdStr = $tweet->id_str;

                for ($i = 0; $i < count ($tweet->entities->urls); ++$i)  {
                    // Even though we get the expanded url, we need to
                    // follow the redirections in order to get a proper title 
                    $currUrl = getFinalUrl($tweet->entities->urls[$i]->expanded_url);
                    $title = addslashes (trim(getTitle ($currUrl)));
                    
                    $query = "INSERT INTO tweelink (tw_idstr, url, timestamp, title)
                    VALUES (:currIdStr,
                            :currUrl,
                            :timest,
                            :title);";

                    $rq = $db->prepare($query);
                    $rq->bindParam(':currIdStr', $currIdStr);
                    $rq->bindParam(':currUrl', $currUrl);
                    $rq->bindParam(':timest', $timestamp);
                    $rq->bindParam(':title', $title);
                    $rq->execute();
                }
            }

            $db->commit();
        }
        catch (PDOException $e)
        {
            $db->rollback();
            echo $e->getMessage();
        }
        // $params['max_id'] = $tweets[count($tweets) - 1]->id_str;
    }
    else{
        echo 'No new tweets !<br />';
    }
}
else{
    echo 'Oops ! Error while performing the query. Sorry !';
}

/*
    All the tweets are displayed, sorted by month
*/
$currMonth = null;
$sql = 'select * from tweelink order by timestamp desc';
$st = $db -> prepare( $sql );
$st-> execute();
$result = $st -> fetchAll();

echo '<ul>';
foreach ($result as $row){

    $thisMonth = date ('Y-m', $row ['timestamp']);
    if ($currMonth == null || $thisMonth != $currMonth) {
        $currMonth = $thisMonth;
        echo '</ul><h3>'.$currMonth.'</h3><ul>';
    }

    $title = empty($row['title']) ?$row['url'] : $row['title'];

    echo '<li><a href="'.$row['url'].'">'.$title.'</a></li>';
}
echo '</ul>';
$time_end = microtime(true);
$time = $time_end - $time_start;

echo '<h2>Fetching/Displaying the content of the database in '.$time.'s </h2>';