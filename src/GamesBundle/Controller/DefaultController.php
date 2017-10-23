<?php

namespace GamesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    protected $idExo;
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $hands = $this->trie();
        return $this->render('games/index.html.twig', ['hands'=> $hands]);
    }

    public function valideExoAction()
    {
        try{


            $reponse = false;
            $hands["cards"] = $this->trie();

            $hands['categoryOrder'] = ["DIAMOND", "HEART", "SPADE", "CLUB"];
            $hands['valueOrder'] = ["ACE", "TWO", "THREE", "FOUR", "FIVE", "SIX", "SEVEN", "EIGHT", "NINE", "TEN", "JACK", "QUEEN","KING"];

            $json = json_encode($hands);

            //$hands
            //Appel du webService
            $urlService = $this->container->getParameter('cards')['verification'].$this->idExo;

            $client = new \GuzzleHttp\Client();
            $res = $client->post($urlService, ['json' => $hands]);
            if($res->getStatusCode() == 200)
            {
                $reponse = true;

            }
        } catch (\Exception $ex) {

            error_log($ex->getTraceAsString());
        }
        return $this->render('games/verification.html.twig', ['reponse'=> $reponse]);
    }

    /**
     * method private qui retourne la mains trié
     * @param string $order
     */
    private function trie($order="asc")
    {
        $hands = array();
        try {
            //Appel du webService
            $urlService = $this->container->getParameter('cards')['hands'];
            $client = new \GuzzleHttp\Client();
            $res = $client->request('GET', $urlService);

            if($res->getStatusCode() == 200)
            {
                $data = $res->getBody()->__toString();
                $json = json_decode($data);

                $this->idExo = $json->exerciceId;
                /* Tri a bulle */
                foreach ($json->data->categoryOrder as $categoryOrder)
                {
                    foreach ($json->data->valueOrder as $valueOrder)
                    {
                        foreach ($json->data->cards as $cards)
                        {
                            if($cards->category == $categoryOrder && $cards->value == $valueOrder)
                            {
                                $tabcard['category'] = $cards->category;
                                $tabcard['value'] = $cards->value;
                                $hands[] = $tabcard;
                            }
                        }
                    }
                }

            }

        } catch (\Exception $ex) {
            error_log($ex->getTraceAsString());
        }

        return $hands;
    }
}
