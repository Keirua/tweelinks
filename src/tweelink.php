<?php

class Tweelink {
    private $tmhOAuth;
    private $db;

    public function construct ($config){
        $this->config = $config;

        $this->tmhOAuth = new tmhOAuth(array(
          'consumer_key'    => $config['twitter']['consumer_key'],
          'consumer_secret' => $config['twitter']['consumer_secret'],
          'user_token'      => $config['twitter']['user_token'],
          'user_secret'     => $config['twitter']['user_secret'],
        ));

        $this->db = new PDO(
                'mysql:host='.$config['db']['host'].';dbname='.$config['db']['db_name'],
                $config['db']['login'],
                $config['db']['password']
            );
    }

    public function cacheNewTweets () {
        /*
            Find the latest recorded tweet
        */
        $lastest_id_query = 'SELECT MAX(tw_idstr) AS latest_tw_id FROM `tweelink`';
        $st = $this->db->prepare( $lastest_id_query );
        $st->execute();
        $resul = $st->fetch();
        $latest_twitter_id = $resul['latest_tw_id'];

        /*
            Fetch the new tweets since the last recorded tweet we have
        */
        $params = array(
            'screen_name' => $this->config['twitter']['username'],
            'count' => '200',
            'trim_user' => '1',
            'since_id' => $latest_twitter_id
            );

        $code = $this->tmhOAuth->request(
            'GET',
            $tmhOAuth->url('1.1/statuses/user_timeline'), 
            $params
        );

        /*
            All the tweets are stored in the database
        */
        if ($code == 200) {
            $tweets = json_decode($tmhOAuth->response['response']);

            if (count($tweets) != 0)
            {
                try {
                    $this->db->beginTransaction();

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

                            $rq = $this->db->prepare($query);
                            $rq->bindParam(':currIdStr', $currIdStr);
                            $rq->bindParam(':currUrl', $currUrl);
                            $rq->bindParam(':timest', $timestamp);
                            $rq->bindParam(':title', $title);
                            $rq->execute();
                        }
                    }

                    $this->db->commit();
                }
                catch (PDOException $e)
                {
                    $this->db->rollback();
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
    }

    public function displayCacheContent (){
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
    }


    /**
     * Take an URL as input, and unshortens it by following the redirections.
     * Found at http://jonathonhill.net/2012-05-18/unshorten-urls-with-php-and-curl/
     * @params $url Input url
     * @return Url after all the redirections
     */
    private function getFinalUrl($url){
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_FOLLOWLOCATION => TRUE,  // the magic sauce
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_SSL_VERIFYHOST => FALSE, // suppress certain SSL errors
            CURLOPT_SSL_VERIFYPEER => FALSE, 
        ));
        curl_exec($ch);
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);
        return $url;
    }

    /**
     * Take an URL as input, and unshortens it by following the redirections.
     * Found at http://jonathonhill.net/2012-05-18/unshorten-urls-with-php-and-curl/
     * @params $url Input url
     * @return The page title
     */
    private function getTitle($url){
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5); 

        $content = curl_exec($ch);
        
        curl_close($ch);
        $title = '';
        if(strlen($content) > 0 && preg_match('/\<title\b.*\>(.*)\<\/title\>/i', $content, $matches)){
            $title = trim($matches[1]);
        }

        return $title;
    }
}