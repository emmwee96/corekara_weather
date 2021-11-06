<?php namespace App\Controllers;

use App\Core\BaseController;

class Main extends BaseController
{
    public function __construct()
    {
        $this->pageData = array();
    }

    public function index()
    {
		// // get wiki title from id
        // $url = "https://www.wikidata.org/w/api.php?action=wbgetentities&format=json&props=sitelinks&ids=Q7813925";
        
		// $result = $this->conn($url);
		// $result = json_decode($result, true);
		// $title = $result['entities']['Q7813925']['sitelinks']['jawiki']['title'];
		// $title = urlencode($title);

		// // get wiki description from title
		// $url = 'https://ja.wikipedia.org/w/api.php?action=query&prop=extracts|info|pageimages&exintro&titles=' . $title . '&format=json&explaintext&redirects&inprop=url&indexpageids&pithumbsize=100';
		// $result = $this->conn($url);
		// $result = json_decode($result, true);
		// $page_id = $result['query']['pageids'][0];
		// $description = trim($result['query']['pages'][$page_id]['extract']);
		// $image = $result['query']['pages'][$page_id]['thumbnail']['source'];

		// $url = "https://www.wikidata.org/w/api.php?action=wbgetentities&format=json&props=sitelinks&ids=Q11495784";
		// $url = 'http://ja.wikipedia.org/w/api.php?action=query&titles=%E6%88%90%E8%A6%9A%E5%AF%BA&prop=pageimages&format=json&pithumbsize=100';
        // $url = 'https://ja.wikipedia.org/w/api.php?action=query&prop=extracts|info&exintro&titles=%E6%88%90%E8%A6%9A%E5%AF%BA&format=json&explaintext&redirects&inprop=url&indexpageids';

        // $url = "https://newsapi.org/v2/top-headlines?country=jp&apiKey=928c652396c14f0294eefcb7b07c23ce";
        // $result = $this->conn($url);
        // $result = json_decode($result, true);
        // $articles = $result['articles'];

        // // $this->debug($articles);
        // $this->page_data['articles'] = $articles;

        echo view('main/main', $this->pageData);
    }

    //--------------------------------------------------------------------

}
