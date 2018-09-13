<?php

namespace App\Http\Controllers\Sentiment;

use App\Http\Sentiment\Analyzer;
use App\Http\Sentiment\Sentiment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SentimentController extends Controller
{

    public function analyze(Request $request)
    {
        $sentiment = new Analyzer(new Sentiment());

        $sentiment->train(asset('sentimentData/pos.txt'), 'pos', 5000);
        $sentiment->train(asset('sentimentData/neg.txt'), 'neg', 5000);

        $limit = $request->limit;

        $accurate = 0;
        $pos = 0;
        $neg = 0;
        $neu = 0;
        $file = fopen(asset('sentimentData/pos.test.txt'), 'r');
        while($data = fgets($file)) {
            if($limit == 0) {
                break;
            } else {
                $score = $sentiment->score($data);
                //echo $data. "<br>";
                if($score['category'] == 'pos') {
                    $pos++;
                    $accurate++;
                } elseif($score['category'] == 'neg') {
                    $neg++;
                } else {
                    $neu++;
                }
                $limit--;
            }
        }

        echo "<u>Positive Test Data:</u><br>No of Samples: $request->limit<br>";
        echo "Positive: $pos<br>";
        echo "Negative: $neg<br>";
        echo "Neutral: $neu<br>";
        echo "<b>Accuracy: ". ($pos/$request->limit)*100 ."%</b>";
        fclose($file);

        $limit = $request->limit;
        $pos = 0;
        $neg = 0;
        $neu = 0;
        $file = fopen(asset('sentimentData/neg.test.txt'), 'r');
        while($data = fgets($file)) {
            if($limit == 0) {
                break;
            } else {
                $score = $sentiment->score($data);
                //echo $data. "<br>";
                if($score['category'] == 'pos') {
                    $pos++;
                } elseif($score['category'] == 'neg') {
                    $neg++;
                    $accurate++;
                } else {
                    $neu++;
                }
                $limit--;
            }
        }


        echo "<br><br><u>Negative Test Data:</u><br>No of Samples: $request->limit<br>";
        echo "Positive: $pos<br>";
        echo "Negative: $neg<br>";
        echo "Neutral: $neu<br>";
        echo "<b>Accuracy: ". ($neg/$request->limit)*100 ."%</b>";

        echo "<br><h3>Overall accuracy: ". ($accurate/($request->limit * 2)) * 100 . "%</h3>";
        fclose($file);
        //dd(true);

        //$score = $sentiment->score($request->text);
        //echo $score['category']. "<br>Pos: ". $score['score']['positivity']. ", Neg:". $score['score']['negativity'];
    }

}
