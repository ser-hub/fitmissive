<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\UserService;
use Application\Utilities\{Input, Redirect, Session};

class Search extends Controller
{
    private $loggedUser = null;
    private $userService;

    public function __construct()
    {
        $this->userService = UserService::getInstance();

        if (!$this->userService->isUserLoggedIn()) {
            Redirect::to('/index');
        }

        $this->loggedUser = $this->userService->getLoggedUser()->user_id;
    }

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

            $this->view('search', array(
                'loggedUser' => $this->loggedUser,
                'keyword' => $keyword,
                'searchResults' => $this->userService->searchUsers($this->loggedUser, $keyword),
                'follows' => $this->userService->getFollowsArrayOf($this->loggedUser),
            ));
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
