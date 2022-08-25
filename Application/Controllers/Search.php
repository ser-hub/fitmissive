<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Utilities\{Input, Redirect, Session, Constants};

class Search extends Controller
{
    public function index()
    {
        $lastSearch = Session::flash('last_search');

        if (Input::keyExists('search')) {
            $keyword = Input::get('search');
        }
        else if ($lastSearch != null) {
            $keyword = $lastSearch;
        }
        else {
            Redirect::to('/home');
        }

        if (strlen($keyword)) {
            Session::flash('last_search', $keyword);

            $page = 1;
            if (Input::keyExists('page') && Input::get('page') > 0) {
                $page = Input::get('page');
            }

            $searchResults = $this->userService->searchUsers(
                $keyword,
                ($page - 1) * Constants::PAGINATION_SEARCH_RESULTS_PER_PAGE,
                Constants::PAGINATION_SEARCH_RESULTS_PER_PAGE
            );

            $profilePictures = [];

            if ($searchResults) {
                foreach($searchResults['users'] as $user) {
                    $profilePictures[$user->user_id] = $this->userService->getPicturePathOf($user->user_id);
                }
            }

            $this->view('search', array(
                'keyword' => $keyword,
                'searchResults' => $searchResults,
                'profilePictures' => $profilePictures,
                'follows' => $this->userService->getFollowsArrayOf($this->loggedUser),
            ));
        }
        else {
            Redirect::to('/home');
        }
    }

    public function follow()
    {
        if (Input::exists()) {
            if (Input::get('action') == 'Follow') {
                $this->userService->follow($this->loggedUser, Input::get('followed'));
            }
            else if (Input::get('action') == 'Unfollow') {
                $this->userService->unfollow($this->loggedUser, Input::get('followed'));
            }
        }
        $this->index();
    }
}
