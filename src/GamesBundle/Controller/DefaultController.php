<?php

namespace GamesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $hands = $this->trie();
        return $this->render('games/index.html.twig', ['hands'=> $hands]);
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

                /* Tri a bulle */
                foreach ($json->data->categoryOrder as $categoryOrder)
                {
                    foreach ($json->data->valueOrder as $valueOrder)
                    {
                        foreach ($json->data->cards as $cards)
                        {
                            if($cards->category == $categoryOrder && $cards->value == $valueOrder)
                            {
                                $tabcard['categoryOrder'] = $cards->category;
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
