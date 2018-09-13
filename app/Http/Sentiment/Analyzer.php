<?php

namespace App\Http\Sentiment;


class Analyzer
{

    protected $analyzer;

    public function __construct(Sentiment $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function train($filePath, $type, $amount=0)
    {
        try {
            return $this->analyzer->insertTrainingData($filePath, $type, $amount);
        } catch (\Exception $e) {
        }
    }

    public function score($input)
    {
        return $this->analyzer->score($input);
    }
}