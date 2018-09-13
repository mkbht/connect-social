<?php

namespace App\Http\Sentiment;

class Sentiment
{

    protected $categories = [],
        $types = ['pos', 'neg'],
        $wordType = ['pos' => 0, 'neg' => 0],
        $sentenceType = ['pos' => 0, 'neg' => 0],
        $wordCount = 0,
        $sentenceCount = 0,
        $distribution = ['pos' => 0.5, 'neg' => 0.5],
        $difference;

    /**
     * SentimentController constructor.
     */
    public function __construct()
    {
        $this->difference = range(-1.0, 1.5, 0.1);
    }

    /**
     * @param $sentence
     * @return mixed
     */
    private function breakSentenceToWords($sentence)
    {
        preg_match_all('/\w+/', $sentence, $result);
        return $result[0];
    }

    /**
     * @param $filePath
     * @param $type
     * @param int $amount
     * @return bool
     * @throws \Exception
     */
    public function insertTrainingData($filePath, $type, $amount = 0)
    {
        if (!in_array($type, $this->types)) {
            throw new \Exception("Invalid category type");
        }

        $trackAmount = 0;
        $trainingData = fopen($filePath, "r");

        while ($data = fgets($trainingData)) {
            if ($trackAmount > $amount && $amount > 0) {
                break;
            } else {
                $trackAmount++;
                $this->sentenceCount++;
                $this->sentenceType[$type]++;
                $words = self::breakSentenceToWords($data);

                foreach ($words as $word) {
                    $word = strtolower($word);
                    $this->wordType[$type]++;
                    $this->wordCount++;
                    !isset($this->categories[$word][$type])
                        ? $this->categories[$word][$type] = 0
                        : $this->categories[$word][$type]++;
                }
            }
        }
        return true;
    }

    /**
     * @param $input
     * @return array
     */
    public function score($input)
    {
        foreach ($this->types as $type) {
            $this->distribution[$type] =
                $this->sentenceType[$type] / $this->sentenceCount;
        }

        // chunk input sentences to individual words
        $words = self::breakSentenceToWords($input);
        foreach ($this->types as $type) {
            $categoryScores[$type] = 1;
            foreach ($words as $word) {
                $word = strtolower($word);
                if (!isset($this->categories[$word][$type])) {
                    $tracker = 0;
                } else {
                    $tracker = $this->categories[$word][$type];
                }
                $categoryScores[$type] += log(($tracker + 1) / ($this->wordType[$type] + $this->wordCount));
            }
            $categoryScores[$type] += log($this->distribution[$type]);
            $scoreConverted[$type] = exp($categoryScores[$type]);
        }
        arsort($categoryScores);
        arsort($scoreConverted);

        //dd($scoreConverted);
        //dd($categoryScores);

        if ($scoreConverted['pos'] == 0 || $scoreConverted['neg'] == 0) {

            $log = true;
            // calculate difference
            if (key($categoryScores) == 'pos') {
                $bayesDifference = $categoryScores['pos'] - $categoryScores['neg'];
            } else {
                $bayesDifference = $categoryScores['neg'] - $categoryScores['pos'];
            }

            $positivity = $categoryScores['pos'];
            $negativity = $categoryScores['neg'];

            if (abs($bayesDifference) <= 1) {
                $category = 'neu';
            } else {
                $category = key($categoryScores);
            }
        } else {
            $log = false;

            if (key($scoreConverted) == 'pos') {
                $bayesDifference = $scoreConverted['pos'] / $scoreConverted['neg'];
            } else {
                $bayesDifference = $scoreConverted['neg'] / $scoreConverted['pos'];
            }

            $positivity = ($scoreConverted['pos'] / ($scoreConverted['pos'] + $scoreConverted['neg']));
            $negativity = ($scoreConverted['neg'] / ($scoreConverted['pos'] + $scoreConverted['neg']));

            if (in_array(round($bayesDifference, 1), $this->difference)) {
                $category = 'neu';
            } else {
                $category = key($scoreConverted);
            }
        }


        //dd($scoreConverted);

//        dd($bayesDifference);


        //dd($positivity. ' '. $negativity);

        return [
            'category' => $category,
            'log' => $log,
            'score' => [
                'positivity' => $positivity,
                'negativity' => $negativity
            ]
        ];
    }

}
