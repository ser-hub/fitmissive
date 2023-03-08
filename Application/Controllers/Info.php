<?php

namespace Application\Controllers;

use Application\Core\Controller;
use Application\Services\{InfoService, UserService};
use Application\Utilities\{Constants, Input, Token, Validator, Redirect};

class Info extends Controller
{
    private $infoService;
    private $adminMode = false;
    private $data = [
        'errors' => null,
        'inputs' => null
    ];

    public function __construct()
    {
        $this->infoService = InfoService::getInstance();
        $this->userService = UserService::getInstance();

        if ($this->userService->isUserLoggedIn()) {
            if ($this->userService->getLoggedUser()->role_name === Constants::USER_ROLE_ADMIN) {
                $this->adminMode = true;
            }
        }
    }

    public function index($slug = null)
    {
        $data = [
            'title' => null,
            'content' => null
        ];
        if ($slug != null) {
            $info = $this->infoService->getInfoBySlug($slug);
            if ($info) {
                $data['title'] = $info->title;
                $data['content'] = $info->content;
            }
        }

        $this->view('info', [
            'title' => $data['title'],
            'content' => $data['content'],
            'adminMode' => $this->adminMode,
            'errors' => $this->data['errors'],
            'inputs' => $this->data['inputs']
        ]);
    }

    public function update($slug = null)
    {
        if ($this->adminMode && Input::exists()) {
            if (Token::check(Input::get("token"), "session/info_update_token")) {
                $title = Input::get('title');
                $content = Input::get('content');
                if (strlen($content) < 4000) {
                    $this->infoService->updateInfo($title, array(
                        'content' => $content,
                        'updated_at' => date('Y-m-d H:i:s')
                    ));
                } else {
                    $this->data['inputs'] = array(
                        'content' => $content
                    );
                    $this->data['errors'] = array('Content is too long.');
                    Input::put('action', 'edit');
                }
            }
        }
        $this->index($slug);
    }

    public function delete($id = null)
    {
        if ($this->adminMode && $id != null) {
            if (Token::check(Input::get("token"), "session/info_delete_token")) {
                $this->infoService->deleteInfo($id);
            }
        }
        $this->index();
    }

    public function create()
    {
        $slug = null;
        if ($this->adminMode && Input::exists()) {
            if (Token::check(Input::get("token"), "session/info_create_token")) {
                $title = Input::get('title');
                $slug = Input::get('slug');
                $content = Input::get('content');

                $validator = new Validator();
                $validator->check($_POST, array(
                    'title' => array(
                        'name' => 'Title',
                        '!contains' => '\\/?#@*=;\'"',
                        'required' => true,
                        'max' => 45
                    ),
                    'slug' => array(
                        'name' => 'Slug',
                        'required' => true,
                        'max' => 45,
                        '!contains' => ' \\/?%&#@!*()+=,.;:\'"',
                        'unique' => 'info',
                        'dbColumn' => 'slug'
                    ),
                    'content' => array(
                        'name' => 'Content',
                        'required' => true,
                        'max' => 4000
                    )
                ));

                if ($validator->passed()) {
                    $this->infoService->addInfo(array(
                        'title' => $title,
                        'slug' => $slug,
                        'content' => $content
                    ));
                    Redirect::to('/info/' . $slug);
                } else {
                    $this->data['inputs'] = array(
                        'title' => $title,
                        'slug' => $slug,
                        'content' => $content
                    );
                    $this->data['errors'] = $validator->errors();
                }
            }
        }
        $this->index($slug);
    }
}
