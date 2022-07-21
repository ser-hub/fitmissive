<?php
namespace Application\Utilities;

class Constants
{
    const USER_ROLE_USER = 'User';
    const USER_ROLE_ADMIN = 'Admin';
    const ALLOWED_IMAGE_TYPES = ['jpg', 'jpeg', 'png'];
    const DEFAULT_IMAGE = '/img/profiles/default.png';
    const IMAGE_PATH = '/img/profiles/uploads/';
    const PAGINATION_SEARCH_RESULTS_PER_PAGE = 8;
    const PAGINATION_PAGES_TO_SHOW = 3;
    const FOLLOWS_MAX = 300;
    const INFO_MAX = 10;
}