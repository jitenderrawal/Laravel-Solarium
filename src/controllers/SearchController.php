<?php namespace Fbf\LaravelSolarium;

class SearchController extends \BaseController {

	public function results()
	{
		$results = $paginator = false;

	    if ( \Input::has('term') )
        {
            $solr = new LaravelSolariumQuery(config('laravel-solarium.default_core'));

            $searchInput = \Input::get('term');

            $searchArray = explode(' ', $searchInput);

            $searchTermsArray = array();

            foreach ( $searchArray as $term )
            {
                $searchTermArray[] = 'search_content:"'.trim($term).'"';
            }

            $searchTerm = implode(' OR ', $searchTermArray);

            $searchTerm .= ' AND status:"APPROVED"';

            $resultsPerPage = config('laravel-solarium.results.items_per_page');

            $results = $solr->search($searchTerm)
                ->fields(array('id', 'title', 'content', 'url'))
                ->page(\Input::get('page', 1), $resultsPerPage)
                ->highlight(array('content'))
                ->get();

            $highlighting = $results->getHighlighting();

            $paginator = \Paginator::make(
                $results->getDocuments(),
                $results->getNumFound(),
                $resultsPerPage
            );
        }

		$viewFile = config('laravel-solarium.results.view');

        return \View::make($viewFile)->with(compact('results', 'paginator', 'highlighting'));
	}
}