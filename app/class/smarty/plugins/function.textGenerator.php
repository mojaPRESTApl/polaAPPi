<?php
class LangGen {
        protected $model = array();
        protected $rootScores = array();
        protected $sentenceEnd = array('.', '!', '?');
        protected $joinSentence = array(',', ':', ';');
		public $text;
        public function learn($filePath=null) {
                $contents = strip_tags($this->text);
                $tokens = $this->tokenise($contents);
                unset($contents);
                
                $prevToken = null;
                foreach($tokens as $token) {
                        if($prevToken) {
                                if(!isset($this->model[$prevToken])) {
                                        $this->model[$prevToken] = array();
                                }
                                if(!isset($this->model[$prevToken][$token])) {
                                        $this->model[$prevToken][$token] = 0;
                                }
                                $this->model[$prevToken][$token]++;
                        }
                        $prevToken = $token;
                        
                        // handle sentence enders
                        if(in_array($token, $this->sentenceEnd)) {
                                $prevToken = null;
                        } else {
                                if(!isset($this->rootScores[$token])) {
                                        $this->rootScores[$token] = 0;
                                }
                                $this->rootScores[$token]++;
                        }
                }
                unset($tokens);
                
                // normalise probabilities
                foreach($this->model as $key => $tokens) {
                        $this->model[$key] = $this->probNormalise($tokens);
                }
                $this->rootScores = $this->probNormalise($this->rootScores);
        }
        
        public function generate($length = 15) {
                $word = null;   
                for($i = 0; $i < $length; $i++) {
                        if(is_array($this->model[$word])) {
                                do {
                                        $return[$i] = $this->pick($this->model[$word]);
                                } while($word == $return[$i]);
                                $word = $return[$i];
                        } else {
                                $return[$i] = $word = $this->pick($this->rootScores);
                        }
                }
                return $this->generateString($return);
        }       
        
        protected function generateString(array $words) {
                $words[0] = ucwords($words[0]);
                foreach($words as $key => $word) {
                        if(in_array($word, $this->sentenceEnd)) {
                                $words[$key-1] .= $word;
                                unset($words[$key]);
                                $words[$key+1] = ucwords($words[$key+1]);
                        } else if(in_array($word, $this->joinSentence)) {
                                if(strlen($words[$key-1])) {
                                        $words[$key-1] .= $word;
                                }
                                unset($words[$key]);
                        }
                }
                return implode(' ', $words);
        }
        
        protected function probNormalise($array) {
                $total = array_sum($array);
                $runningScore = 0;
                foreach($array as $key => $score) {
                        $runningScore += ($score/$total);
                        $array[$key] = $runningScore; 
                }
                return $array;
        }

        protected function pick($array) {
                $floatRand = rand(0, 1000000) / 1000000.0;
                foreach($array as $key => $value) {
                        if($floatRand < $value) {
                                return $key;
                        }
                }
        }

        protected function tokenise($string) {
                preg_match_all("/[\'|\w]+|[\:|\;|\.|\?|\!|\,]/", $string, $matches); 
                foreach($matches[0] as $id => $match) {
                        if(is_numeric($match)) {
                                unset($matches[0][$id]);
                        } else {
                                $matches[0][$id] = strtolower($match);
                        }
                }
                return $matches[0]; 
        }
}
function smarty_function_textGenerator($p, $template){
	$text1='Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Dla jednych byli nieustraszonymi bohaterami, dla innych bezwzględnymi zabójcami. Ofiary zwykle nie miały szansy ich zobaczyć, ponieważ potrafili się znakomicie maskować i oddać śmiertelnie celny strzał z odległości nawet pół kilometra. Najskuteczniejsi zabili kilkuset żołnierzy wroga. Przedstawiamy słynnych snajperów II wojny światowej. Simo Hayha nie miał zadatków na legendę wojskowości. Syn farmera z małej, fińskiej wioski w Karelii był skromnym człowiekiem bez większych ambicji. Kochał las, tropienie zwierzyny oraz polowanie. I świetnie strzelał, co potwierdzał na licznych zawodach. ';
	$langGen = new LangGen();
	$langGen->text = $text1;
	$langGen->learn();
	return $langGen->generate($p['ile']);
}

?>